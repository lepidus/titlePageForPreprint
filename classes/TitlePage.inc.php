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
    const CPDF_PATH = __DIR__ . "/../tools/cpdf";

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
        return $TitlePageFile;
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

        $uniteCommand = self::CPDF_PATH . " -merge {$TitlePageFile} {$originalFileCopy} -o {$modifiedFile} > /dev/null 2>&1";

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

    public function remove(pdf $pdf): void {
        $modifiedFile = self::OUTPUT_DIRECTORY . "withoutTitlePage.pdf";
        $separateCommand = self::CPDF_PATH . " {$pdf->getPath()} 2-end -o {$modifiedFile} > /dev/null 2>&1";
        exec($separateCommand, $output, $resultCode);

        if (!$this->commandSuccessful($resultCode)) {
            throw new Exception('Title Page Remove Failure');
        }

        rename($modifiedFile, $pdf->getPath());
    }

    public function addDocumentHeader(pdf $pdf): void {
        $withHeaders = self::OUTPUT_DIRECTORY . "withoutHeaders.pdf";
        
        $linkDOI = "https://doi.org/".$this->submission->getDOI();
        $headerText = $this->translator->translate('plugins.generic.titlePageForPreprint.headerText', $this->locale, ['doiPreprint' => $linkDOI]);
        $addHeaderCommand = self::CPDF_PATH . " -add-text \"{$headerText}\" -top 15pt -font \"Helvetica\" -font-size 8 {$pdf->getPath()} -o {$withHeaders} > /dev/null 2>&1";
        exec($addHeaderCommand, $output, $resultCode);

        if (!$this->commandSuccessful($resultCode)) {
            throw new Exception('Title Page Remove Failure');
        }

        rename($withHeaders, $pdf->getPath());
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
        $uniteCommand = self::CPDF_PATH . " -merge {$originalFileCopy} {$checklistPageFile} -o {$modifiedFile} > /dev/null 2>&1";
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