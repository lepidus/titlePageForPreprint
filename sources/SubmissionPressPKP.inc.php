<?php
class SubmissionPressPKP implements SubmissionPress {

    private $logoForTitlePage;
    private $submission;
    private $translator;

    public function __construct(string $logoForTitlePage, SubmissionModel $submission, Translator $translator) {
        $this->logoForTitlePage = $logoForTitlePage;
        $this->submission = $submission;
        $this->translator = $translator;
    }

    public function insertInDB($id, $json){
        $submissionFileDao = DAORegistry::getDAO('SubmissionFileDAO');
        $submissionFile = $submissionFileDao->getById($id);

        Services::get('submissionFile')->edit($submissionFile, [
            'folhaDeRosto' => 'sim',
            'revisoes' => $json
        ], Application::get()->getRequest());
    }

    private function verifyInDB($titlePage, $galley, $pdf) {
        $submissionFileDao = DAORegistry::getDAO('SubmissionFileDAO');

        $submissionFile = $submissionFileDao->getById($galley->submissionFileId);
        $revisionId = $galley->revisionId;

        $hasTitlePage = $submissionFile->getData('folhaDeRosto');
        $revisions = ($submissionFile->getData('revisoes')) ? json_decode($submissionFile->getData('revisoes')) : array();

        if($hasTitlePage == 'sim') {
            $titlePage->remove($pdf);
        }
        else {
            $titlePage->addDocumentHeader($pdf);
            $titlePage->addChecklistPage($pdf);
            array_push($revisions, $revisionId);
        }

        return json_encode($revisions);
    }

    public function insertTitlePage(): void {
        foreach($this->submission->getGalleys() as $galley) {
            $titlePage = new TitlePage($this->submission, $this->logoForTitlePage, $galley->locale, $this->translator);
            $pdfPath = \Config::getVar('files', 'files_dir') . DIRECTORY_SEPARATOR . $galley->file;

            if (Pdf::isPdf($pdfPath)) {
                $pdf = new Pdf($pdfPath);
                $submissionFileId = $galley->submissionFileId;
                $revisionsJSON = $this->verifyInDB($titlePage, $galley, $pdf);
                $titlePage->insert($pdf);
                $this->insertInDB($submissionFileId, $revisionsJSON);
            }
        }   
    }
}