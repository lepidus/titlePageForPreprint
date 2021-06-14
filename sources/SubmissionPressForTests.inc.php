<?php
import ('plugins.generic.titlePageForPreprint.sources.SubmissionModel');
import ('plugins.generic.titlePageForPreprint.sources.SubmissionPress');
import ('plugins.generic.titlePageForPreprint.sources.Translator');
import ('plugins.generic.titlePageForPreprint.sources.TitlePage');
import ('plugins.generic.titlePageForPreprint.sources.Pdf');

class SubmissionPressForTests implements SubmissionPress {

    private $logoForTitlePage;
    private $submission;
    private $translator;

    public function __construct(string $logoForTitlePage, SubmissionModel $submission, Translator $translator) {
        $this->logoForTitlePage = $logoForTitlePage;
        $this->submission = $submission;
        $this->translator = $translator;
    }

    public function insertTitlePage(): void {
       foreach($this->submission->getGalleys() as $galley){
           $titlePage = new TitlePage($this->submission, $this->logoForTitlePage, $galley->locale, $this->translator);

           if (Pdf::isPdf($galley->file)) {               
               $pdf = new Pdf($galley->file);
               $titlePage->insert($pdf);
           }
       }   
    }
}