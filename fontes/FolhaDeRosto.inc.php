<?php

require __DIR__ . '/../vendor/autoload.php';
use \setasign\Fpdi\Fpdi;

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
        $folhaDeRosto->Image($this->logo, '', '', '35', '20', 'PNG', 'false', 'C', true, 400, 'C', false, false, 0, false, false, false);
        $folhaDeRosto->Ln(25);
        $fontname = TCPDF_FONTS::addTTFfont(__DIR__.'/../recursos/opensans.ttf', 'TrueTypeUnicode', '', 32);
        
        $folhaDeRosto->SetFont($fontname, '', 10, '', false);
        $folhaDeRosto->Write(0, $this->tradutor->traduzir('common.status', $this->locale) . ": " . $this->tradutor->traduzir($this->submissão->obterStatus(), $this->locale), '', 0, 'JUSTIFY', true, 0, false, false, 0);
        if($this->submissão->obterStatus() == 'publication.relation.published'){
            $folhaDeRosto->Write(0, $this->tradutor->traduzir('publication.relation.vorDoi', $this->locale) . ": ", '', 0, 'JUSTIFY', false, 0, false, false, 0);
            $folhaDeRosto->write(0, $this->submissão->obterDOIJournal(), $this->submissão->obterDOIJournal(), 0, 'JUSTIFY', true, 0, false, false, 0);
        }
        $folhaDeRosto->Ln(5);
        
        $folhaDeRosto->SetFont($fontname, '', 18, '', false);
        $folhaDeRosto->Write(0, $this->tradutor->obterTítuloTraduzido($this->locale), '', 0, 'C', true, 0, false, false, 0);
        $folhaDeRosto->SetFont($fontname, '', 12, '', false);
        $folhaDeRosto->Write(0, $this->submissão->obterAutores(), '', 0, 'C', true, 0, false, false, 0);
        $folhaDeRosto->SetFont($fontname, '', 11, '', false);
        $folhaDeRosto->Ln(5);
        $folhaDeRosto->Write(0, $this->tradutor->traduzir('metadata.property.displayName.doi', $this->locale) . ": " . $this->submissão->obterDOI(), '', 0, 'C', true, 0, false, false, 0);
        $folhaDeRosto->Ln(10);
        $folhaDeRosto->Write(0, $this->tradutor->traduzir('plugins.generic.folhaDeRostoDoPDF.rotuloDaChecklist', $this->locale) . ": ", '', 0, 'JUSTIFY', true, 0, false, false, 0);
        $folhaDeRosto->SetFont($fontname, '', 10, '', false);
        $folhaDeRosto->Ln(5);

        $textoChecklist = '';
        foreach ($this->tradutor->obterCheckListTraduzida($this->locale) as $item) {
            $textoChecklist = $textoChecklist. "<ul style=\"text-align:justify;\"><li>". $item . "</li></ul>";
        }
        $folhaDeRosto->writeHTMLCell(0, 0, '', '',$textoChecklist, 1, 1, false, true, 'JUSTIFY', false);
        $folhaDeRosto->SetFont($fontname, '', 11, '', false);
        $folhaDeRosto->Ln(5);
        $folhaDeRosto->Write(0, $this->tradutor->traduzir('plugins.geric.folhaDeRostoDoPDF.dataSubmissao', $this->locale) . ": " . $this->tradutor->obterDataTraduzida($this->submissão->obterDataDeSubmissão(), $this->locale), '', 0, 'JUSTIFY', true, 0, false, false, 0);
        $folhaDeRosto->Write(0, $this->tradutor->traduzir('plugins.geric.folhaDeRostoDoPDF.dataPublicacao', $this->locale) . ": " . $this->tradutor->obterDataTraduzida($this->submissão->obterDataDePublicação(), $this->locale), '', 0, 'JUSTIFY', true, 0, false, false, 0);
      
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
        $this->removerArquivosTemporários($arquivoDaFolhaDeRosto);
        $this->removerArquivosTemporários($copiaArquivoOriginal);
    }
    
    private function removerArquivosTemporários($arquivo) {
        unlink($arquivo);
    }

    public function inserir(pdf $pdf): void {
        $arquivoDaFolhaDeRosto = $this->gerarFolhaDeRosto();
        $this->concatenarFolhaDeRosto($arquivoDaFolhaDeRosto, $pdf);
    }

    private function separarPaginas(pdf $pdf, $paginaInicial) {
        $comandoParaSeparar = "pdfseparate -f {$paginaInicial} {$pdf->obterCaminho()} %d.pdf";
        shell_exec($comandoParaSeparar);
    }

    private function juntarPaginas(pdf $pdf, $paginaInicial) {
        $comandoParaJuntar = 'pdfunite ';
        $arquivoModificado = self::DIRETORIO_DE_SAIDA . "semFolhaDeRosto.pdf";
        $paginas = $pdf->obterNúmeroDePáginas();

        for ($i = $paginaInicial; $i <= $paginas; $i++){
            $comandoParaJuntar .= ($i .'.pdf ');
        }

        $comandoParaJuntar .= $arquivoModificado;
        shell_exec($comandoParaJuntar);
        rename($arquivoModificado, $pdf->obterCaminho());

        for ($i = $paginaInicial; $i <= $paginas; $i++){
            $this->removerArquivosTemporários( $i .'.pdf');
        }
    }

    public function remover(pdf $pdf): void {
        $this->separarPaginas($pdf, 2);
        $this->juntarPaginas($pdf, 2);
    }

    private function adicionaHeaderPagina($caminhoPagina) {
        $pdf = new FPDI();
        
        $pdf->AddPage();
        $pdf->setSourceFile($caminhoPagina);
        $tplIdx = $pdf->importPage(1);
        $pdf->useTemplate($tplIdx);

        $pdf->SetFont('Helvetica', '', 10);
        $pdf->Write(0, mb_convert_encoding("Não vale a pena sonhar e esquecer de viver", 'ISO-8859-15'), '', 0, 'C', true, 0, false, false, 0);

        $caminhoSaida = "header_{$caminhoPagina}";
        $pdf->Output($caminhoSaida, "F");
        rename($caminhoSaida, $caminhoPagina);
    }

    public function adicionaHeadersDocumento(pdf $pdf): void {
        $this->separarPaginas($pdf, 1);

        $paginas = $pdf->obterNúmeroDePáginas();
        for($i = 1; $i <= $paginas; $i++) {
            $this->adicionaHeaderPagina("{$i}.pdf");
        }

        $this->juntarPaginas($pdf, 1);
    }

}
?>