<?php

require __DIR__ . '/../vendor/autoload.php';

class FolhaDeRosto { 

    private $statusDaSubmissão;
    private $doi;
    private $logo;
    private $checklist;
    const DIRETORIO_DE_SAIDA = DIRECTORY_SEPARATOR . "tmp" .  DIRECTORY_SEPARATOR;

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

    private function gerarFolhaDeRosto(): string {
        $folhaDeRosto = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        $folhaDeRosto->AddPage();
        $folhaDeRosto->SetFont('times', 'BI', 20);
        $folhaDeRosto->Write(0, $this->statusDaSubmissão, '', 0, 'C', true, 0, false, false, 0);
        $folhaDeRosto->Write(0, $this->doi, '', 0, 'C', true, 0, false, false, 0);
        foreach ($this->checklist as $item) {
            $folhaDeRosto->Write(0, $item, '', 0, 'C', true, 0, false, false, 0);
        }
        $folhaDeRosto->Image($this->logo, 10, 10, 15, '', 'PNG', '', 'T', false, 300, '', false, false, 0, false, false, false);
        $arquivoDaFolhaDeRosto = self::DIRETORIO_DE_SAIDA . 'folhaDeRosto.pdf';
        $folhaDeRosto->Output($arquivoDaFolhaDeRosto, 'F');
        return $arquivoDaFolhaDeRosto;
    }

    private function concatenarFolhaDeRosto(string $arquivoDaFolhaDeRosto, pdf $pdf): void {
        $copiaArquivoOriginal = self::DIRETORIO_DE_SAIDA . "copia_arquivo_original.pdf";
        copy($pdf->obterCaminho(), $copiaArquivoOriginal);
        $arquivoModificado = self::DIRETORIO_DE_SAIDA . "comFolhaDeRosto.pdf";
        $comandoParaJuntar = 'pdfunite '.  $arquivoDaFolhaDeRosto . ' '. $copiaArquivoOriginal . ' ' . $arquivoModificado;
        shell_exec($comandoParaJuntar);
        rename($arquivoModificado, $pdf->obterCaminho());
        $this->removerArquivosTemporários($arquivoDaFolhaDeRosto, $copiaArquivoOriginal);
    }
    
    private function removerArquivosTemporários($arquivoDaFolhaDeRosto, $copiaArquivoOriginal) {
        unlink($arquivoDaFolhaDeRosto);
        unlink($copiaArquivoOriginal);
    }

    public function inserir(pdf $pdf): void {
        $arquivoDaFolhaDeRosto = $this->gerarFolhaDeRosto();
        $this->concatenarFolhaDeRosto($arquivoDaFolhaDeRosto, $pdf);
    }
}
?>