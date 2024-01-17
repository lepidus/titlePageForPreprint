<?php

namespace APP\plugins\generic\titlePageForPreprint\tests;

use PKP\tests\PKPTestCase;
use APP\plugins\generic\titlePageForPreprint\classes\Translator;

define('TESTS_DIRECTORY', (dirname(__FILE__)).DIRECTORY_SEPARATOR);
define('ASSETS_DIRECTORY', 'assets'.DIRECTORY_SEPARATOR);
define('OUTPUT_DIRECTORY', DIRECTORY_SEPARATOR.'tmp'.DIRECTORY_SEPARATOR);

class PdfHandlingTest extends PKPTestCase
{
    protected $status = "publication.relation.none";
    protected $doi = "10.1000/182";
    protected $doiJournal = "https://doi.org/10.1590/1413-81232020256.1.10792020";
    protected $logo = TESTS_DIRECTORY.ASSETS_DIRECTORY."logo_noAlphaChannel.png";
    protected $checklist = array("A submissão não foi publicado anteriormente.", "As URLs das referências foram fornecidas.");
    protected $locale = "pt_BR";
    protected $title = "Assim Falou Zaratustra-àáâã";
    protected $authors = "Cleide Silva; João Carlos";
    protected $submissionDate = "31/06/2020";
    protected $publicationDate = "02/07/2020";
    protected $endorserName = 'Carl Sagan';
    protected $endorserOrcid = 'https://orcid.org/0123-4567-89AB-CDEF';
    protected $version = "1";
    protected $versionJustification = 'Nova versão criada para corrigir erros de ortografia';
    protected $translator;

    protected function setUp(): void
    {
        parent::setUp();

        $this->registerMockSubmissionFileDAO();

        $this->translator = $this->buildMockTranslator();
        $this->pathOfTestPdf = TESTS_DIRECTORY.ASSETS_DIRECTORY."testOnePage.pdf";
        $this->copyOfTestPdfToRestore = TESTS_DIRECTORY.ASSETS_DIRECTORY."testOnePage_copy.pdf";
        copy($this->pathOfTestPdf, $this->copyOfTestPdfToRestore);
        $this->pdfAsText = TESTS_DIRECTORY.ASSETS_DIRECTORY."testOnePage.txt";

        $this->pathOfTestPdf2 = TESTS_DIRECTORY.ASSETS_DIRECTORY."testTwoPages.pdf";
        $this->copyOfTestPdfToRestore2 = TESTS_DIRECTORY.ASSETS_DIRECTORY."testTwoPages_copy.pdf";
        copy($this->pathOfTestPdf2, $this->copyOfTestPdfToRestore2);
    }

    protected function getMockedDAOs()
    {
        return array('SubmissionFileDAO');
    }

    private function registerMockSubmissionFileDAO(): void
    {
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

    private function buildMockTranslator(): Translator
    {
        $mockContext = $this->getMockBuilder(Context::class)
            ->setMethods(array('getData'))
            ->getMock();

        $mockPublication = $this->getMockBuilder(Publication::class)
            ->setMethods(array('getLocalizedTitle'))
            ->getMock();

        $mockTranslator = $this->getMockBuilder(Translator::class)
            ->setConstructorArgs(array($mockContext, $mockPublication))
            ->setMethods(array('translate', 'getTranslatedChecklist', 'getTranslatedTitle'))
            ->getMock();

        $mockTranslator->expects($this->any())
                       ->method('getTranslatedChecklist')
                       ->will($this->returnValue($this->checklist));

        $mockTranslator->expects($this->any())
                       ->method('translate')
                       ->will($this->returnCallback(array($this, 'getTranslation')));

        $mockTranslator->expects($this->any())
                       ->method('getTranslatedTitle')
                       ->will($this->returnValue($this->getLanguageMap($this->locale)["title"]));

        return $mockTranslator;
    }

    public function getTranslation($key, $locale, $params): string
    {
        $language = $this->getLanguageMap($locale);
        $translatedString = $language[$key];
        if ($params) {
            foreach ($params as $key => $value) {
                $translatedString = strtr($translatedString, ['{!' . $key . '}' => $value]);
            }
        }
        return $translatedString;
    }

    private function getLanguageMap($locale): array
    {
        $languageMap["en_US"] = [
            "publication.relation.none" => "Preprint has not been submitted for publication",
            "publication.relation.submitted" => "Preprint has been submitted for publication in journal",
            "publication.relation.published" => "Preprint has been published in a journal as an article",
            "metadata.property.displayName.doi" => "DOI",
            "plugins.generic.titlePageForPreprint.publicationStatus" => "Publication status",
            "plugins.generic.titlePageForPreprint.emptyPublicationStatus" => "Not informed by the submitting author",
            "plugins.generic.titlePageForPreprint.checklistLabel" => "This preprint was submitted under the following conditions",
            "plugins.generic.titlePageForPreprint.submissionDate" => "Submitted on: {!subDate}",
            "plugins.generic.titlePageForPreprint.publicationDate" => "Posted on: {!postDate} (version {!version}",
            "plugins.generic.titlePageForPreprint.dateFormat" => "(YYYY-MM-DD)",
            "plugins.generic.titlePageForPreprint.headerText" => "SciELO Preprints - this preprint has not been peer reviewed",
            "plugins.generic.titlePageForPreprint.endorsement" => "The moderation of this preprint received the endorsement of:<br>{!endorserName} (ORCID: <a href=\"{!endorserOrcid}\">{!endorserOrcid}</a>)",
            "plugins.generic.titlePageForPreprint.versionJustification" => "Version justification",
            "item1CheckList" => "The submission has not been previously published.",
            "item2CheckList" => "Where available, URLs for the references have been provided.",
            "title" => "So spoke Zaratustra"
        ];

        $languageMap["pt_BR"] = [
            "publication.relation.none" => "O preprint não foi submetido para publicação",
            "publication.relation.submitted" => "O preprint foi submetido para publicação em um periódico",
            "publication.relation.published" => "O preprint foi publicado em um periódico como um artigo",
            "metadata.property.displayName.doi" => "DOI",
            "plugins.generic.titlePageForPreprint.publicationStatus" => "Estado da publicação",
            "plugins.generic.titlePageForPreprint.emptyPublicationStatus" => "Não informado pelo autor submissor",
            "plugins.generic.titlePageForPreprint.checklistLabel" => "Este preprint foi submetido sob as seguintes condições",
            "plugins.generic.titlePageForPreprint.submissionDate" => "Submetido em: {!subDate}",
            "plugins.generic.titlePageForPreprint.publicationDate" => "Postado em: {!postDate} (versão {!version})",
            "plugins.generic.titlePageForPreprint.dateFormat" => "(AAAA-MM-DD)",
            "plugins.generic.titlePageForPreprint.headerText" => "SciELO Preprints - este preprint não foi revisado por pares",
            "plugins.generic.titlePageForPreprint.endorsement" => "A moderação deste preprint recebeu o endosso de:<br>{!endorserName} (ORCID: <a href=\"{!endorserOrcid}\">{!endorserOrcid}</a>)",
            "plugins.generic.titlePageForPreprint.versionJustification" => "Justificativa da versão",
            "item1CheckList" => "A submissão não foi publicado anteriormente.",
            "item2CheckList" => "As URLs das referências foram fornecidas.",
            "title" => "Assim Falou Zaratustra-àáâã"
        ];

        return $languageMap[$locale];
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

    public function testDummy(): void
    {
        $this->assertTrue(true);
    }
}
