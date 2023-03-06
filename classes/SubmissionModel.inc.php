<?php

class SubmissionModel
{
    private $status;
    private $doi;
    private $doiJournal;
    private $galleys;
    private $authors;
    private $submissionDate;
    private $publicationDate;
    private $viewUrl;
    private $version;

    public function __construct(string $status, $doi, $doiJournal, string $authors, string $submissionDate, string $publicationDate, string $viewUrl, string $version, array $galleys = null)
    {
        $this->status = $status;
        $this->doi = ((empty($doi)) ? ("Not informed") : ($doi));
        $this->doiJournal = ((empty($doiJournal)) ? ("Not informed") : ($doiJournal));
        $this->authors = $authors;
        $this->galleys = $galleys;
        $this->submissionDate = $submissionDate;
        $this->publicationDate = $publicationDate;
        $this->viewUrl = $viewUrl;
        $this->version = $version;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function getDOI(): string
    {
        return $this->doi;
    }

    public function getJournalDOI(): string
    {
        return $this->doiJournal;
    }

    public function getAuthors(): string
    {
        return $this->authors;
    }

    public function getGalleys(): array
    {
        return $this->galleys;
    }

    public function getSubmissionDate(): string
    {
        return $this->submissionDate;
    }

    public function getPublicationDate(): string
    {
        return $this->publicationDate;
    }

    public function getViewUrl(): string
    {
        return $this->viewUrl;
    }

    public function getVersion(): string
    {
        return $this->version;
    }
}
