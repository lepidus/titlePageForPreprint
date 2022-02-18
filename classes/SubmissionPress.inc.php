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

    private function galleyHasTitlePage($galley) {
        $submissionFileDao = DAORegistry::getDAO('SubmissionFileDAO');
        $submissionFile = $submissionFileDao->getById($galley->submissionFileId);

        $hasTitlePage = $submissionFile->getData('folhaDeRosto');
        return $hasTitlePage == 'sim';
    }

    public function insertTitlePage($submissionFileUpdater): void {
        foreach($this->submission->getGalleys() as $galley) {
            $titlePage = new TitlePage($this->submission, $this->logoForTitlePage, $galley->locale, $this->translator);
            $pdfPath = $galley->getFullFilePath();

            if (Pdf::isPdf($pdfPath)) {
                $pdf = new Pdf($pdfPath);
                $submissionFileId = $galley->submissionFileId;

                $hasTitlePage = $this->galleyHasTitlePage($galley);

                try {
                    $hasTitlePage ? $titlePage->updateTitlePage($pdf) : $titlePage->insertTitlePageFirstTime($pdf);
                    $submissionFileUpdater->updateRevisions($submissionFileId, $galley->revisionId, $hasTitlePage);
                } catch(Exception $e) {
                    error_log('Caught exception: ' .  $e->getMessage());
                }
            }
        }   
    }
}