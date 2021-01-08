<?php
use PHPUnit\Framework\TestCase;

class PdfHandlingTest extends TestCase {

    protected $status = "publication.relation.none";
    protected $doi = "10.1000/182";
    protected $doiJournal = "https://doi.org/10.1590/1413-81232020256.1.10792020";
    protected $logo = 'tests' . DIRECTORY_SEPARATOR . "logo_noAlphaChannel.png";
    protected $checklist = array("A submissão não foi publicado anteriormente.", "As URLs das referências foram fornecidas.");
    protected $locale = "pt_BR";
    protected $title = "Assim Falou Zaratustra";
    protected $authors = "Cleide Silva; João Carlos";
    protected $submissionDate = "31/06/2020";
    protected $publicationDate = "02/07/2020";
    protected $translator;

    protected function setUp(): void {
        $this->translator = new TranslatorForTests();
        $this->pathOfTestPdf = "tests" . DIRECTORY_SEPARATOR . "testOnePage.pdf";
        $this->copyOfTestPdfToRestore = "tests" . DIRECTORY_SEPARATOR . "testOnePage_copy.pdf";
        copy($this->pathOfTestPdf, $this->copyOfTestPdfToRestore);
        $this->pdfComoTexto = "tests" . DIRECTORY_SEPARATOR . "testOnePage.txt";

        $this->pathOfTestPdf2 = "tests" . DIRECTORY_SEPARATOR . "testTwoPages.pdf";
        $this->copyOfTestPdfToRestore2 = "tests" . DIRECTORY_SEPARATOR . "testTwoPages_copy.pdf";
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
}
?>