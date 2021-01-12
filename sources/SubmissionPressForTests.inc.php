<?php
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
       foreach($this->submission->getCompositions() as $composition){
           $titlePage = new TitlePage($this->submission, $this->logoForTitlePage, $composition->locale, $this->translator);

           if (Pdf::isPdf($composition->file)) {               
               $pdf = new Pdf($composition->file);
               $titlePage->insert($pdf);
           }
       }   
    }
}