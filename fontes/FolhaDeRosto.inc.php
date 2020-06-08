<?php

require __DIR__ . '/../vendor/autoload.php';

class FolhaDeRosto { 

    private $statusDaSubmissão;
    private $doi;
    private $logo;
    private $checklist;
    private $locale;
    private $tradutor;
    const DIRETORIO_DE_SAIDA = DIRECTORY_SEPARATOR . "tmp" .  DIRECTORY_SEPARATOR;

    public function __construct(string $status, string $doi, string $logo, array $checklist, string $locale, tradutor $tradutor) {
        $this->statusDaSubmissão = $status;
        $this->doi = $doi;
        $this->logo = $logo;
        $this->checklist = $checklist;
        $this->locale = $locale;
        $this->tradutor = $tradutor;
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
        $folhaDeRosto->setPrintHeader(false);
        $folhaDeRosto->setPrintFooter(false);
        $folhaDeRosto->AddPage();
        $folhaDeRosto->SetFont('times', '', 18);
        $folhaDeRosto->Image($this->logo, 20, 20, 25    , '', 'PNG', '', 'T', false, 350, '', false, false, 0, false, false, false);
        $folhaDeRosto->Write(0, " ", '', 0, 'C', true, 0, false, false, 0);
        $folhaDeRosto->Write(0, $this->tradutor->traduzir('common.status', $this->locale) . ": " . $this->statusDaSubmissão, '', 0, 'C', true, 0, false, false, 0);
        $folhaDeRosto->Write(0, "DOI: " . $this->doi, '', 0, 'C', true, 0, false, false, 0);
        $folhaDeRosto->Write(0, "Autore(a)s reconhecem que aceitaram os requisitos abaixo no momento da submissão:", '', 0, 'C', true, 0, false, false, 0);
        
        foreach ($this->checklist as $item) {
            $texto = "<ul><li>". $item . "</li></ul>";
            $folhaDeRosto->WriteHTML($texto, true, 0, true, 0);
        }

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
        // $this->removerArquivosTemporários($arquivoDaFolhaDeRosto, $copiaArquivoOriginal);
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