<?php

use PKP\tests\PKPTestCase;
use APP\plugins\generic\titlePageForPreprint\classes\SubmissionModel;

class SubmissionTest extends PKPTestCase
{
    private $titlePt = 'Uma aventura em mundo imaginario';
    private $titleEn = 'An adventure in an imaginary world';
    private $status = "publication.relation.none";
    private $doi = "10.1000/182";
    private $doiJournal = "https://doi.org/10.1590/1413-81232020256.1.10792020";
    private $authors = "Clarice Linspector, Atila Iamarino";
    private $submissionDate = "10/06/2020";
    private $publicationDate = "12/06/2020";
    private $version = "1";
    private $endorserName = 'Carl Sagan';
    private $endorserOrcid = 'https://orcid.org/0123-4567-89AB-CDEF';
    private $versionJustification = 'Nova versão criada para corrigir erros de ortografia';
    private $isTranslation = false;
    private $citation = 'Lispector, C. & Iamarino, A. (2024). An adventure in an imaginary world. Public Knowledge Preprint Server';
    private $galleys = array();

    private function getSubmissionForTests()
    {
        $submission = new SubmissionModel();
        $submission->setAllData([
            'title' => [
                'pt_BR' => $this->titlePt,
                'en' => $this->titleEn
            ],
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
            'galleys' => $this->galleys
        ]);

        return $submission;
    }

    public function testHasSubmissionTitle(): void
    {
        $submission = $this->getSubmissionForTests();
        $this->assertEquals($this->titlePt, $submission->getTitle('pt_BR'));
        $this->assertEquals($this->titleEn, $submission->getTitle('en'));
    }

    public function testHasSubmissionStatus(): void
    {
        $submission = $this->getSubmissionForTests();
        $this->assertEquals($this->status, $submission->getStatus());
    }

    public function testHasDoi(): void
    {
        $submission = $this->getSubmissionForTests();
        $this->assertEquals($this->doi, $submission->getDOI());
    }

    public function testDoiNotInformed(): void
    {
        $submission = $this->getSubmissionForTests();
        $submission->unsetData('doi');

        $this->assertEquals("Not informed", $submission->getDOI());
    }

    public function testHasDoiJournal(): void
    {
        $submission = $this->getSubmissionForTests();
        $this->assertEquals($this->doiJournal, $submission->getJournalDOI());
    }

    public function testDoiJournalNotInformed(): void
    {
        $submission = $this->getSubmissionForTests();
        $submission->unsetData('doiJournal');

        $this->assertEquals("Not informed", $submission->getJournalDOI());
    }

    public function testHasAuthors(): void
    {
        $submission = $this->getSubmissionForTests();
        $this->assertEquals($this->authors, $submission->getAuthors());
    }

    public function testHasGalleys(): void
    {
        $submission = $this->getSubmissionForTests();
        $this->assertEquals($this->galleys, $submission->getGalleys());
    }

    public function testSubmissionDate(): void
    {
        $submission = $this->getSubmissionForTests();
        $this->assertEquals($this->submissionDate, $submission->getSubmissionDate());
    }

    public function testPublicationDate(): void
    {
        $submission = $this->getSubmissionForTests();
        $this->assertEquals($this->publicationDate, $submission->getPublicationDate());
    }

    public function testHasVersionNumber(): void
    {
        $submission = $this->getSubmissionForTests();
        $this->assertEquals($this->version, $submission->getVersion());
    }

    public function testHasEndorserName(): void
    {
        $submission = $this->getSubmissionForTests();
        $this->assertEquals($this->endorserName, $submission->getEndorserName());
    }

    public function testHasEndorserOrcid(): void
    {
        $submission = $this->getSubmissionForTests();
        $this->assertEquals($this->endorserOrcid, $submission->getEndorserOrcid());
    }

    public function testHasVersionJustification(): void
    {
        $submission = $this->getSubmissionForTests();
        $this->assertEquals($this->versionJustification, $submission->getVersionJustification());
    }

    public function testIsTranslation(): void
    {
        $submission = $this->getSubmissionForTests();
        $this->assertFalse($submission->getIsTranslation());

        $submission->setData('isTranslation', true);
        $this->assertTrue($submission->getIsTranslation());
    }

    public function testHasCitation(): void
    {
        $submission = $this->getSubmissionForTests();
        $this->assertEquals($this->citation, $submission->getCitation());
    }
}
