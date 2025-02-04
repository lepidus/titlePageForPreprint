<?php

namespace APP\plugins\generic\titlePageForPreprint\classes;

use PKP\core\DataObject;

class SubmissionModel extends DataObject
{
    public function getTitle(string $locale): string
    {
        $localizedTitle = $this->getData('title', $locale);

        if (!is_null($localizedTitle)) {
            return $localizedTitle;
        }

        return '';
    }

    public function getStatus(): string
    {
        return $this->getData('status');
    }

    public function getDOI(): string
    {
        $doi = $this->getData('doi');
        return empty($doi) ? ("Not informed") : $doi;
    }

    public function getJournalDOI(): string
    {
        $doiJournal = $this->getData('doiJournal');
        return empty($doiJournal) ? ("Not informed") : $doiJournal;
    }

    public function getAuthors(): string
    {
        return $this->getData('authors');
    }

    public function getGalleys(): array
    {
        $galleys = $this->getData('galleys');
        return is_null($galleys) ? [] : $galleys;
    }

    public function getSubmissionDate(): string
    {
        return $this->getData('submissionDate');
    }

    public function getPublicationDate(): string
    {
        return $this->getData('publicationDate');
    }

    public function getVersion(): string
    {
        return $this->getData('version');
    }

    public function getEndorserName(): ?string
    {
        return $this->getData('endorserName');
    }

    public function getEndorserOrcid(): ?string
    {
        return $this->getData('endorserOrcid');
    }

    public function getVersionJustification(): ?string
    {
        return $this->getData('versionJustification');
    }

    public function setIsTranslation(bool $isTranslation)
    {
        $this->setData('isTranslation', $isTranslation);
    }

    public function getIsTranslation(): bool
    {
        return $this->getData('isTranslation') ?? false;
    }

    public function getCitation(): ?string
    {
        return $this->getData('citation');
    }
}
