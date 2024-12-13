<?php

namespace APP\plugins\generic\titlePageForPreprint\classes;

class SubmissionModel
{
    private $status;
    private $doi;
    private $doiJournal;
    private $galleys;
    private $authors;
    private $submissionDate;
    private $publicationDate;
    private $version;
    private $versionJustification;
    private $endorserName;
    private $endorserOrcid;
    private $isTranslation;
    private $citation;

    public function __construct(array $title, string $status, $doi, $doiJournal, string $authors, string $submissionDate, string $publicationDate, $endorserName, $endorserOrcid, string $version, $versionJustification, bool $isTranslation, string $citation, array $galleys = null)
    {
        $this->title = $title;
        $this->status = $status;
        $this->doi = ((empty($doi)) ? ("Not informed") : ($doi));
        $this->doiJournal = ((empty($doiJournal)) ? ("Not informed") : ($doiJournal));
        $this->authors = $authors;
        $this->galleys = $galleys;
        $this->submissionDate = $submissionDate;
        $this->publicationDate = $publicationDate;
        $this->endorserName = $endorserName;
        $this->endorserOrcid = $endorserOrcid;
        $this->version = $version;
        $this->versionJustification = $versionJustification;
        $this->isTranslation = $isTranslation;
        $this->citation = $citation;
    }

    public function getTitle(string $locale): string
    {
        if (isset($this->title[$locale])) {
            return $this->title[$locale];
        }

        return '';
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

    public function getVersion(): string
    {
        return $this->version;
    }

    public function getEndorserName(): ?string
    {
        return $this->endorserName;
    }

    public function getEndorserOrcid(): ?string
    {
        return $this->endorserOrcid;
    }

    public function getVersionJustification(): ?string
    {
        return $this->versionJustification;
    }

    public function setIsTranslation(bool $isTranslation)
    {
        $this->isTranslation = $isTranslation;
    }

    public function getIsTranslation(): bool
    {
        return $this->isTranslation;
    }

    public function getCitation(): ?string
    {
        return $this->citation;
    }
}
