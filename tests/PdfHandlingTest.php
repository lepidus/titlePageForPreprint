<?php

namespace APP\plugins\generic\titlePageForPreprint\tests;

use PKP\tests\PKPTestCase;
use PKP\db\DAORegistry;
use PKP\submissionFile\SubmissionFile;
use APP\plugins\generic\titlePageForPreprint\classes\SubmissionModel;
use APP\plugins\generic\titlePageForPreprint\classes\Translator;

class PdfHandlingTest extends PKPTestCase
{
    public const TESTS_DIRECTORY = __DIR__ . DIRECTORY_SEPARATOR;
    public const ASSETS_DIRECTORY = 'assets' . DIRECTORY_SEPARATOR;
    public const OUTPUT_DIRECTORY = DIRECTORY_SEPARATOR . 'tmp' . DIRECTORY_SEPARATOR;

    protected $status = "publication.relation.none";
    protected $doi = "10.1000/182";
    protected $doiJournal = "https://doi.org/10.1590/1413-81232020256.1.10792020";
    protected $logo = self::TESTS_DIRECTORY . self::ASSETS_DIRECTORY . "logo_noAlphaChannel.png";
    protected $checklist = [
        "en" => ["The submission has not been previously published.", "Where available, URLs for the references have been provided."],
        "pt_BR" => ["A submissão não foi publicado anteriormente.", "As URLs das referências foram fornecidas."]
    ];
    protected $locale = "pt_BR";
    protected $title = [
        'pt_BR' => "Assim Falou Zaratustra-àáâã",
        'en' => 'Thus spoke Zarathustra'
    ];
    protected $authors = "Cleide Silva; João Carlos";
    protected $submissionDate = "31/06/2020";
    protected $publicationDate = "02/07/2020";
    protected $endorserName = 'Carl Sagan';
    protected $endorserOrcid = 'https://orcid.org/0123-4567-89AB-CDEF';
    protected $version = "1";
    protected $versionJustification = 'Nova versão criada para corrigir erros de ortografia';
    protected $isTranslation = false;
    protected $citation = 'Silva, C. & Carlos, J. (2024). Thus spoke Zarathustra. Public Knowledge Preprint Server';
    protected $dataStatement = [
        'Os dados de pesquisa estão disponíveis sob demanda, condição justificada no manuscrito'
    ];

    protected function setUp(): void
    {
        parent::setUp();

        $this->pathOfTestPdf = self::TESTS_DIRECTORY . self::ASSETS_DIRECTORY . "testOnePage.pdf";
        $this->copyOfTestPdfToRestore = self::TESTS_DIRECTORY . self::ASSETS_DIRECTORY . "testOnePage_copy.pdf";
        copy($this->pathOfTestPdf, $this->copyOfTestPdfToRestore);
        $this->pdfAsText = self::TESTS_DIRECTORY . self::ASSETS_DIRECTORY . "testOnePage.txt";

        $this->pathOfTestPdf2 = self::TESTS_DIRECTORY . self::ASSETS_DIRECTORY . "testTwoPages.pdf";
        $this->copyOfTestPdfToRestore2 = self::TESTS_DIRECTORY . self::ASSETS_DIRECTORY . "testTwoPages_copy.pdf";
        copy($this->pathOfTestPdf2, $this->copyOfTestPdfToRestore2);
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        $this->assertTrue(unlink($this->pathOfTestPdf));
        rename($this->copyOfTestPdfToRestore, $this->pathOfTestPdf);

        $this->assertTrue(unlink($this->pathOfTestPdf2));
        rename($this->copyOfTestPdfToRestore2, $this->pathOfTestPdf2);

        if (file_exists($this->pdfAsText)) {
            unlink($this->pdfAsText);
        }
    }

    protected function getSubmissionForTests(): SubmissionModel
    {
        $submission = new SubmissionModel();
        $submission->setAllData([
            'title' => $this->title,
            'status' => $this->status,
            'doi' => $this->doi,
            'doiJournal' => $this->doiJournal,
            'authors' => $this->authors,
            'submissionDate' => $this->submissionDate,
            'publicationDate' => $this->publicationDate,
            'endorserName' => $this->endorserName,
            'endorserOrcid' => $this->endorserOrcid,
            'version' => $this->version,
            'versionJustification' => $this->versionJustification,
            'isTranslation' => $this->isTranslation,
            'citation' => $this->citation,
            'dataStatement' => $this->dataStatement
        ]);

        return $submission;
    }

    public function testDummy(): void
    {
        $this->assertTrue(true);
    }
}
