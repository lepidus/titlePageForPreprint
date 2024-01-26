<?php

namespace APP\plugins\generic\titlePageForPreprint\classes;

use APP\facades\Repo;
use APP\plugins\generic\titlePageForPreprint\classes\Pdf;
use APP\plugins\generic\titlePageForPreprint\classes\SubmissionModel;
use APP\plugins\generic\titlePageForPreprint\classes\TitlePage;

class SubmissionPress
{
    private $submission;
    private $checklist;
    private $logoForTitlePage;

    public function __construct(SubmissionModel $submission, array $checklist, string $logoForTitlePage)
    {
        $this->submission = $submission;
        $this->checklist = $checklist;
        $this->logoForTitlePage = $logoForTitlePage;
    }

    private function galleyHasTitlePage($galley)
    {
        $submissionFile = Repo::submissionFile()->get($galley->submissionFileId);

        $hasTitlePage = $submissionFile->getData('folhaDeRosto');
        return $hasTitlePage == 'sim';
    }

    public function insertTitlePage($submissionFileUpdater): void
    {
        foreach ($this->submission->getGalleys() as $galley) {
            $titlePage = new TitlePage($this->submission, $this->checklist, $this->logoForTitlePage, $galley->locale);
            $pdfPath = $galley->getFullFilePath();

            if (Pdf::isPdf($pdfPath)) {
                $pdf = new Pdf($pdfPath);
                $submissionFileId = $galley->submissionFileId;

                $hasTitlePage = $this->galleyHasTitlePage($galley);

                try {
                    $hasTitlePage ? $titlePage->updateTitlePage($pdf) : $titlePage->insertTitlePageFirstTime($pdf);
                    $submissionFileUpdater->updateRevisions($submissionFileId, $galley->revisionId, $hasTitlePage);
                } catch (\Exception $e) {
                    error_log('Caught exception: ' .  $e->getMessage());
                }
            }
        }
    }
}
