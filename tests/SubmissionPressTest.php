<?php

use APP\plugins\generic\titlePageForPreprint\tests\PdfHandlingTest;
use APP\plugins\generic\titlePageForPreprint\classes\GalleyAdapter;
use APP\plugins\generic\titlePageForPreprint\classes\SubmissionFileUpdater;
use APP\plugins\generic\titlePageForPreprint\classes\SubmissionModel;
use APP\plugins\generic\titlePageForPreprint\classes\SubmissionPress;
use APP\plugins\generic\titlePageForPreprint\classes\Pdf;

class SubmissionPressTest extends PdfHandlingTest
{
    private function buildMockGalleyAdapter($args): GalleyAdapter
    {
        $mockGalley = $this->getMockBuilder(GalleyAdapter::class)
            ->setConstructorArgs($args)
            ->onlyMethods(['getFullFilePath'])
            ->getMock();

        $mockGalley->expects($this->any())
                    ->method('getFullFilePath')
                    ->will($this->returnValue($args[0]));

        return $mockGalley;
    }

    private function buildMockSubmissionFileUpdater(): SubmissionFileUpdater
    {
        $mockUpdater = $this->getMockBuilder(SubmissionFileUpdater::class)
            ->onlyMethods(['updateRevisions'])
            ->getMock();

        return $mockUpdater;
    }

    public function testInsertsCorrectlySingleGalley(): void
    {
        $galleyPath = $this->pathOfTestPdf;
        $galley = $this->buildMockGalleyAdapter(array($galleyPath, $this->locale, 1, 2));
        $submission = new SubmissionModel(
            $this->title,
            $this->status,
            $this->doi,
            $this->doiJournal,
            $this->authors,
            $this->submissionDate,
            $this->publicationDate,
            $this->endorserName,
            $this->endorserOrcid,
            $this->version,
            $this->versionJustification,
            $this->isTranslation,
            $this->citation,
            array($galley)
        );
        $press = new SubmissionPress($submission, $this->checklist, $this->logo);

        $press->insertTitlePage($this->buildMockSubmissionFileUpdater());

        $pdfOfGalley = new Pdf($galleyPath);
        $this->assertEquals(3, $pdfOfGalley->getNumberOfPages());
    }

    public function testInsertsCorrectlyMultipleGalleys(): void
    {
        $fistGalleyPath = $this->pathOfTestPdf;
        $secondGalleyPath = $this->pathOfTestPdf2;
        $firstGalley = $this->buildMockGalleyAdapter(array($fistGalleyPath, $this->locale, 2, 2));
        $secondGalley = $this->buildMockGalleyAdapter(array($secondGalleyPath, "en", 3, 2));
        $submission = new SubmissionModel(
            $this->title,
            $this->status,
            $this->doi,
            $this->doiJournal,
            $this->authors,
            $this->submissionDate,
            $this->publicationDate,
            $this->endorserName,
            $this->endorserOrcid,
            $this->version,
            $this->versionJustification,
            $this->isTranslation,
            $this->citation,
            array($firstGalley, $secondGalley)
        );

        $press = new SubmissionPress($submission, $this->checklist, $this->logo);
        $press->insertTitlePage($this->buildMockSubmissionFileUpdater());

        $pdfOfFirstGalley = new Pdf($fistGalleyPath);
        $pdfOfSecondGalley = new Pdf($secondGalleyPath);

        $this->assertEquals(3, $pdfOfFirstGalley->getNumberOfPages());
        $this->assertEquals(4, $pdfOfSecondGalley->getNumberOfPages());
    }

    public function testMustIgnoreNotPdfFiles(): void
    {
        $fistGalleyPath = $this->pathOfTestPdf;
        $secondGalleyPath = PdfHandlingTest::TESTS_DIRECTORY . PdfHandlingTest::ASSETS_DIRECTORY . "fileNotPdf.odt";
        $firstGalley = $this->buildMockGalleyAdapter(array($fistGalleyPath, $this->locale, 4, 2));
        $secondGalley = $this->buildMockGalleyAdapter(array($secondGalleyPath, $this->locale, 5, 2));
        $submission = new SubmissionModel(
            $this->title,
            $this->status,
            $this->doi,
            $this->doiJournal,
            $this->authors,
            $this->submissionDate,
            $this->publicationDate,
            $this->endorserName,
            $this->endorserOrcid,
            $this->version,
            $this->versionJustification,
            $this->isTranslation,
            $this->citation,
            array($firstGalley, $secondGalley)
        );

        $hashOfNotPdfGalley = md5_file($secondGalleyPath);
        $press = new SubmissionPress($submission, $this->checklist, $this->logo);
        $press->insertTitlePage($this->buildMockSubmissionFileUpdater());

        $pdfOfFirstGalley = new Pdf($fistGalleyPath);

        $this->assertEquals(3, $pdfOfFirstGalley->getNumberOfPages());
        $this->assertEquals($hashOfNotPdfGalley, md5_file($secondGalleyPath));
    }
}
