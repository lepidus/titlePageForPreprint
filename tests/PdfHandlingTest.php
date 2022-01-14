<?php
use PHPUnit\Framework\TestCase;
import ('plugins.generic.titlePageForPreprint.classes.TranslatorForTests');

class PdfHandlingTest extends TestCase {
    protected const TESTS_DIRECTORY = 'plugins' . DIRECTORY_SEPARATOR . 'generic' . DIRECTORY_SEPARATOR . 'titlePageForPreprint' . DIRECTORY_SEPARATOR . 'tests' . DIRECTORY_SEPARATOR;
    protected const OUTPUT_DIRECTORY = DIRECTORY_SEPARATOR . "tmp" .  DIRECTORY_SEPARATOR;

    protected $status = "publication.relation.none";
    protected $doi = "10.1000/182";
    protected $doiJournal = "https://doi.org/10.1590/1413-81232020256.1.10792020";
    protected $logo = self::TESTS_DIRECTORY . "logo_noAlphaChannel.png";
    protected $checklist = array("A submissão não foi publicado anteriormente.", "As URLs das referências foram fornecidas.");
    protected $locale = "pt_BR";
    protected $title = "Assim Falou Zaratustra";
    protected $authors = "Cleide Silva; João Carlos";
    protected $submissionDate = "31/06/2020";
    protected $publicationDate = "02/07/2020";
    protected $version = "1";
    protected $translator;

    protected function setUp(): void {
        $this->translator = new TranslatorForTests();
        $this->pathOfTestPdf = self::TESTS_DIRECTORY . "testOnePage.pdf";
        $this->copyOfTestPdfToRestore = self::TESTS_DIRECTORY . "testOnePage_copy.pdf";
        copy($this->pathOfTestPdf, $this->copyOfTestPdfToRestore);
        $this->pdfAsText = self::TESTS_DIRECTORY . "testOnePage.txt";

        $this->pathOfTestPdf2 = self::TESTS_DIRECTORY . "testTwoPages.pdf";
        $this->copyOfTestPdfToRestore2 = self::TESTS_DIRECTORY . "testTwoPages_copy.pdf";
        copy($this->pathOfTestPdf2, $this->copyOfTestPdfToRestore2);
    }
    
    protected function tearDown(): void {
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