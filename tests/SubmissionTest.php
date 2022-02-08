<?php

import('lib.pkp.tests.PKPTestCase');
import('plugins.generic.titlePageForPreprint.classes.SubmissionModel');

class SubmissionTest extends PKPTestCase {

    private $status = "publication.relation.none";
    private $doi = "10.1000/182";
    private $doiJournal = "https://doi.org/10.1590/1413-81232020256.1.10792020";
    private $authors = "Clarice Linspector, Atila Iamarino";
    private $submissionDate = "10/06/2020";
    private $publicationDate = "12/06/2020";
    private $version = "1";
    private $galleys = array();
    
    private function getSubmissionForTests() {
        return new SubmissionModel($this->status, $this->doi, $this->doiJournal, $this->authors, $this->submissionDate, $this->publicationDate, $this->version, $this->galleys);
    }

    public function testHasSubmissionStatus(): void {
        $submission = $this->getSubmissionForTests();
        $this->assertEquals($this->status, $submission->getStatus());
    }
    
    public function testHasDoi(): void {
        $submission = $this->getSubmissionForTests();
        $this->assertEquals($this->doi, $submission->getDOI());
    }

    public function testDoiNotInformed(): void {
        $submission = new SubmissionModel($this->status, null, $this->doiJournal, $this->authors, $this->submissionDate, $this->publicationDate, $this->version, $this->galleys);
        $this->assertEquals("Not informed", $submission->getDOI());
    }

    public function testHasDoiJournal(): void {
        $submission = $this->getSubmissionForTests();
        $this->assertEquals($this->doiJournal, $submission->getJournalDOI());
    }

    public function testDoiJournalNotInformed(): void {
        $submission = new SubmissionModel($this->status, $this->doi, null, $this->authors, $this->submissionDate, $this->publicationDate, $this->version, $this->galleys);
        $this->assertEquals("Not informed", $submission->getJournalDOI());
    }

    public function testHasAuthors(): void {
        $submission = $this->getSubmissionForTests();
        $this->assertEquals($this->authors, $submission->getAuthors());
    }

    public function testHasGalleys(): void {
        $submission = $this->getSubmissionForTests();
        $this->assertEquals($this->galleys, $submission->getGalleys());
    }

    public function testSubmissionDate(): void {
        $submission = $this->getSubmissionForTests();
        $this->assertEquals($this->submissionDate, $submission->getSubmissionDate());
    }

    public function testPublicationDate(): void {
        $submission = $this->getSubmissionForTests();
        $this->assertEquals($this->publicationDate, $submission->getPublicationDate());
    }

    public function testHasVersionNumber(): void {
        $submission = $this->getSubmissionForTests();
        $this->assertEquals($this->version, $submission->getversion());
    }
}
?>