<?php
class PrensaDeSubmissoes {

    private $logoParaFolhaDeRosto;
    private $checklist;
    private $submissões;

    public function __construct(string $logoParaFolhaDeRosto, array $checklist, array $submissões) {
        $this->logoParaFolhaDeRosto = $logoParaFolhaDeRosto;
        $this->checklist = $checklist;
        $this->submissões = $submissões;
    }

    public function inserirFolhasDeRosto(): void {
       foreach($this->submissões as $submissão){
           $folhaDeRosto = new FolhaDeRosto($submissão->status, $submissão->doi, $this->logoParaFolhaDeRosto, $this->checklist);
           $caminhoDaComposição = $submissão->caminhoDaComposição;
           $pdf = new Pdf($caminhoDaComposição);
           $folhaDeRosto->inserir($pdf);
       }
       
    }

    public function obterSubmissões(): array {
        return $this->submissões;
    }
}