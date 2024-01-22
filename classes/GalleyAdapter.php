<?php

namespace APP\plugins\generic\titlePageForPreprint\classes;

use PKP\config\Config;

class GalleyAdapter
{
    public $file;
    public $locale;
    public $submissionFileId;
    public $revisionId;

    public function __construct(string $filePath, string $locale, int $submissionFileId, int $revisionId)
    {
        $this->file = $filePath;
        $this->locale = $locale;
        $this->submissionFileId = $submissionFileId;
        $this->revisionId = $revisionId;
    }

    public function getFullFilePath(): string
    {
        return \Config::getVar('files', 'files_dir') . DIRECTORY_SEPARATOR . $this->file;
    }
}
