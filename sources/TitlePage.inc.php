<?php

require __DIR__ . '/../vendor/autoload.php';

class TitlePage { 

    private $submission;
    private $logo;
    private $locale;
    private $translator;
    private $fontName;
    const OUTPUT_DIRECTORY = DIRECTORY_SEPARATOR . "tmp" .  DIRECTORY_SEPARATOR;

    public function __construct(SubmissionModel $submission, string $logo, string $locale, translator $translator) {
        $this->submission = $submission;
        $this->logo = $logo;
        $this->locale = $locale;
        $this->translator = $translator;
        $this->fontName = TCPDF_FONTS::addTTFfont(__DIR__.'/../resources/opensans.ttf', 'TrueTypeUnicode', '', 32);
    }

    public function getLogoType(): string {
        $fileType = pathinfo($this->logo, PATHINFO_EXTENSION);
        return strtoupper($fileType);
    }
    
    private function generateTitlePage(): string {
        $titlePage = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        $titlePage->setPrintHeader(false);
        $titlePage->setPrintFooter(false);
        $titlePage->AddPage();
        $titlePage->Image($this->logo, '', '', '', '20', $this->getLogoType(), 'false', 'C', false, 400, 'C', false, false, 0, false, false, false);
        $titlePage->Ln(25);
        
        $titlePage->SetFont($this->fontName, '', 10, '', false);
        $titlePage->Write(0, $this->translator->translate('common.status', $this->locale) . ": " . $this->translator->translate($this->submission->getStatus(), $this->locale), '', 0, 'JUSTIFY', true, 0, false, false, 0);
        if($this->submission->getStatus() == 'publication.relation.published'){
            $titlePage->Write(0, $this->translator->translate('publication.relation.vorDoi', $this->locale) . ": ", '', 0, 'JUSTIFY', false, 0, false, false, 0);
            $titlePage->write(0, $this->submission->getJournalDOI(), $this->submission->getJournalDOI(), 0, 'JUSTIFY', true, 0, false, false, 0);
        }
        $titlePage->Ln(5);
        
        $titlePage->SetFont($this->fontName, '', 18, '', false);
        $titlePage->Write(0, $this->translator->getTranslatedTitle($this->locale), '', 0, 'C', true, 0, false, false, 0);
        $titlePage->SetFont($this->fontName, '', 12, '', false);
        $titlePage->Write(0, $this->submission->getAuthors(), '', 0, 'C', true, 0, false, false, 0);
        $titlePage->SetFont($this->fontName, '', 11, '', false);
        $titlePage->Ln(5);
        $titlePage->Write(0, "https://doi.org/" . $this->submission->getDOI(), "https://doi.org/" . $this->submission->getDOI(), 0, 'C', true, 0, false, false, 0);
        $titlePage->Ln(10);
        $titlePage->Write(0, $this->translator->translate('plugins.generic.titlePageForPreprint.checklistLabel', $this->locale) . ": ", '', 0, 'JUSTIFY', true, 0, false, false, 0);
        $titlePage->SetFont($this->fontName, '', 10, '', false);
        $titlePage->Ln(5);

        $checklistText = '';
        foreach ($this->translator->getTranslatedChecklist($this->locale) as $item) {
            $checklistText = $checklistText. "<ul style=\"text-align:justify;\"><li>". $item . "</li></ul>";
        }
        $titlePage->writeHTMLCell(0, 0, '', '',$checklistText, 1, 1, false, true, 'JUSTIFY', false);
        $titlePage->SetFont($this->fontName, '', 11, '', false);
        $titlePage->Ln(5);
        $titlePage->Write(0, $this->translator->translate('plugins.generic.titlePageForPreprint.submissionDate', $this->locale) . ": " . $this->translator->getTranslatedDate($this->submission->getSubmissionDate(), $this->locale), '', 0, 'JUSTIFY', true, 0, false, false, 0);
        $titlePage->Write(0, $this->translator->translate('plugins.generic.titlePageForPreprint.publicationDate', $this->locale) . ": " . $this->translator->getTranslatedDate($this->submission->getPublicationDate(), $this->locale), '', 0, 'JUSTIFY', true, 0, false, false, 0);
      
        $TitlePageFile = self::OUTPUT_DIRECTORY . 'titlePage.pdf';
        $titlePage->Output($TitlePageFile, 'F');
        return $TitlePageFile;
    }

    private function concatenateTitlePage(string $TitlePageFile, pdf $pdf): void {
        $originalFileCopy = self::OUTPUT_DIRECTORY . "original_file_copy.pdf";
        copy($pdf->getPath(), $originalFileCopy);
        $modifiedFile = self::OUTPUT_DIRECTORY . "withTitlePage.pdf";
        $uniteCommand = 'pdfunite '.  $TitlePageFile . ' '. $originalFileCopy . ' ' . $modifiedFile;
        shell_exec($uniteCommand);
        rename($modifiedFile, $pdf->getPath());
        $this->removeTemporaryFiles($TitlePageFile);
        $this->removeTemporaryFiles($originalFileCopy);
    }
    
    private function removeTemporaryFiles($file) {
        unlink($file);
    }

    public function insert(pdf $pdf): void {
        $TitlePageFile = $this->generateTitlePage();
        $this->concatenateTitlePage($TitlePageFile, $pdf);
    }

    private function separatePages(pdf $pdf, $initialPage) {
        $separateCommand = "pdfseparate -f {$initialPage} {$pdf->getPath()} %d.pdf";
        shell_exec($separateCommand);
    }

    private function unitePages(pdf $pdf, $initialPage) {
        $uniteCommand = 'pdfunite ';
        $modifiedFile = self::OUTPUT_DIRECTORY . "withoutTitlePage.pdf";
        $pages = $pdf->getNumberOfPages();

        for ($i = $initialPage; $i <= $pages; $i++){
            $uniteCommand .= ($i .'.pdf ');
        }

        $uniteCommand .= $modifiedFile;
        shell_exec($uniteCommand);
        rename($modifiedFile, $pdf->getPath());

        for ($i = $initialPage; $i <= $pages; $i++){
            $this->removeTemporaryFiles( $i .'.pdf');
        }
    }

    public function remove(pdf $pdf): void {
        $this->separatePages($pdf, 2);
        $this->unitePages($pdf, 2);
    }

    private function addPageHeader($pagePath) {
        $pdf = new Pdf($pagePath);
        $pageOrientation = $pdf->getPageOrientation();
        
        $pdf = new TCPDI($pageOrientation, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        $pdf->SetPrintHeader(false);
        $pdf->AddPage();
        $pdf->setSourceFile($pagePath);
        $tplIdx = $pdf->importPage(1);
        $pdf->useTemplate($tplIdx);

        $linkDOI = "https://doi.org/".$this->submission->getDOI();
        $pdf->SetY(1);
        $pdf->SetFont($this->fontName, '', 8);
        $pdf->Write(0, $this->translator->translate('plugins.generic.titlePageForPreprint.headerText', $this->locale, ['doiPreprint' => $linkDOI]), $linkDOI, 0, 'C', true, 0, false, false, 0);

        $outputPath = self::OUTPUT_DIRECTORY . "pageHeader";
        $pdf->Output($outputPath, "F");
        rename($outputPath, $pagePath);
    }

    public function addDocumentHeader(pdf $pdf): void {
        $this->separatePages($pdf, 1);

        $pages = $pdf->getNumberOfPages();
        for($i = 1; $i <= $pages; $i++) {
            $this->addPageHeader("{$i}.pdf");
        }

        $this->unitePages($pdf, 1);
    }

}
?>