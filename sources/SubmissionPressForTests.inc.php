<?php
class SubmissionPressForTests implements PrensaDeSubmissoes {

    private $logoForTitlePage;
    private $submission;
    private $translator;

    public function __construct(string $logoForTitlePage, Submission $submission, Translator $translator) {
        $this->logoForTitlePage = $logoForTitlePage;
        $this->submission = $submission;
        $this->translator = $translator;
    }

    public function insertTitlePages(): void {
       foreach($this->submission->getComposition() as $composition){
           $titlePage = new TitlePage($this->submission, $this->logoForTitlePage, $composition->locale, $this->translator);

           if (Pdf::isPdf($composition->file)) {               
               $pdf = new Pdf($composition->file);
               $titlePage->insert($pdf);
           }
       }   
    }
}