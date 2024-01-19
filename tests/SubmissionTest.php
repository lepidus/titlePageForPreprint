<?php

use PKP\tests\PKPTestCase;
use APP\plugins\generic\titlePageForPreprint\classes\SubmissionModel;

class SubmissionTest extends PKPTestCase
{
    private $title = 'Uma aventura em mundo imaginario';
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
    private $galleys = array();

    private function getSubmissionForTests()
    {
        return new SubmissionModel($this->title, $this->status, $this->doi, $this->doiJournal, $this->authors, $this->submissionDate, $this->publicationDate, $this->endorserName, $this->endorserOrcid, $this->version, $this->versionJustification, $this->galleys);
    }

    public function testHasSubmissionTitle(): void
    {
        $submission = $this->getSubmissionForTests();
        $this->assertEquals($this->title, $submission->getTitle());
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
        $submission = new SubmissionModel($this->title, $this->status, null, $this->doiJournal, $this->authors, $this->submissionDate, $this->publicationDate, $this->endorserName, $this->endorserOrcid, $this->version, $this->versionJustification, $this->galleys);
        $this->assertEquals("Not informed", $submission->getDOI());
    }

    public function testHasDoiJournal(): void
    {
        $submission = $this->getSubmissionForTests();
        $this->assertEquals($this->doiJournal, $submission->getJournalDOI());
    }

    public function testDoiJournalNotInformed(): void
    {
        $submission = new SubmissionModel($this->title, $this->status, $this->doi, null, $this->authors, $this->submissionDate, $this->publicationDate, $this->endorserName, $this->endorserOrcid, $this->version, $this->versionJustification, $this->galleys);
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
}
