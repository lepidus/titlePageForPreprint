<?php

require __DIR__ . '/tc-lib-pdf-develop/vendor/autoload.php';

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

        $folhaDeRosto = new \Com\Tecnick\Pdf\Tcpdf();
        $folhaDeRosto->page->add();
        // $folhaDeRosto->Write(0, "umCarimboQualquer", '', 0, 'C', true, 0, false, false, 0);
        
        $conteudoDoDocumento = $folhaDeRosto->getOutPDFString();

        $diretorioDeSaida = DIRECTORY_SEPARATOR . "tmp" .  DIRECTORY_SEPARATOR;
        $arquivoDaFolhaDeRosto = $diretorioDeSaida . 'folhaDeRosto.pdf';
        
        file_put_contents($arquivoDaFolhaDeRosto, $conteudoDoDocumento);
        
        $arquivoOriginal =  $diretorioDeSaida . "arquivo_original.pdf";
        copy($pdf->obterCaminho(), $arquivoOriginal);
        
        $arquivoModificado = $diretorioDeSaida . "comFolhaDeRosto.pdf";
        
        $comandoParaJuntar = 'pdfunite '.  $arquivoDaFolhaDeRosto . ' '. $arquivoOriginal . ' ' . $arquivoModificado;

        shell_exec($comandoParaJuntar);
        
        rename($arquivoModificado, $pdf->obterCaminho());
        unlink($arquivoDaFolhaDeRosto);

    }
}
?>