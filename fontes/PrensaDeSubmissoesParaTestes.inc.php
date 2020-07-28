<?php
class PrensaDeSubmissoesParaTestes implements PrensaDeSubmissoes {

    private $logoParaFolhaDeRosto;
    private $submissão;
    private $tradutor;

    public function __construct(string $logoParaFolhaDeRosto, Submissao $submissão, Tradutor $tradutor) {
        $this->logoParaFolhaDeRosto = $logoParaFolhaDeRosto;
        $this->submissão = $submissão;
        $this->tradutor = $tradutor;
    }

    public function inserirFolhasDeRosto(): void {
       foreach($this->submissão->obterComposições() as $composição){
           $folhaDeRosto = new FolhaDeRosto($this->submissão, $this->logoParaFolhaDeRosto, $composição->locale, $this->tradutor);

           if (Pdf::éPdf($composição->arquivo)) {               
               $pdf = new Pdf($composição->arquivo);
               $folhaDeRosto->inserir($pdf);
           }
       }   
    }
}