<?php
class SubmissionModel {

    private $status;
    private $doi;
    private $doiJournal;
    private $galleys;
    private $authors;
    private $submissionDate;
    private $publicationDate;

    public function __construct(string $status, $doi, $doiJournal, string $authors, string $submissionDate, string $publicationDate, array $galleys = null) {
        $this->status = $status;
        $this->doi = ((empty($doi)) ? ("Not informed") : ($doi));
        $this->doiJournal = ((empty($doiJournal)) ? ("Not informed") : ($doiJournal));
        $this->authors = $authors;
        $this->galleys = $galleys;
        $this->submissionDate = $submissionDate;
        $this->publicationDate = $publicationDate;
    }

    public function getStatus(): string {
        return $this->status;
    }

    public function getDOI(): string {
        return $this->doi;
    }

    public function getJournalDOI(): string {
        return $this->doiJournal;
    }

    public function getAuthors(): string {
        return $this->authors;
    }

    public function getGalleys(): array {
        return $this->galleys;
    }

    public function getSubmissionDate(): string {
        return $this->submissionDate;
    }

    public function getPublicationDate(): string {
        return $this->publicationDate;
    }

}