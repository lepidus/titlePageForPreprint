<?php
class PrensaDeSubmissoes {

    private $logoParaFolhaDeRosto;
    private $checklist;
    private $submissão;

    public function __construct(string $logoParaFolhaDeRosto, array $checklist, Submissao $submissão) {
        $this->logoParaFolhaDeRosto = $logoParaFolhaDeRosto;
        $this->checklist = $checklist;
        $this->submissão = $submissão;
    }

    public function inserirFolhasDeRosto(): void {
       foreach($this->submissão->composições as $composição){
           $folhaDeRosto = new FolhaDeRosto($this->submissão->status, $this->submissão->doi, $this->logoParaFolhaDeRosto, $this->checklist);

           if (Pdf::éPdf($composição)) {
               $pdf = new Pdf($composição);
               $folhaDeRosto->inserir($pdf);
           }
       }
       
    }
}