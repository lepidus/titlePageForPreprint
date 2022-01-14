<?php
import ('plugins.generic.titlePageForPreprint.classes.SubmissionModel');
import ('plugins.generic.titlePageForPreprint.classes.SubmissionPress');
import ('plugins.generic.titlePageForPreprint.classes.Translator');
import ('plugins.generic.titlePageForPreprint.classes.TitlePage');
import ('plugins.generic.titlePageForPreprint.classes.Pdf');

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
                $titlePage->insertTitlePageFirstTime($pdf);
            }
        }   
    }
}