<?php
require_once ("PdfHandlingTest.php");

class SubmissionPressTest extends PdfHandlingTest {

    public function testWithOnlyOnePdfTitlePageMustBeIncluded(): void {   
        $compositionPath = $this->pathOfTestPdf;
        $composition = new GalleyAdapter($compositionPath, $this->locale, 1, 2);
        $submission = new SubmissionModel($this->status, $this->doi, $this->doiJournal, $this->authors, $this->submissionDate, $this->publicationDate, array($composition));
        $press = new SubmissionPressForTests($this->logo, $submission, $this->translator);

        $press->insertTitlePage();
        
        $pdfOfComposition = new Pdf($compositionPath);
        $this->assertEquals(2, $pdfOfComposition->getNumberOfPages());
    }

    public function testWithMoreThanOnePdfTitlePageMustBeIncluded(): void {
        $fistCompositionPath = $this->pathOfTestPdf;
        $secondCompositionPath = $this->pathOfTestPdf2;
        $firstComposition = new GalleyAdapter($fistCompositionPath, $this->locale, 2, 2);
        $secondComposition = new GalleyAdapter($secondCompositionPath, "en_US", 3, 2);
        $submission = new SubmissionModel($this->status, $this->doi, $this->doiJournal, $this->authors, $this->submissionDate, $this->publicationDate, array($firstComposition, $secondComposition));
        
        $press = new SubmissionPressForTests($this->logo, $submission, $this->translator);
        $press->insertTitlePage();

        $pdfOfFirstComposition = new Pdf($fistCompositionPath);
        $pdfOfSecondComposition = new Pdf($secondCompositionPath);

        $this->assertEquals(2, $pdfOfFirstComposition->getNumberOfPages());
        $this->assertEquals(3, $pdfOfSecondComposition->getNumberOfPages());
    }

    public function testMustIgnoreNotPdfFiles(): void {
        $fistCompositionPath = $this->pathOfTestPdf;
        $secondCompositionPath = "tests" . DIRECTORY_SEPARATOR . "fileNotPdf.odt";
        $firstComposition = new GalleyAdapter($fistCompositionPath, $this->locale, 4, 2);
        $secondComposition = new GalleyAdapter($secondCompositionPath, $this->locale, 5, 2);
        $submission = new SubmissionModel($this->status, $this->doi, $this->doiJournal, $this->authors, $this->submissionDate, $this->publicationDate, array($firstComposition, $secondComposition));

        $hashOfNotPdfComposition = md5_file($secondCompositionPath);
        $press = new SubmissionPressForTests($this->logo, $submission, $this->translator);
        $press->insertTitlePage();

        $pdfOfFirstComposition = new Pdf($fistCompositionPath);

        $this->assertEquals(2, $pdfOfFirstComposition->getNumberOfPages());
        $this->assertEquals($hashOfNotPdfComposition, md5_file($secondCompositionPath));
    }
}