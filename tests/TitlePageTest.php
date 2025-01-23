<?php

use APP\plugins\generic\titlePageForPreprint\tests\PdfHandlingTest;
use APP\plugins\generic\titlePageForPreprint\classes\TitlePage;
use APP\plugins\generic\titlePageForPreprint\classes\Pdf;
use APP\plugins\generic\titlePageForPreprint\classes\SubmissionModel;

class TitlePageTest extends PdfHandlingTest
{
    private function getTitlePageForTests(): TitlePage
    {
        $submission = $this->getSubmissionForTests();
        return new TitlePage($submission, $this->checklist, $this->logo, $this->locale);
    }

    private function convertPdfToImage(string $pdfPath, $imagePath): Imagick
    {
        $image = new Imagick($pdfPath);
        $image->setImageFormat('jpeg');
        $image->writeImage($imagePath);
        return $image;
    }

    private function imagesAreEqual(Imagick $image1, Imagick $image2): void
    {
        $differenceBetweenThem = $image1->compareImages($image2, Imagick::METRIC_MEANSQUAREERROR);
        $this->assertEquals(0.0, $differenceBetweenThem[1]);
    }

    private function extractImageFromPdf(Pdf $pdf): string
    {
        $pathOfExtractedImage = self::TESTS_DIRECTORY;
        $result = shell_exec("pdfimages -f 1 -png ". $pdf->getPath() . " " . $pathOfExtractedImage);
        $extractedImage = $pathOfExtractedImage . DIRECTORY_SEPARATOR . "-000.png";
        return $extractedImage;
    }

    private function searchForTextInPdf(Pdf $pdf, string $targetText, int $startPage = 1): bool
    {
        shell_exec("pdftotext -f " . $startPage . " ". $pdf->getPath() . " " . $this->pdfAsText);

        $pdfText = file_get_contents($this->pdfAsText, FILE_TEXT);
        $pdfText = str_replace(["\r", "\n"], ' ', $pdfText);

        return str_contains($pdfText, $targetText);
    }

    public function testInsertInExistingPdfFileCreatesNewPages(): void
    {
        $titlePage = $this->getTitlePageForTests();

        $pdf = new Pdf($this->pathOfTestPdf);
        $titlePage->insertTitlePageFirstTime($pdf);
        $this->assertEquals(3, $pdf->getNumberOfPages());
    }

    public function testInsertingInExistingPdfStampsChecklistOnLastPage(): void
    {
        $titlePage = $this->getTitlePageForTests();
        $pdf = new Pdf($this->pathOfTestPdf);

        $originalFile = $pdf->getPath();
        $originalFileCopy = self::OUTPUT_DIRECTORY . "original_file_copy.pdf";
        copy($originalFile, $originalFileCopy);

        $checklistPage = $titlePage->generateChecklistPage();
        $titlePage->concatenateChecklistPage($originalFileCopy, $checklistPage);
        rename($originalFileCopy, $originalFile);

        $numberOfPages = $pdf->getNumberOfPages();

        $expectedLabel = __('plugins.generic.titlePageForPreprint.checklistLabel', [], $this->locale) . ':';
        $this->assertTrue($this->searchForTextInPdf($pdf, $expectedLabel, $numberOfPages));

        $firstItem = $this->checklist[$this->locale][0];
        $this->assertTrue($this->searchForTextInPdf($pdf, $firstItem, $numberOfPages));

        $secondItem = $this->checklist[$this->locale][1];
        $this->assertTrue($this->searchForTextInPdf($pdf, $secondItem, $numberOfPages));
    }

    public function testInsertingInExistingPdfStampsLogo(): void
    {
        $titlePage = $this->getTitlePageForTests();
        $pdf = new Pdf($this->pathOfTestPdf);

        $titlePage->insertTitlePageFirstTime($pdf);

        $extractedImage = $this->extractImageFromPdf($pdf);
        $this->imagesAreEqual(new Imagick($this->logo), new Imagick($extractedImage));
        unlink($extractedImage);
    }

    public function testInsertingInExistingPdfStampsRelation(): void
    {
        $titlePage = $this->getTitlePageForTests();
        $pdf = new Pdf($this->pathOfTestPdf);

        $titlePage->insertTitlePageFirstTime($pdf);

        $expectedText = __('plugins.generic.titlePageForPreprint.publicationStatus', [], $this->locale)
            . ': ' . __('publication.relation.none', [], $this->locale);

        $this->assertTrue($this->searchForTextInPdf($pdf, $expectedText));
    }

    public function testInsertingInExistingPdfStampsNotInformedRelation(): void
    {
        $submission = $this->getSubmissionForTests();
        $submission->setData('status', '');
        $titlePage = new TitlePage($submission, $this->checklist, $this->logo, $this->locale);
        $pdf = new Pdf($this->pathOfTestPdf);

        $titlePage->insertTitlePageFirstTime($pdf);

        $expectedText = __('plugins.generic.titlePageForPreprint.publicationStatus', [], $this->locale)
            . ': ' . __('plugins.generic.titlePageForPreprint.emptyPublicationStatus', [], $this->locale);

        $this->assertTrue($this->searchForTextInPdf($pdf, $expectedText));
    }

    public function testInsertingInExistingPdfStampsTitle(): void
    {
        $titlePage = $this->getTitlePageForTests();
        $pdf = new Pdf($this->pathOfTestPdf);

        $titlePage->insertTitlePageFirstTime($pdf);

        $this->assertTrue($this->searchForTextInPdf($pdf, $this->title[$this->locale]));
    }

    public function testInsertingInExistingPdfStampsAuthors(): void
    {
        $titlePage = $this->getTitlePageForTests();
        $pdf = new Pdf($this->pathOfTestPdf);

        $titlePage->insertTitlePageFirstTime($pdf);

        $this->assertTrue($this->searchForTextInPdf($pdf, $this->authors));
    }

    public function testInsertingInExistingPdfStampsDoi(): void
    {
        $titlePage = $this->getTitlePageForTests();
        $pdf = new Pdf($this->pathOfTestPdf);

        $titlePage->insertTitlePageFirstTime($pdf);

        $expectedText = "https://doi.org/" . $this->doi;
        $this->assertTrue($this->searchForTextInPdf($pdf, $expectedText));
    }

    public function testInsertingInExistingPdfStampsSubmissionDate(): void
    {
        $titlePage = $this->getTitlePageForTests();
        $pdf = new Pdf($this->pathOfTestPdf);

        $titlePage->insertTitlePageFirstTime($pdf);

        $expectedText = __('plugins.generic.titlePageForPreprint.submissionDate', ['subDate' => $this->submissionDate], $this->locale);
        $this->assertTrue($this->searchForTextInPdf($pdf, $expectedText));
    }

    public function testInsertingInExistingPdfStampsPublicationDate(): void
    {
        $titlePage = $this->getTitlePageForTests();
        $pdf = new Pdf($this->pathOfTestPdf);

        $titlePage->insertTitlePageFirstTime($pdf);

        $expectedText = __('plugins.generic.titlePageForPreprint.publicationDate', ['postDate' => $this->publicationDate, 'version' => $this->version], $this->locale);
        $this->assertTrue($this->searchForTextInPdf($pdf, $expectedText));
    }

    public function testDontStampVersionJustificationOnFirstVersion(): void
    {
        $titlePage = $this->getTitlePageForTests();
        $pdf = new Pdf($this->pathOfTestPdf);

        $titlePage->insertTitlePageFirstTime($pdf);

        $expectedText = __('plugins.generic.titlePageForPreprint.versionJustification', [], $this->locale) . ': ' . $this->versionJustification;
        $this->assertFalse($this->searchForTextInPdf($pdf, $expectedText));
    }

    public function testStampsVersionJustificationFromSecondVersion(): void
    {
        $this->version = "2";
        $titlePage = $this->getTitlePageForTests();
        $pdf = new Pdf($this->pathOfTestPdf);

        $titlePage->insertTitlePageFirstTime($pdf);

        $expectedText = __('plugins.generic.titlePageForPreprint.versionJustification', [], $this->locale) . ': ' . $this->versionJustification;
        $this->assertTrue($this->searchForTextInPdf($pdf, $expectedText));
    }

    public function testInsertingInExistingPdfStampsEndorsement(): void
    {
        $titlePage = $this->getTitlePageForTests();
        $pdf = new Pdf($this->pathOfTestPdf);

        $titlePage->insertTitlePageFirstTime($pdf);

        $expectedText = __('plugins.generic.titlePageForPreprint.endorsement', [], $this->locale);
        $this->assertTrue($this->searchForTextInPdf($pdf, $expectedText));
    }

    public function testInsertingInExistingPdfStampsCitation(): void
    {
        $submission = $this->getSubmissionForTests();
        $submission->setData('isTranslation', true);
        $titlePage = new TitlePage($submission, $this->checklist, $this->logo, $this->locale);
        $pdf = new Pdf($this->pathOfTestPdf);

        $titlePage->insertTitlePageFirstTime($pdf);

        $expectedText = __('plugins.generic.titlePageForPreprint.citation', [], $this->locale);
        $this->assertTrue($this->searchForTextInPdf($pdf, $expectedText));
    }

    public function testInsertingInExistingPdfStampsDataStatement(): void
    {
        $titlePage = $this->getTitlePageForTests();
        $pdf = new Pdf($this->pathOfTestPdf);

        $titlePage->insertTitlePageFirstTime($pdf);
        $expectedText = __('plugins.generic.titlePageForPreprint.dataStatement', [], $this->locale);
        $this->assertTrue($this->searchForTextInPdf($pdf, $expectedText));

        $firstStatement = $this->dataStatement[0];
        $this->assertTrue($this->searchForTextInPdf($pdf, $firstStatement));

        $secondStatementMsg = $this->dataStatement[1]['message'];
        $this->assertTrue($this->searchForTextInPdf($pdf, $secondStatementMsg));

        $secondStatementReason = $this->dataStatement[1]['dataStatementReason'];
        $this->assertTrue($this->searchForTextInPdf($pdf, $secondStatementReason));
    }

    public function testInsertingInExistingPdfStampsResearchData(): void
    {
        $titlePage = $this->getTitlePageForTests();
        $pdf = new Pdf($this->pathOfTestPdf);

        $titlePage->insertTitlePageFirstTime($pdf);

        $expectedText = __('plugins.generic.titlePageForPreprint.researchData', ['researchDataCitation' => $this->researchData], $this->locale);
        $this->assertTrue($this->searchForTextInPdf($pdf, $expectedText));
    }

    public function testInsertingInExistingPdfStampsHeader(): void
    {
        $titlePage = $this->getTitlePageForTests();
        $pdf = new Pdf($this->pathOfTestPdf);

        $titlePage->addDocumentHeader($this->pathOfTestPdf);

        $expectedText = __('plugins.generic.titlePageForPreprint.headerText', ['doiPreprint' => $this->doi], $this->locale);
        $this->assertTrue($this->searchForTextInPdf($pdf, $expectedText));
    }

    public function testInsertingInExistingPdfDontChangeOriginal(): void
    {
        $titlePage = $this->getTitlePageForTests();
        $pdfOriginalWithHeaders = self::OUTPUT_DIRECTORY . "originalWithHeaders.pdf";
        copy($this->pathOfTestPdf, $pdfOriginalWithHeaders);
        $titlePage->addDocumentHeader($pdfOriginalWithHeaders);

        $pdfWithTitlePage = new Pdf($this->pathOfTestPdf);
        $titlePage->insertTitlePageFirstTime($pdfWithTitlePage);

        $fileImageOriginalWithHeaders = 'imagem_pdf_original.jpg';
        $fileImagePdfWithTitlePage = 'imagem_pdf_folhaderosto.jpg';
        $imageOfOriginalPdf = $this->convertPdfToImage($pdfOriginalWithHeaders . '[0]', $fileImageOriginalWithHeaders);
        $imageOfPdfWithTitlePage = $this->convertPdfToImage($pdfWithTitlePage->getPath() . '[1]', $fileImagePdfWithTitlePage);
        $this->imagesAreEqual($imageOfOriginalPdf, $imageOfPdfWithTitlePage);
        unlink($pdfOriginalWithHeaders);
        unlink($fileImageOriginalWithHeaders);
        unlink($fileImagePdfWithTitlePage);
    }

    public function testStampsTitlePageWithRelationTranslatedToGalleyLanguage(): void
    {
        $submission = $this->getSubmissionForTests();
        $titlePage = new TitlePage($submission, $this->checklist, $this->logo, "en");
        $pdf = new Pdf($this->pathOfTestPdf);

        $titlePage->insertTitlePageFirstTime($pdf);

        $expectedText = __('plugins.generic.titlePageForPreprint.publicationStatus', [], 'en')
            . ': ' . __('publication.relation.none', [], 'en');
        $this->assertTrue($this->searchForTextInPdf($pdf, $expectedText));
    }

    public function testInsertingInExistingPdfStampsNotInformedRelationTranslated(): void
    {
        $submission = $this->getSubmissionForTests();
        $submission->setData('status', '');
        $titlePage = new TitlePage($submission, $this->checklist, $this->logo, "en");
        $pdf = new Pdf($this->pathOfTestPdf);

        $titlePage->insertTitlePageFirstTime($pdf);

        $expectedText = __('plugins.generic.titlePageForPreprint.publicationStatus', [], 'en')
            . ": " . __('plugins.generic.titlePageForPreprint.emptyPublicationStatus', [], 'en');
        $this->assertTrue($this->searchForTextInPdf($pdf, $expectedText));
    }

    public function testStampsChecklistTranslatedToGalleyLanguage(): void
    {
        $submission = $this->getSubmissionForTests();
        $titlePage = new TitlePage($submission, $this->checklist, $this->logo, 'en');
        $pdf = new Pdf($this->pathOfTestPdf);

        $originalFile = $pdf->getPath();
        $originalFileCopy = self::OUTPUT_DIRECTORY . "original_file_copy.pdf";
        copy($originalFile, $originalFileCopy);

        $checklistPage = $titlePage->generateChecklistPage();
        $titlePage->concatenateChecklistPage($originalFileCopy, $checklistPage);
        rename($originalFileCopy, $originalFile);

        $numberOfPages = $pdf->getNumberOfPages();

        $expectedText = __("plugins.generic.titlePageForPreprint.checklistLabel", [], 'en') . ': ';
        $this->assertTrue($this->searchForTextInPdf($pdf, $expectedText, $numberOfPages));

        $firstItem = $this->checklist['en'][0];
        $this->assertTrue($this->searchForTextInPdf($pdf, $firstItem, $numberOfPages));

        $secondItem = $this->checklist['en'][1];
        $this->assertTrue($this->searchForTextInPdf($pdf, $secondItem, $numberOfPages));
    }

    public function testStampsTitlePageWithSubmissionDateTranslatedToGalleyLanguage(): void
    {
        $submission = $this->getSubmissionForTests();
        $titlePage = new TitlePage($submission, $this->checklist, $this->logo, 'en');
        $pdf = new Pdf($this->pathOfTestPdf);

        $titlePage->insertTitlePageFirstTime($pdf);

        $expectedText = __('plugins.generic.titlePageForPreprint.submissionDate', ['subDate' => $this->submissionDate], 'en');
        $this->assertTrue($this->searchForTextInPdf($pdf, $expectedText));
    }

    public function testStampsTitlePageWithPublicationDateTranslatedToGalleyLanguage(): void
    {
        $submission = $this->getSubmissionForTests();
        $titlePage = new TitlePage($submission, $this->checklist, $this->logo, 'en');
        $pdf = new Pdf($this->pathOfTestPdf);

        $titlePage->insertTitlePageFirstTime($pdf);

        $expectedText = __('plugins.generic.titlePageForPreprint.publicationDate', ['postDate' => $this->publicationDate, 'version' => $this->version], 'en');
        $this->assertTrue($this->searchForTextInPdf($pdf, $expectedText));
    }

    public function testStampsTitlePageWithDateFormat(): void
    {
        $titlePage = $this->getTitlePageForTests();
        $pdf = new Pdf($this->pathOfTestPdf);

        $titlePage->insertTitlePageFirstTime($pdf);

        $expectedText = __('plugins.generic.titlePageForPreprint.dateFormat', [], $this->locale);
        $this->assertTrue($this->searchForTextInPdf($pdf, $expectedText));
    }

    public function testStampsTitlePageWithDateFormatTranslatedToGalleyLanguage(): void
    {
        $submission = $this->getSubmissionForTests();
        $titlePage = new TitlePage($submission, $this->checklist, $this->logo, 'en');
        $pdf = new Pdf($this->pathOfTestPdf);

        $titlePage->insertTitlePageFirstTime($pdf);

        $expectedText = __('plugins.generic.titlePageForPreprint.dateFormat', [], 'en');
        $this->assertTrue($this->searchForTextInPdf($pdf, $expectedText));
    }
}
