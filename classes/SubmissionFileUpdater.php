<?php

namespace APP\plugins\generic\titlePageForPreprint\classes;

use APP\core\Application;
use APP\facades\Repo;

class SubmissionFileUpdater
{
    public function updateRevisions($submissionFileId, $newRevisionId, $hasTitlePage)
    {
        $submissionFile = Repo::submissionFile()->get($submissionFileId);
        $revisions = !is_null($submissionFile->getData('revisoes')) ? json_decode($submissionFile->getData('revisoes')) : array();

        if (!$hasTitlePage) {
            array_push($revisions, $newRevisionId);
        }

        Repo::submissionFile()->edit($submissionFile, [
            'folhaDeRosto' => 'sim',
            'revisoes' => json_encode($revisions)
        ]);
    }
}
