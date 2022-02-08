<?php

import('lib.pkp.tests.PKPTestCase');
import('plugins.generic.titlePageForPreprint.classes.Translator');

define('TESTS_DIRECTORY', (dirname(__FILE__)));
define('ASSETS_DIRECTORY', DIRECTORY_SEPARATOR. 'assets'. DIRECTORY_SEPARATOR);
define('OUTPUT_DIRECTORY', DIRECTORY_SEPARATOR. "tmp".  DIRECTORY_SEPARATOR);

class PdfHandlingTest extends PKPTestCase {

    protected $status = "publication.relation.none";
    protected $doi = "10.1000/182";
    protected $doiJournal = "https://doi.org/10.1590/1413-81232020256.1.10792020";
    protected $logo = TESTS_DIRECTORY. ASSETS_DIRECTORY. "logo_noAlphaChannel.png";
    protected $checklist = array("A submissão não foi publicado anteriormente.", "As URLs das referências foram fornecidas.");
    protected $locale = "pt_BR";
    protected $title = "Assim Falou Zaratustra";
    protected $authors = "Cleide Silva; João Carlos";
    protected $submissionDate = "31/06/2020";
    protected $publicationDate = "02/07/2020";
    protected $version = "1";
    protected $translator;

    protected function setUp(): void {
        parent::setUp();

        $this->registerMockSubmissionFileDAO();

        $this->translator = $this->registerMockTranslator();
        $this->pathOfTestPdf = TESTS_DIRECTORY. ASSETS_DIRECTORY. "testOnePage.pdf";
        $this->copyOfTestPdfToRestore = TESTS_DIRECTORY. ASSETS_DIRECTORY. "testOnePage_copy.pdf";
        copy($this->pathOfTestPdf, $this->copyOfTestPdfToRestore);
        $this->pdfAsText = TESTS_DIRECTORY. ASSETS_DIRECTORY. "testOnePage.txt";

        $this->pathOfTestPdf2 = TESTS_DIRECTORY. ASSETS_DIRECTORY. "testTwoPages.pdf";
        $this->copyOfTestPdfToRestore2 = TESTS_DIRECTORY. ASSETS_DIRECTORY. "testTwoPages_copy.pdf";
        copy($this->pathOfTestPdf2, $this->copyOfTestPdfToRestore2);
    }

    protected function getMockedDAOs() {
		return array('SubmissionFileDAO');
	}

    private function registerMockSubmissionFileDAO(): void {
        $submissionFileDAO = $this->getMockBuilder(SubmissionFileDAO::class)
			->setMethods(array('getById'))
			->getMock();

		$submissionFile = new SubmissionFile();
        $submissionFile->setData('folhaDeRosto', 'nao');

		$submissionFileDAO->expects($this->any())
		           ->method('getById')
		           ->will($this->returnValue($submissionFile));

		DAORegistry::registerDAO('SubmissionFileDAO', $submissionFileDAO);

    }

    private function registerMockTranslator(): Translator {
        $mockContext = $this->getMockBuilder(Context::class)
            ->setMethods(array('getData'))
            ->getMock();

        $mockPublication = $this->getMockBuilder(Publication::class)
            ->setMethods(array('getLocalizedTitle'))
            ->getMock();

        $mockTranslator = $this->getMockBuilder(Translator::class)
            ->setConstructorArgs(array($mockContext, $mockPublication))
            ->setMethods(array('getTranslatedChecklist'))
            ->getMock();

        $mockTranslator->expects($this->any())
                        ->method('getTranslatedChecklist')
                        ->will($this->returnValue($this->checklist));

        return $mockTranslator;
    }
    
    protected function tearDown(): void {
        parent::tearDown();

        $this->assertTrue(unlink($this->pathOfTestPdf));
        rename($this->copyOfTestPdfToRestore, $this->pathOfTestPdf);
        
        $this->assertTrue(unlink($this->pathOfTestPdf2));
        rename($this->copyOfTestPdfToRestore2, $this->pathOfTestPdf2);

        if (file_exists($this->pdfAsText)) {
            unlink($this->pdfAsText);
        }
    }

    public function testDummy(): void {
        $this->assertTrue(true);
    }
}
?>