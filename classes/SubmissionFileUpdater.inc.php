<?php

class SubmissionFileUpdater {

    public function updateRevisions($submissionFileId, $newRevisionId, $hasTitlePage){
        $submissionFileDao = DAORegistry::getDAO('SubmissionFileDAO');
        $submissionFile = $submissionFileDao->getById($submissionFileId);
        $revisions = !is_null($submissionFile->getData('revisoes')) ? json_decode($submissionFile->getData('revisoes')) : array();
        
        if(!$hasTitlePage) array_push($revisions, $newRevisionId);

        Services::get('submissionFile')->edit($submissionFile, [
            'folhaDeRosto' => 'sim',
            'revisoes' => json_encode($revisions)
        ], Application::get()->getRequest());
    }
}