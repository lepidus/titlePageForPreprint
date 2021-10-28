<?php

require __DIR__ . '/../vendor/autoload.php';
import ('plugins.generic.titlePageForPreprint.classes.Pdf');

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
        
        $titlePage->Write(0, $this->translator->translate('plugins.generic.titlePageForPreprint.submissionDate', $this->locale, ['subDate' => $this->submission->getSubmissionDate()]), '', 0, 'JUSTIFY', true, 0, false, false, 0);
        $titlePage->Write(0, $this->translator->translate('plugins.generic.titlePageForPreprint.publicationDate', $this->locale, ['postDate' => $this->submission->getPublicationDate(), 'version' => $this->submission->getVersion()]), '', 0, 'JUSTIFY', true, 0, false, false, 0);
        $titlePage->Write(0, $this->translator->translate('plugins.generic.titlePageForPreprint.dateFormat', $this->locale), '', 0, 'JUSTIFY', true, 0, false, false, 0);
      
        $TitlePageFile = self::OUTPUT_DIRECTORY . 'titlePage.pdf';
        $titlePage->Output($TitlePageFile, 'F');
        return $TitlePageFile;
    }

    private function commandSuccessful(int $resultCode): bool{
        if ($resultCode != 0) {
            return false;
        }
        return true;
    }

    private function concatenateTitlePage(string $TitlePageFile, pdf $pdf): void {
        $originalFileCopy = self::OUTPUT_DIRECTORY . "original_file_copy.pdf";
        copy($pdf->getPath(), $originalFileCopy);
        $modifiedFile = self::OUTPUT_DIRECTORY . "withTitlePage.pdf";

        $uniteCommand = 'pdftk '.  $TitlePageFile . ' '. $originalFileCopy . ' cat output ' . $modifiedFile;

        exec($uniteCommand, $output, $resultCode);

        if (!$this->commandSuccessful($resultCode)) {
            throw new Exception('Final Union Failure');
        }

        rename($modifiedFile, $pdf->getPath());
        $this->removeTemporaryFiles($TitlePageFile);
        $this->removeTemporaryFiles($originalFileCopy);
    }
    
    private function removeTemporaryFiles($file) {
        unlink($file);
    }

    public function insert(pdf $pdf): void {
        $TitlePageFile = $this->generateTitlePage();
        try {
            $this->concatenateTitlePage($TitlePageFile, $pdf);
        } catch (Exception $e) {
            error_log('Caught exception: ' .  $e->getMessage());
        }
    }

    private function separatePages(pdf $pdf) {
        $separateCommand = "pdftk {$pdf->getPath()} burst output %d.pdf";
        exec($separateCommand, $output, $resultCode);
        if (!$this->commandSuccessful($resultCode)) {
            throw new Exception('Separation Failure ');
        }
    }

    private function unitePages(pdf $pdf, $initialPage) {
        $uniteCommand = 'pdftk ';
        $modifiedFile = self::OUTPUT_DIRECTORY . "withoutTitlePage.pdf";
        $pages = $pdf->getNumberOfPages();

        for ($i = $initialPage; $i <= $pages; $i++){
            $uniteCommand .= ($i .'.pdf ');
        }

        $uniteCommand .= 'cat output ' .$modifiedFile;
        exec($uniteCommand, $output, $resultCode);

        if (!$this->commandSuccessful($resultCode)) {
            throw new Exception('Union Failure');
        }

        rename($modifiedFile, $pdf->getPath());

        for ($i = $initialPage; $i <= $pages; $i++){
            $this->removeTemporaryFiles( $i .'.pdf');
        }
    }

    public function remove(pdf $pdf): void {
        $modifiedFile = self::OUTPUT_DIRECTORY . "withoutTitlePage.pdf";
        $separateCommand = "pdftk {$pdf->getPath()} cat 2-end output {$modifiedFile}";
        exec($separateCommand, $output, $resultCode);

        if (!$this->commandSuccessful($resultCode)) {
            throw new Exception('Title Page Remove Failure');
        }

        rename($modifiedFile, $pdf->getPath());
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
        try {
            $this->separatePages($pdf);
        } catch (Exception $e) {
            error_log('Caught exception: ' .  $e->getMessage());
        }

        $pages = $pdf->getNumberOfPages();
        for($i = 1; $i <= $pages; $i++) {
            $this->addPageHeader("{$i}.pdf");
        }

        try {
            $this->unitePages($pdf, 1);
        } catch (Exception $e) {
            error_log('Caught exception: ' .  $e->getMessage());
        }
    }

    private function generateChecklistPage(): string {
        $checklistPage = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        $checklistPage->setPrintHeader(false);
        $checklistPage->setPrintFooter(false);
        $checklistPage->AddPage();

        $checklistPage->Write(0, $this->translator->translate('plugins.generic.titlePageForPreprint.checklistLabel', $this->locale) . ": ", '', 0, 'JUSTIFY', true, 0, false, false, 0);
        $checklistPage->SetFont($this->fontName, '', 10, '', false);
        $checklistPage->Ln(5);

        $checklistText = '';
        foreach ($this->translator->getTranslatedChecklist($this->locale) as $item) {
            $checklistText = $checklistText. "<ul style=\"text-align:justify;\"><li>". $item . "</li></ul>";
        }
        $checklistPage->writeHTMLCell(0, 0, '', '',$checklistText, 1, 1, false, true, 'JUSTIFY', false);
        $checklistPage->SetFont($this->fontName, '', 11, '', false);
        $checklistPage->Ln(5);

        $checklistPageFile = self::OUTPUT_DIRECTORY . 'checklistPage.pdf';
        $checklistPage->Output($checklistPageFile, 'F');
        return $checklistPageFile;
    }

    private function concatenateChecklistPage(string $checklistPageFile, pdf $pdf): void {
        $originalFileCopy = self::OUTPUT_DIRECTORY . "original_file_copy.pdf";
        copy($pdf->getPath(), $originalFileCopy);
        $modifiedFile = self::OUTPUT_DIRECTORY . "withChecklistPage.pdf";
        $uniteCommand = 'pdftk '.  $originalFileCopy . ' '. $checklistPageFile . ' cat output ' . $modifiedFile;
        exec($uniteCommand, $output, $resultCode);

        if (!$this->commandSuccessful($resultCode)) {
            throw new Exception('Checklist Page Concatenation Failure');
        }

        rename($modifiedFile, $pdf->getPath());
        $this->removeTemporaryFiles($checklistPageFile);
        $this->removeTemporaryFiles($originalFileCopy);
    }

    public function addChecklistPage(pdf $pdf) {
        $checklistPageFile = $this->generateChecklistPage();

        try {
            $this->concatenateChecklistPage($checklistPageFile, $pdf);
        } catch (Exception $e) {
            error_log('Caught exception: ' .  $e->getMessage());
        }
        
    }

}
?>