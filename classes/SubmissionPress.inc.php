<?php
import ('plugins.generic.titlePageForPreprint.classes.Pdf');
import ('plugins.generic.titlePageForPreprint.classes.SubmissionModel');
import ('plugins.generic.titlePageForPreprint.classes.Translator');
import ('plugins.generic.titlePageForPreprint.classes.TitlePage');

class SubmissionPress {

    private $logoForTitlePage;
    private $submission;
    private $translator;

    public function __construct(string $logoForTitlePage, SubmissionModel $submission, Translator $translator) {
        $this->logoForTitlePage = $logoForTitlePage;
        $this->submission = $submission;
        $this->translator = $translator;
    }

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

    private function galleyHasTitlePage($galley) {
        $submissionFileDao = DAORegistry::getDAO('SubmissionFileDAO');
        $submissionFile = $submissionFileDao->getById($galley->submissionFileId);

        $hasTitlePage = $submissionFile->getData('folhaDeRosto');
        return $hasTitlePage == 'sim';
    }

    public function insertTitlePage(): void {
        foreach($this->submission->getGalleys() as $galley) {
            $titlePage = new TitlePage($this->submission, $this->logoForTitlePage, $galley->locale, $this->translator);
            $pdfPath = $galley->getFullFilePath();

            if (Pdf::isPdf($pdfPath)) {
                $pdf = new Pdf($pdfPath);
                $submissionFileId = $galley->submissionFileId;

                $hasTitlePage = $this->galleyHasTitlePage($galley);

                try {
                    $hasTitlePage ? $titlePage->updateTitlePage($pdf) : $titlePage->insertTitlePageFirstTime($pdf);
                    $this->updateRevisions($submissionFileId, $galley->revisionId, $hasTitlePage);
                } catch(Exception $e) {
                    error_log('Caught exception: ' .  $e->getMessage());
                }
            }
        }   
    }
}