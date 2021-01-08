<?php

interface SubmissionPress {
    public function __construct(string $logoForTitlePage, Submission $submission, Translator $translator);
    public function insertTitlePage();
}