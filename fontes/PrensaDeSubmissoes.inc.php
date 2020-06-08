<?php
class PrensaDeSubmissoes {

    private $logoParaFolhaDeRosto;
    private $checklist;
    private $submissão;
    private $tradutor;

    public function __construct(string $logoParaFolhaDeRosto, array $checklist, Submissao $submissão, Tradutor $tradutor) {
        $this->logoParaFolhaDeRosto = $logoParaFolhaDeRosto;
        $this->checklist = $checklist;
        $this->submissão = $submissão;
        $this->tradutor = $tradutor;
    }

    public function inserirFolhasDeRosto(): void {
       foreach($this->submissão->composições as $composição){
           $folhaDeRosto = new FolhaDeRosto($this->submissão->status, $this->submissão->doi, $this->logoParaFolhaDeRosto, $this->checklist, $composição->locale, $this->tradutor);

           if (Pdf::éPdf($composição->arquivo)) {
               $pdf = new Pdf($composição->arquivo);
               $folhaDeRosto->inserir($pdf);
           }
       }
       
    }
}