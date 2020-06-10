<?php

require __DIR__ . '/../vendor/autoload.php';

class FolhaDeRosto { 

    private $submissão;
    private $logo;
    private $locale;
    private $tradutor;
    const DIRETORIO_DE_SAIDA = DIRECTORY_SEPARATOR . "tmp" .  DIRECTORY_SEPARATOR;

    public function __construct(Submissao $submissão, string $logo, string $locale, tradutor $tradutor) {
        $this->submissão = $submissão;
        $this->logo = $logo;
        $this->locale = $locale;
        $this->tradutor = $tradutor;
    }

    public function obterLogo(): string {
        return $this->logo;
    }
    
    private function gerarFolhaDeRosto(): string {
        $folhaDeRosto = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        $folhaDeRosto->setPrintHeader(false);
        $folhaDeRosto->setPrintFooter(false);
        $folhaDeRosto->AddPage();
        $folhaDeRosto->SetFont('times', '', 18);
        $folhaDeRosto->Image($this->logo, 20, 20, 25    , '', 'PNG', '', 'T', false, 350, '', false, false, 0, false, false, false);
        $folhaDeRosto->Write(0, " ", '', 0, 'C', true, 0, false, false, 0);
        $folhaDeRosto->Write(0, $this->tradutor->traduzir('common.status', $this->locale) . ": " . $this->tradutor->traduzir($this->submissão->obterStatus(), $this->locale), '', 0, 'C', true, 0, false, false, 0);
        $folhaDeRosto->Write(0, $this->tradutor->obterTítuloTraduzido($this->locale), '', 0, 'C', true, 0, false, false, 0);
        $folhaDeRosto->Write(0, $this->submissão->obterAutores(), '', 0, 'C', true, 0, false, false, 0);
        $folhaDeRosto->Write(0, $this->tradutor->traduzir('metadata.property.displayName.doi', $this->locale) . ": " . $this->submissão->obterDOI(), '', 0, 'C', true, 0, false, false, 0);
        $folhaDeRosto->Write(0, $this->tradutor->traduzir('submission.submit.submissionChecklist', $this->locale) . ": ", '', 0, 'C', true, 0, false, false, 0);
        
        foreach ($this->tradutor->obterCheckListTraduzida($this->locale) as $item) {
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