<?php
class GalleyAdapter {

    public $file;
    public $locale;
    public $submissionFileId;
    public $revisionId;

    public function __construct(string $filePath, string $locale, int $submissionFileId, int $revisionId) {
        $this->file = $filePath;
        $this->locale = $locale;
        $this->submissionFileId = $submissionFileId;
        $this->revisionId = $revisionId;
    }
}