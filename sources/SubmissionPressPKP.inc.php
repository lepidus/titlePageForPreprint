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
        $fileSettingsDAO = new SubmissionFileSettingsDAO(); 
        DAORegistry::registerDAO('SubmissionFileSettingsDAO', $fileSettingsDAO);
        
        $fileSettingsDAO->updateSetting($id, 'titlePage', 'yes', 'string', false);
        $fileSettingsDAO->updateSetting($id, 'revisions', $json, 'JSON', false);
    }

    private function verifyInDB($titlePage, $composition) {
        $fileSettingsDAO = new SubmissionFileSettingsDAO(); 
        DAORegistry::registerDAO('SubmissionFileSettingsDAO', $fileSettingsDAO);

        $pdf = new Pdf($composition->file);
        $id = $composition->identifier;
        $revision = $composition->revision;

        $setting = $fileSettingsDAO->getSetting($id, 'titlePage');
        $revisions = '[]';

        if($setting == 'yes') {     
            $revisions = $fileSettingsDAO->getSetting($id, 'revisions');
            $titlePage->remove($pdf);
        }
        else {
            $titlePage->addDocumentHeader($pdf);
        }

        $revisions = json_decode($revisions);
        array_push($revisions, $revision);
        $revisionsJSON = json_encode($revisions);

        return $revisionsJSON;
    }

    public function insertTitlePage(): void {
        foreach($this->submission->getCompositions() as $composition) {
            $titlePage = new TitlePage($this->submission, $this->logoForTitlePage, $composition->locale, $this->translator);

            if (Pdf::isPdf($composition->file)) {
                $pdf = new Pdf($composition->file);
                $id = $composition->identifier;
                $revisionsJSON = $this->verifyInDB($titlePage, $composition);
                $titlePage->insert($pdf);
                $this->insertInDB($id, $revisionsJSON);
            }
        }   
    }
}