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
        
        $fileSettingsDAO->updateSetting($id, 'folhaDeRosto', 'sim', 'string', false);
        $fileSettingsDAO->updateSetting($id, 'revisoes', $json, 'JSON', false);
    }

    private function verifyInDB($titlePage, $galley) {
        $fileSettingsDAO = new SubmissionFileSettingsDAO(); 
        DAORegistry::registerDAO('SubmissionFileSettingsDAO', $fileSettingsDAO);

        $pdf = new Pdf($galley->file);
        $id = $galley->identifier;
        $revision = $galley->revision;

        $setting = $fileSettingsDAO->getSetting($id, 'folhaDeRosto');
        $revisions = '[]';

        if($setting == 'sim') {     
            $revisions = $fileSettingsDAO->getSetting($id, 'revisoes');
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
        foreach($this->submission->getGalleys() as $galley) {
            $titlePage = new TitlePage($this->submission, $this->logoForTitlePage, $galley->locale, $this->translator);

            if (Pdf::isPdf($galley->file)) {
                $pdf = new Pdf($galley->file);
                $id = $galley->identifier;
                $revisionsJSON = $this->verifyInDB($titlePage, $galley);
                $titlePage->insert($pdf);
                $this->insertInDB($id, $revisionsJSON);
            }
        }   
    }
}