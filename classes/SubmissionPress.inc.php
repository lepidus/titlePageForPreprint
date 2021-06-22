<?php

interface SubmissionPress {
    public function __construct(string $logoForTitlePage, SubmissionModel $submission, Translator $translator);
    public function insertTitlePage();
}