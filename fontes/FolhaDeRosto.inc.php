<?php

require __DIR__ . '/TCPDF/vendor/autoload.php';

class FolhaDeRosto { 

    private $statusDaSubmissão;
    private $doi;
    private $logo;
    private $checklist;

    public function __construct(string $status, string $doi, string $logo, array $checklist) {
        $this->statusDaSubmissão = $status;
        $this->doi = $doi;
        $this->logo = $logo;
        $this->checklist = $checklist;
    }
    
    public function obterStatusDeSubmissão(): string {
        return $this->statusDaSubmissão;
    }

    public function obterDOI(): string {
        return $this->doi;
    }

    public function obterLogo(): string {
        return $this->logo;
    }

    public function obterChecklist(): array {
        return $this->checklist;
    }

    public function inserir(pdf $pdf): void {

        $folhaDeRosto = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        $folhaDeRosto->AddPage();
        
        $diretorioDeSaida = DIRECTORY_SEPARATOR . "tmp" .  DIRECTORY_SEPARATOR;
        $arquivoDaFolhaDeRosto = $diretorioDeSaida . 'folhaDeRosto.pdf';
        
        $folhaDeRosto->Output($arquivoDaFolhaDeRosto, 'F');
        
        $arquivoOriginal =  $diretorioDeSaida . "arquivo_original.pdf";
        copy($pdf->obterCaminho(), $arquivoOriginal);
        
        $arquivoModificado = $diretorioDeSaida . "comFolhaDeRosto.pdf";
        
        $comandoParaJuntar = 'pdfunite '.  $arquivoDaFolhaDeRosto . ' '. $arquivoOriginal . ' ' . $arquivoModificado;

        shell_exec($comandoParaJuntar);
        
        rename($arquivoModificado, $pdf->obterCaminho());
        // unlink($arquivoDaFolhaDeRosto);

    }
}
?>