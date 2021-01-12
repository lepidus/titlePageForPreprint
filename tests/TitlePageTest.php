<?php
require_once ("PdfHandlingTest.php");

class TitlePageTest extends PdfHandlingTest {
    
    private function getTitlePageForTests(): TitlePage {
        return new TitlePage(new SubmissionModel($this->status, $this->doi, $this->doiJournal, $this->authors, $this->submissionDate, $this->publicationDate), $this->logo, $this->locale, $this->translator);
    }

    private function convertPdfToImage(string $pdfPath, $imagePath): imagick {
        $image = new imagick($pdfPath);
        $image->setImageFormat('jpeg');
        $image->writeImage($imagePath);
        return $image;
    }
    
    private function imagesAreEqual(imagick $image1, imagick $image2): void {
        $differenceBetweenThem = $image1->compareImages($image2, Imagick::METRIC_MEANSQUAREERROR);
        $this->assertEquals(0.0, $differenceBetweenThem[1]);
    }

    private function extractImageFromPdf(pdf $pdf): string {
        $pathOfExtractedImage = "tests" . DIRECTORY_SEPARATOR;
        $result = shell_exec("pdfimages -f 1 -png ". $pdf->getPath() . " " . $pathOfExtractedImage);
        $extractedImage = $pathOfExtractedImage . DIRECTORY_SEPARATOR . "-000.png";
        return $extractedImage;
    }

    private function convertPdfToText(pdf $pdf): void {
        shell_exec("pdftotext ". $pdf->getPath() . " " . $this->pdfAsText);
    }

    private function searchInTextFiles($targetString, $filePath): string {
        $searchResult = shell_exec("grep '$targetString' ". $filePath);
        return trim($searchResult);
    }

    public function testInsertInExistingPdfFileCreatesNewPage(): void {
        $titlePage = $this->getTitlePageForTests();

        $pdf = new Pdf($this->pathOfTestPdf);
        $titlePage->insert($pdf);
        $this->assertEquals(2, $pdf->getNumberOfPages());
    }

    public function testInExistingPdfRemovePage(): void {
        $titlePage = $this->getTitlePageForTests();
        $pdf = new Pdf($this->pathOfTestPdf);
        $titlePage->insert($pdf);
        $titlePage->remove($pdf);
        $this->assertEquals(1, $pdf->getNumberOfPages());
    }

    public function testInsertingInExistingPdfStampsOnChecklist(): void {
        $titlePage = $this->getTitlePageForTests();
        $pdf = new Pdf($this->pathOfTestPdf);
        
        $titlePage->insert($pdf);
        
        $this->convertPdfToText($pdf);

        $expectedLabel = "Este preprint foi submetido sob as seguintes condições:";
        $labelSearchResults = $this->searchInTextFiles($expectedLabel, $this->pdfAsText);
        $this->assertEquals($expectedLabel, $labelSearchResults);

        $firstItem = $this->checklist[0];
        $resultOfSearchForFirstItemOfChecklist = $this->searchInTextFiles($firstItem, $this->pdfAsText);
        $this->assertEquals($firstItem, $resultOfSearchForFirstItemOfChecklist);
        $secondItem = $this->checklist[1];
        $resultOfSearchForSecondItemOfChecklist = $this->searchInTextFiles($secondItem, $this->pdfAsText);
        $this->assertEquals($secondItem, $resultOfSearchForSecondItemOfChecklist);
    }

    public function testInsertingInExistingPdfStampsLogo(): void {
        $titlePage = $this->getTitlePageForTests();
        $pdf = new Pdf($this->pathOfTestPdf);
        
        $titlePage->insert($pdf);
        
        $extractedImage = $this->extractImageFromPdf($pdf);
        $this->imagesAreEqual(new imagick($this->logo), new imagick($extractedImage));
        unlink($extractedImage);
    }

    public function testInsertingInExistingPdfStampsRelation(): void {
        $titlePage = $this->getTitlePageForTests();
        $pdf = new Pdf($this->pathOfTestPdf);
        
        $titlePage->insert($pdf);

        $this->convertPdfToText($pdf);
        $expectedText = "Situação: O preprint não foi submetido para publicação";
        $searchResult = $this->searchInTextFiles($expectedText, $this->pdfAsText);
        $this->assertEquals($expectedText, $searchResult);
    }

    public function testInsertingInExistingPdfStampsTitle(): void {
        $titlePage = $this->getTitlePageForTests();
        $pdf = new Pdf($this->pathOfTestPdf);
        
        $titlePage->insert($pdf);
        
        $this->convertPdfToText($pdf);
        $expectedText = $this->title;
        $searchResult = $this->searchInTextFiles($expectedText, $this->pdfAsText);
        $this->assertEquals($expectedText, $searchResult);
    }

    public function testInsertingInExistingPdfStampsAuthors(): void {
        $titlePage = $this->getTitlePageForTests();
        $pdf = new Pdf($this->pathOfTestPdf);
        
        $titlePage->insert($pdf);
        
        $this->convertPdfToText($pdf);
        $expectedText = $this->authors;
        $searchResult = $this->searchInTextFiles($expectedText, $this->pdfAsText);
        $this->assertEquals($expectedText, $searchResult);
    }

    public function testInsertingInExistingPdfStampsDOI(): void {
        $titlePage = $this->getTitlePageForTests();
        $pdf = new Pdf($this->pathOfTestPdf);
        
        $titlePage->insert($pdf);
        
        $this->convertPdfToText($pdf);
        $expectedText = "https://doi.org/" . $this->doi;
        $searchResult = $this->searchInTextFiles($expectedText, $this->pdfAsText);
        $this->assertEquals($expectedText, $searchResult);
    }

    public function testInsertingInExistingPdfStampsSubmissionDate(): void {
        $titlePage = $this->getTitlePageForTests();
        $pdf = new Pdf($this->pathOfTestPdf);
        
        $titlePage->insert($pdf);
        
        $this->convertPdfToText($pdf);
        $expectedText = "Data de submissão: ". $this->submissionDate;
        $searchResult = $this->searchInTextFiles($expectedText, $this->pdfAsText);
        $this->assertEquals($expectedText, $searchResult);
    }

    public function testInsertingInExistingPdfStampsPublicationDate(): void {
        $titlePage = $this->getTitlePageForTests();
        $pdf = new Pdf($this->pathOfTestPdf);
        
        $titlePage->insert($pdf);

        $this->convertPdfToText($pdf);
        $expectedText = "Data de postagem: ". $this->publicationDate;
        $searchResult = $this->searchInTextFiles($expectedText, $this->pdfAsText);
        $this->assertEquals($expectedText, $searchResult);
    }

    public function testInsertingInExistingPdfStampsHeader(): void {
        $titlePage = $this->getTitlePageForTests();
        $pdf = new Pdf($this->pathOfTestPdf);

        $titlePage->addDocumentHeader($pdf);

        $this->convertPdfToText($pdf);
        $expectedText = "SciELO Preprints - este preprint não foi revisado por pares";
        $searchResult = $this->searchInTextFiles($expectedText, $this->pdfAsText);
        $this->assertEquals($expectedText, $searchResult);
    }

    public function testInsertingInExistingPdfDontChangeOriginal(): void {
        $titlePage = $this->getTitlePageForTests();
        $newPdf = new Pdf($this->pathOfTestPdf);
        $titlePage->insert($newPdf);
        $originalPdf = new Pdf($this->copyOfTestPdfToRestore);

        $fileImageOfOriginalPdf = 'imagem_pdf_original.jpg';
        $fileImageOfPdfWithTitlePage = 'imagem_pdf_folhaderosto.jpg';
        $imageOfOriginalPdf = $this->convertPdfToImage($originalPdf->getPath().'[0]', $fileImageOfOriginalPdf);
        $imageOfPdfWithTitlePage = $this->convertPdfToImage($newPdf->getPath().'[1]', $fileImageOfPdfWithTitlePage);
        $this->imagesAreEqual($imageOfOriginalPdf, $imageOfPdfWithTitlePage);
        unlink($fileImageOfOriginalPdf);
        unlink($fileImageOfPdfWithTitlePage);
    }

    public function testStampsTitlePageWithRelationTranslatedToCompositionLanguage(): void {
        $titlePage = new TitlePage(new SubmissionModel($this->status, $this->doi, $this->doiJournal, $this->authors, $this->submissionDate, $this->publicationDate), $this->logo, "en_US", $this->translator);
        $pdf = new Pdf($this->pathOfTestPdf);
        
        $titlePage->insert($pdf);
        
        $this->convertPdfToText($pdf);
        $expectedText = "Status: Preprint has not been submitted for publication";
        $searchResult = $this->searchInTextFiles($expectedText, $this->pdfAsText);
        $this->assertEquals($expectedText, $searchResult);
    }

    public function testStampsTitlePageWithChecklistLabelTranslatedToCompositionLanguage(): void {
        $titlePage = new TitlePage(new SubmissionModel($this->status, $this->doi, $this->doiJournal, $this->authors, $this->submissionDate, $this->publicationDate), $this->logo, "en_US", $this->translator);
        $pdf = new Pdf($this->pathOfTestPdf);
        
        $titlePage->insert($pdf);
        
        $this->convertPdfToText($pdf);
        $expectedText = "This preprint was submitted under the following conditions:";
        $searchResult = $this->searchInTextFiles($expectedText, $this->pdfAsText);
        $this->assertEquals($expectedText, $searchResult);
    }

    public function testStampsTitlePageWithChecklistTranslatedToCompositionLanguage(): void {
        $titlePage = new TitlePage(new SubmissionModel($this->status, $this->doi, $this->doiJournal, $this->authors, $this->submissionDate, $this->publicationDate), $this->logo, "en_US", $this->translator);
        $pdf = new Pdf($this->pathOfTestPdf);
        
        $titlePage->insert($pdf);
        
        $this->convertPdfToText($pdf);

        $firstItem = "The submission has not been previously published.";
        $resultOfSearchForFirstItemOfChecklist = $this->searchInTextFiles($firstItem, $this->pdfAsText);
        $this->assertEquals($firstItem, $resultOfSearchForFirstItemOfChecklist);
        $secondItem = "Where available, URLs for the references have been provided.";
        $resultOfSearchForSecondItemOfChecklist = $this->searchInTextFiles($secondItem, $this->pdfAsText);
        $this->assertEquals($secondItem, $resultOfSearchForSecondItemOfChecklist);
    }

    public function testStampsTitlePageWithSubmissionDateTranslatedToCompositionLanguage(): void {
        $titlePage = new TitlePage(new SubmissionModel($this->status, $this->doi, $this->doiJournal, $this->authors, $this->submissionDate, $this->publicationDate), $this->logo, "en_US", $this->translator);
        $pdf = new Pdf($this->pathOfTestPdf);
        
        $titlePage->insert($pdf);
        
        $this->convertPdfToText($pdf);
        $expectedText = "Date submitted: ". $this->submissionDate;
        $searchResult = $this->searchInTextFiles($expectedText, $this->pdfAsText);
        $this->assertEquals($expectedText, $searchResult);
    }

    public function testStampsTitlePageWithPublicationDateTranslatedToCompositionLanguage(): void {
        $titlePage = new TitlePage(new SubmissionModel($this->status, $this->doi, $this->doiJournal, $this->authors, $this->submissionDate, $this->publicationDate), $this->logo, "en_US", $this->translator);
        $pdf = new Pdf($this->pathOfTestPdf);
        
        $titlePage->insert($pdf);
        
        $this->convertPdfToText($pdf);
        $expectedText = "Date published: ". $this->publicationDate;
        $searchResult = $this->searchInTextFiles($expectedText, $this->pdfAsText);
        $this->assertEquals($expectedText, $searchResult);
    }
}
?>