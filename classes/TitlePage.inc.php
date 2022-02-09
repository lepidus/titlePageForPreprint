<?php

require dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR.'vendor'.DIRECTORY_SEPARATOR.'autoload.php';
require_once dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR.'vendor'.DIRECTORY_SEPARATOR.'tecnickcom'.DIRECTORY_SEPARATOR.'tcpdf'.DIRECTORY_SEPARATOR.'tcpdf.php';
import ('plugins.generic.titlePageForPreprint.classes.Pdf');

class TitlePage { 

    private $submission;
    private $logo;
    private $locale;
    private $translator;
    private $fontName;
    const OUTPUT_DIRECTORY = DIRECTORY_SEPARATOR . "tmp" .  DIRECTORY_SEPARATOR;
    const CPDF_PATH = __DIR__ . "/../tools/cpdf";

    public function __construct(SubmissionModel $submission, string $logo, string $locale, translator $translator) {
        $this->submission = $submission;
        $this->logo = $logo;
        $this->locale = $locale;
        $this->translator = $translator;
        $this->fontName = TCPDF_FONTS::addTTFfont(__DIR__.'/../resources/opensans.ttf', 'TrueTypeUnicode', '', 32);
    }

    private function commandSuccessful(int $resultCode): bool{
        if ($resultCode != 0) {
            return false;
        }
        return true;
    }

    public function removeTitlePage($pdf): void {
        $separateCommand = self::CPDF_PATH . " {$pdf} 2-end -o {$pdf}";
        exec($separateCommand, $output, $resultCode);

        if (!$this->commandSuccessful($resultCode)) {
            throw new Exception('Title Page Remove Failure');
        }
    }

    public function getLogoType(): string {
        $fileType = pathinfo($this->logo, PATHINFO_EXTENSION);
        return strtoupper($fileType);
    }

    private function writePublicationStatusOnTitlePage($titlePage) {
        $titlePage->SetFont($this->fontName, '', 10, '', false);
        
        if(!empty($this->submission->getStatus())){
            $titlePage->Write(0, $this->translator->translate('plugins.generic.titlePageForPreprint.publicationStatus', $this->locale) . ": " . $this->translator->translate($this->submission->getStatus(), $this->locale), '', 0, 'JUSTIFY', true, 0, false, false, 0);
            
            if($this->submission->getStatus() == 'publication.relation.published'){
                $titlePage->Write(0, $this->translator->translate('publication.relation.vorDoi', $this->locale) . ": ", '', 0, 'JUSTIFY', false, 0, false, false, 0);
                $titlePage->write(0, $this->submission->getJournalDOI(), $this->submission->getJournalDOI(), 0, 'JUSTIFY', true, 0, false, false, 0);
            }
        }
        else {
            $titlePage->Write(0, $this->translator->translate('plugins.generic.titlePageForPreprint.publicationStatus', $this->locale) . ": " . $this->translator->translate('plugins.generic.titlePageForPreprint.emptyPublicationStatus', $this->locale), '', 0, 'JUSTIFY', true, 0, false, false, 0);
        }

        $titlePage->Ln(5);
    }
    
    private function generateTitlePage(): string {
        try {
            $titlePage = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
            $titlePage->setPrintHeader(false);
            $titlePage->setPrintFooter(false);
            $titlePage->AddPage();
            $titlePage->Image($this->logo, '', '', '', '20', $this->getLogoType(), 'false', 'C', false, 400, 'C', false, false, 0, false, false, false);
            $titlePage->Ln(25);
            
            $this->writePublicationStatusOnTitlePage($titlePage);
            
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
        } catch(Exception $e) {
            throw new Exception('Title Page Generation Failure');
        }

        return $TitlePageFile;
    }

    public function generateChecklistPage(): string {
        try {
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
        } catch(Exception $e) {
            throw new Exception('Checklist Page Generation Failure');
        }

        return $checklistPageFile;
    }

    public function addDocumentHeader($pdf): void {
        $linkDOI = "https://doi.org/".$this->submission->getDOI();
        $headerText = $this->translator->translate('plugins.generic.titlePageForPreprint.headerText', $this->locale, ['doiPreprint' => $linkDOI]);
        $addHeaderCommand = self::CPDF_PATH . " -add-text \"{$headerText}\" -top 15pt -font \"Helvetica\" -font-size 8 {$pdf} -o {$pdf}";
        exec($addHeaderCommand, $output, $resultCode);

        if (!$this->commandSuccessful($resultCode)) {
            throw new Exception('Headers Stamping Failure');
        }
    }

    private function concatenateTitlePage($pdf, $titlePage): void {
        $uniteCommand = self::CPDF_PATH . " -merge {$titlePage} {$pdf} -o {$pdf}";
        exec($uniteCommand, $output, $resultCode);

        if (!$this->commandSuccessful($resultCode)) {
            throw new Exception('Title Page Concatenation Failure');
        }
    }

    public function concatenateChecklistPage($pdf, $checklistPage): void {
        $uniteCommand = self::CPDF_PATH . " -merge {$pdf} {$checklistPage} -o {$pdf}";
        exec($uniteCommand, $output, $resultCode);

        if (!$this->commandSuccessful($resultCode)) {
            throw new Exception('Checklist Page Concatenation Failure');
        }
    }

    public function insertTitlePageFirstTime(pdf $pdf) {
        $originalFile = $pdf->getPath();
        $originalFileCopy = self::OUTPUT_DIRECTORY . "original_file_copy.pdf";
        copy($originalFile, $originalFileCopy);

        $this->addDocumentHeader($originalFileCopy);

        $titlePage = $this->generateTitlePage();
        $this->concatenateTitlePage($originalFileCopy, $titlePage);

        $checklistPage = $this->generateChecklistPage();
        $this->concatenateChecklistPage($originalFileCopy, $checklistPage);

        rename($originalFileCopy, $originalFile);
    }

    public function updateTitlePage(pdf $pdf) {
        $originalFile = $pdf->getPath();
        $originalFileCopy = self::OUTPUT_DIRECTORY . "original_file_copy.pdf";
        copy($originalFile, $originalFileCopy);

        $this->removeTitlePage($originalFileCopy);
        
        $titlePage = $this->generateTitlePage();
        $this->concatenateTitlePage($originalFileCopy, $titlePage);

        rename($originalFileCopy, $originalFile);
    }
}
?>