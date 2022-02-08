<?php

import ('plugins.generic.titlePageForPreprint.classes.GalleyAdapter');
import ('plugins.generic.titlePageForPreprint.classes.SubmissionModel');
import ('plugins.generic.titlePageForPreprint.classes.SubmissionPress');
import ('plugins.generic.titlePageForPreprint.classes.Pdf');

class SubmissionPressTest extends PdfHandlingTest {
    
    private function registerMockGalleyAdapter($args): GalleyAdapter {
        $mockGalley = $this->getMockBuilder(GalleyAdapter::class)
            ->setConstructorArgs($args)
            ->setMethods(array('getFullFilePath'))
            ->getMock();

        $mockGalley->expects($this->any())
                   ->method('getFullFilePath')
                   ->will($this->returnValue($args[0]));
        
        return $mockGalley;
    }

    public function testInsertsCorrectlySingleGalley(): void {   
        $galleyPath = $this->pathOfTestPdf;
        $galley = $this->registerMockGalleyAdapter(array($galleyPath, $this->locale, 1, 2));
        $submission = new SubmissionModel($this->status, $this->doi, $this->doiJournal, $this->authors, $this->submissionDate, $this->publicationDate, $this->version, array($galley));
        $press = new SubmissionPress($this->logo, $submission, $this->translator);

        $press->insertTitlePage();
        
        $pdfOfGalley = new Pdf($galleyPath);
        $this->assertEquals(3, $pdfOfGalley->getNumberOfPages());
    }

    public function testInsertsCorrectlyMultipleGalleys(): void {
        $fistGalleyPath = $this->pathOfTestPdf;
        $secondGalleyPath = $this->pathOfTestPdf2;
        $firstGalley = $this->registerMockGalleyAdapter(array($fistGalleyPath, $this->locale, 2, 2));
        $secondGalley = $this->registerMockGalleyAdapter(array($secondGalleyPath, "en_US", 3, 2));
        $submission = new SubmissionModel($this->status, $this->doi, $this->doiJournal, $this->authors, $this->submissionDate, $this->publicationDate, $this->version, array($firstGalley, $secondGalley));
        
        $press = new SubmissionPress($this->logo, $submission, $this->translator);
        $press->insertTitlePage();

        $pdfOfFirstGalley = new Pdf($fistGalleyPath);
        $pdfOfSecondGalley = new Pdf($secondGalleyPath);

        $this->assertEquals(3, $pdfOfFirstGalley->getNumberOfPages());
        $this->assertEquals(4, $pdfOfSecondGalley->getNumberOfPages());
    }

    public function testMustIgnoreNotPdfFiles(): void {
        $fistGalleyPath = $this->pathOfTestPdf;
        $secondGalleyPath = TESTS_DIRECTORY . ASSETS_DIRECTORY . "fileNotPdf.odt";
        $firstGalley = $this->registerMockGalleyAdapter(array($fistGalleyPath, $this->locale, 4, 2));
        $secondGalley = $this->registerMockGalleyAdapter(array($secondGalleyPath, $this->locale, 5, 2));
        $submission = new SubmissionModel($this->status, $this->doi, $this->doiJournal, $this->authors, $this->submissionDate, $this->publicationDate, $this->version, array($firstGalley, $secondGalley));

        $hashOfNotPdfGalley = md5_file($secondGalleyPath);
        $press = new SubmissionPress($this->logo, $submission, $this->translator);
        $press->insertTitlePage();

        $pdfOfFirstGalley = new Pdf($fistGalleyPath);

        $this->assertEquals(3, $pdfOfFirstGalley->getNumberOfPages());
        $this->assertEquals($hashOfNotPdfGalley, md5_file($secondGalleyPath));
    }
}