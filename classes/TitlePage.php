<?php

namespace APP\plugins\generic\titlePageForPreprint\classes;

require dirname(__DIR__) . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';

use TCPDF_FONTS;
use TCPDF;
use Normalizer;
use APP\plugins\generic\titlePageForPreprint\classes\Pdf;
use APP\plugins\generic\titlePageForPreprint\classes\TitlePageRequirements;

class TitlePage
{
    private $submission;
    private $checklist;
    private $logo;
    private $locale;
    private $fontName;
    private $titlePageRequirements;
    public const OUTPUT_DIRECTORY = DIRECTORY_SEPARATOR . "tmp" .  DIRECTORY_SEPARATOR;

    public function __construct(SubmissionModel $submission, array $checklist, string $logo, string $locale)
    {
        $this->submission = $submission;
        $this->checklist = $checklist;
        $this->logo = $logo;
        $this->locale = $locale;
        $this->fontName = TCPDF_FONTS::addTTFfont(__DIR__ . '/../resources/opensans.ttf', 'TrueTypeUnicode', '', 32);
        $this->titlePageRequirements = new TitlePageRequirements();
    }

    public function removeTitlePage($pdf): void
    {
        $separateCommand = "cpdf {$pdf} 2-end -o {$pdf}";
        exec($separateCommand, $output, $resultCode);

        if ($resultCode === 0) {
            $this->titlePageRequirements->showMissingRequirementNotification('plugins.generic.titlePageForPreprint.requirements.removeTitlePageMissing');
            throw new Exception('Title Page Remove Failure');
        }
    }

    public function getLogoType(): string
    {
        $fileType = pathinfo($this->logo, PATHINFO_EXTENSION);
        return strtoupper($fileType);
    }

    private function writePublicationStatusOnTitlePage($titlePage)
    {
        $titlePage->SetFont($this->fontName, '', 10, '', false);

        if (!empty($this->submission->getStatus())) {
            $titlePage->Write(0, __('plugins.generic.titlePageForPreprint.publicationStatus', [], $this->locale) . ": " . __($this->submission->getStatus(), [], $this->locale), '', 0, 'JUSTIFY', true, 0, false, false, 0);

            if ($this->submission->getStatus() == 'publication.relation.published') {
                $titlePage->Write(0, __('publication.relation.vorDoi', [], $this->locale) . ": ", '', 0, 'JUSTIFY', false, 0, false, false, 0);
                $titlePage->write(0, $this->submission->getJournalDOI(), $this->submission->getJournalDOI(), 0, 'JUSTIFY', true, 0, false, false, 0);
            }
        } else {
            $titlePage->Write(0, __('plugins.generic.titlePageForPreprint.publicationStatus', [], $this->locale) . ": " . __('plugins.generic.titlePageForPreprint.emptyPublicationStatus', [], $this->locale), '', 0, 'JUSTIFY', true, 0, false, false, 0);
        }

        $titlePage->Ln(5);
    }

    private function generateTitlePage(): string
    {
        $errorMessage = 'plugins.generic.titlePageForPreprint.requirements.generateTitlePageMissing';
        try {
            $titlePage = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
            $titlePage->setPrintHeader(false);
            $titlePage->setPrintFooter(false);
            $titlePage->AddPage();
            $logoType = $this->getLogoType();

            if (!$logoType) {
                $errorMessage = 'plugins.generic.titlePageForPreprint.requirements.logoMissing';
            }

            $doiLink = "https://doi.org/" . $this->submission->getDOI();
            $titlePage->Image($this->logo, '', '', '', '20', $logoType, $doiLink, 'C', false, 400, 'C', false, false, 0, false, false, false);
            $titlePage->Ln(25);
            $this->writePublicationStatusOnTitlePage($titlePage);

            $titlePage->SetFont($this->fontName, '', 18, '', false);
            $normalizedTitle = Normalizer::normalize($this->submission->getTitle($this->locale));
            $titlePage->Write(0, $normalizedTitle, '', 0, 'C', true, 0, false, false, 0);

            $titlePage->SetFont($this->fontName, '', 12, '', false);
            $titlePage->Write(0, $this->submission->getAuthors(), '', 0, 'C', true, 0, false, false, 0);
            $titlePage->SetFont($this->fontName, '', 11, '', false);
            $titlePage->Ln(5);
            $titlePage->Write(0, $doiLink, $doiLink, 0, 'C', true, 0, false, false, 0);
            $titlePage->Ln(10);

            $titlePage->Write(0, __('plugins.generic.titlePageForPreprint.submissionDate', [], $this->locale, ['subDate' => $this->submission->getSubmissionDate()]), '', 0, 'JUSTIFY', true, 0, false, false, 0);
            $titlePage->Write(0, __('plugins.generic.titlePageForPreprint.publicationDate', [], $this->locale, ['postDate' => $this->submission->getPublicationDate(), 'version' => $this->submission->getVersion()]), '', 0, 'JUSTIFY', true, 0, false, false, 0);
            $titlePage->Write(0, __('plugins.generic.titlePageForPreprint.dateFormat', [], $this->locale), '', 0, 'JUSTIFY', true, 0, false, false, 0);

            $endorserName = $this->submission->getEndorserName();
            $endorserOrcid = $this->submission->getEndorserOrcid();
            if(!is_null($endorserOrcid) && !is_null($endorserName)) {
                $titlePage->Ln(5);
                $titlePage->writeHTML(__('plugins.generic.titlePageForPreprint.endorsement', ['endorserName' => $endorserName, 'endorserOrcid' => $endorserOrcid], $this->locale));
            }

            $versionJustification = $this->submission->getVersionJustification();
            if($this->submission->getVersion() > 1 && !is_null($versionJustification)) {
                $versionJustification = __('plugins.generic.titlePageForPreprint.versionJustification', [], $this->locale) . ": " . $versionJustification;
                $titlePage->Ln(5);
                $titlePage->Write(0, $versionJustification, '', 0, 'JUSTIFY', true, 0, false, false, 0);
            }

            $TitlePageFile = self::OUTPUT_DIRECTORY . 'titlePage.pdf';
            $titlePage->Output($TitlePageFile, 'F');
        } catch (Exception $e) {
            $this->titlePageRequirements->showMissingRequirementNotification($errorMessage);
            throw new Exception('Title Page Generation Failure');
        }

        return $TitlePageFile;
    }

    public function generateChecklistPage(): string
    {
        try {
            $checklistPage = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
            $checklistPage->setPrintHeader(false);
            $checklistPage->setPrintFooter(false);
            $checklistPage->AddPage();

            $checklistPage->Write(0, __('plugins.generic.titlePageForPreprint.checklistLabel', [], $this->locale) . ": ", '', 0, 'JUSTIFY', true, 0, false, false, 0);
            $checklistPage->SetFont($this->fontName, '', 10, '', false);
            $checklistPage->Ln(5);

            $checklistText = '';
            foreach ($this->checklist as $item) {
                $checklistText = $checklistText . "<ul style=\"text-align:justify;\"><li>" . $item . "</li></ul>";
            }
            $checklistPage->writeHTMLCell(0, 0, '', '', $checklistText, 1, 1, false, true, 'JUSTIFY', false);
            $checklistPage->SetFont($this->fontName, '', 11, '', false);
            $checklistPage->Ln(5);

            $checklistPageFile = self::OUTPUT_DIRECTORY . 'checklistPage.pdf';
            $checklistPage->Output($checklistPageFile, 'F');
        } catch (Exception $e) {
            $this->titlePageRequirements->showMissingRequirementNotification('plugins.generic.titlePageForPreprint.requirements.generateChecklistPageMissing');
            throw new Exception('Checklist Page Generation Failure');
        }

        return $checklistPageFile;
    }

    public function addDocumentHeader($pdf): void
    {
        $linkDOI = "https://doi.org/" . $this->submission->getDOI();
        $headerText = __('plugins.generic.titlePageForPreprint.headerText', ['doiPreprint' => $linkDOI], $this->locale);
        $addHeaderCommand = "cpdf -add-text \"{$headerText}\" -top 15pt -font \"Helvetica\" -font-size 8 {$pdf} -o {$pdf}";
        exec($addHeaderCommand, $output, $resultCode);

        if ($resultCode != 0) {
            $this->titlePageRequirements->showMissingRequirementNotification('plugins.generic.titlePageForPreprint.requirements.addDocumentHeaderMissing');
            throw new Exception('Headers Stamping Failure');
        }
    }

    private function concatenateTitlePage($pdf, $titlePage): void
    {
        $uniteCommand = "cpdf -merge {$titlePage} {$pdf} -o {$pdf}";
        exec($uniteCommand, $output, $resultCode);

        if ($resultCode != 0) {
            $this->titlePageRequirements->showMissingRequirementNotification('plugins.generic.titlePageForPreprint.requirements.concatenateTitlePageMissing');
            throw new Exception('Title Page Concatenation Failure');
        }
    }

    public function concatenateChecklistPage($pdf, $checklistPage): void
    {
        $uniteCommand = "cpdf -merge {$pdf} {$checklistPage} -o {$pdf}";
        exec($uniteCommand, $output, $resultCode);

        if ($resultCode != 0) {
            $this->titlePageRequirements->showMissingRequirementNotification('plugins.generic.titlePageForPreprint.requirements.concatenateChecklistPageMissing');
            throw new Exception('Checklist Page Concatenation Failure');
        }
    }

    public function insertTitlePageFirstTime(pdf $pdf)
    {
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

    public function updateTitlePage(pdf $pdf)
    {
        $originalFile = $pdf->getPath();
        $originalFileCopy = self::OUTPUT_DIRECTORY . "original_file_copy.pdf";
        copy($originalFile, $originalFileCopy);

        $this->removeTitlePage($originalFileCopy);

        $titlePage = $this->generateTitlePage();
        $this->concatenateTitlePage($originalFileCopy, $titlePage);

        rename($originalFileCopy, $originalFile);
    }
}
