<?php
class PrensaDeSubmissoes {

    private $logoParaFolhaDeRosto;
    private $submissão;
    private $tradutor;

    public function __construct(string $logoParaFolhaDeRosto, Submissao $submissão, Tradutor $tradutor) {
        $this->logoParaFolhaDeRosto = $logoParaFolhaDeRosto;
        $this->submissão = $submissão;
        $this->tradutor = $tradutor;
    }

    public function inserirFolhasDeRosto(): void {
       foreach($this->submissão->composições as $composição){
           $folhaDeRosto = new FolhaDeRosto($this->submissão->status, $this->submissão->doi, $this->logoParaFolhaDeRosto, $composição->locale, $this->tradutor);

           if (Pdf::éPdf($composição->arquivo)) {
               $pdf = new Pdf($composição->arquivo);
               $folhaDeRosto->inserir($pdf);
           }
       }   
    }
}