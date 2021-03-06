<?php
require_once ("PdfHandlingTest.php");

class SubmissionPressTest extends PdfHandlingTest {

    public function testWithOnlyOnePdfTitlePageMustBeIncluded(): void {   
        $galleyPath = $this->pathOfTestPdf;
        $galley = new GalleyAdapter($galleyPath, $this->locale, 1, 2);
        $submission = new SubmissionModel($this->status, $this->doi, $this->doiJournal, $this->authors, $this->submissionDate, $this->publicationDate, array($galley));
        $press = new SubmissionPressForTests($this->logo, $submission, $this->translator);

        $press->insertTitlePage();
        
        $pdfOfGalley = new Pdf($galleyPath);
        $this->assertEquals(2, $pdfOfGalley->getNumberOfPages());
    }

    public function testWithMoreThanOnePdfTitlePageMustBeIncluded(): void {
        $fistGalleyPath = $this->pathOfTestPdf;
        $secondGalleyPath = $this->pathOfTestPdf2;
        $firstGalley = new GalleyAdapter($fistGalleyPath, $this->locale, 2, 2);
        $secondGalley = new GalleyAdapter($secondGalleyPath, "en_US", 3, 2);
        $submission = new SubmissionModel($this->status, $this->doi, $this->doiJournal, $this->authors, $this->submissionDate, $this->publicationDate, array($firstGalley, $secondGalley));
        
        $press = new SubmissionPressForTests($this->logo, $submission, $this->translator);
        $press->insertTitlePage();

        $pdfOfFirstGalley = new Pdf($fistGalleyPath);
        $pdfOfSecondGalley = new Pdf($secondGalleyPath);

        $this->assertEquals(2, $pdfOfFirstGalley->getNumberOfPages());
        $this->assertEquals(3, $pdfOfSecondGalley->getNumberOfPages());
    }

    public function testMustIgnoreNotPdfFiles(): void {
        $fistGalleyPath = $this->pathOfTestPdf;
        $secondGalleyPath = "tests" . DIRECTORY_SEPARATOR . "fileNotPdf.odt";
        $firstGalley = new GalleyAdapter($fistGalleyPath, $this->locale, 4, 2);
        $secondGalley = new GalleyAdapter($secondGalleyPath, $this->locale, 5, 2);
        $submission = new SubmissionModel($this->status, $this->doi, $this->doiJournal, $this->authors, $this->submissionDate, $this->publicationDate, array($firstGalley, $secondGalley));

        $hashOfNotPdfGalley = md5_file($secondGalleyPath);
        $press = new SubmissionPressForTests($this->logo, $submission, $this->translator);
        $press->insertTitlePage();

        $pdfOfFirstGalley = new Pdf($fistGalleyPath);

        $this->assertEquals(2, $pdfOfFirstGalley->getNumberOfPages());
        $this->assertEquals($hashOfNotPdfGalley, md5_file($secondGalleyPath));
    }
}