<?php
require_once ("ManipulacaoDePdfTest.php");

class FolhaDeRostoTest extends ManipulacaoDePdfTest {
    
    private function obterFolhaDeRostoParaTeste(): FolhaDeRosto {
        return new FolhaDeRosto($this->status, $this->doi, $this->logo, $this->checklist);
    }

    private function converterPdfEmImagem(string $caminhoDoPdf, $caminhoDaImagem): imagick {
        $imagem = new imagick($caminhoDoPdf);
        $imagem->setImageFormat('jpeg');  
        $imagem->writeImage($caminhoDaImagem);
        return $imagem;
    }
    
    private function imagensSãoIguais(imagick $imagem1, imagick $imagem2): void {
        $diferençaEntreElas = $imagem1->compareImages($imagem2, Imagick::METRIC_MEANSQUAREERROR);
        $this->assertEquals(0.0, $diferençaEntreElas[1]);
    }

    private function extrairImagemDePdf(pdf $pdf): string {
        $caminhoDaImageExtraida = "testes" . DIRECTORY_SEPARATOR;
        $resultado = shell_exec("pdfimages -f 1 -png ". $pdf->obterCaminho() . " " . $caminhoDaImageExtraida);
        $imagemExtraida = $caminhoDaImageExtraida . DIRECTORY_SEPARATOR . "-000.png";
        return $imagemExtraida;
    }

    private function converterPdfEmTexto(pdf $pdf): void {
        shell_exec("pdftotext ". $pdf->obterCaminho() . " " . $this->pdfComoTexto);
    }

    private function procurarEmArquivoDeTexto($textoProcurado, $caminhoDoArquivo): string {
        $resultadoDaProcura = shell_exec("grep '$textoProcurado' ". $caminhoDoArquivo);
        return trim($resultadoDaProcura);
    }

    public function testeTemStatusDeSubmissão(): void {
        $folhaDeRosto = $this->obterFolhaDeRostoParaTeste();
        $this->assertEquals($this->status, $folhaDeRosto->obterStatusDeSubmissão());
    }
    
    public function testeTemDoi(): void {
        $folhaDeRosto = $this->obterFolhaDeRostoParaTeste();
        $this->assertEquals($this->doi, $folhaDeRosto->obterDOI());
    }

    public function testeTemLogo(): void {
        $folhaDeRosto = $this->obterFolhaDeRostoParaTeste();
        $this->assertEquals($this->logo, $folhaDeRosto->obterLogo());
    }
    
    public function testeTemChecklist(): void {
        $folhaDeRosto = $this->obterFolhaDeRostoParaTeste();
        $this->assertEquals($this->checklist, $folhaDeRosto->obterChecklist());
    }

    public function testeInserçãoEmPdfExistenteCriaNovaPágina(): void {
        $folhaDeRosto = $this->obterFolhaDeRostoParaTeste();

        $pdf = new Pdf($this->caminhoDoPdfTeste);
        $folhaDeRosto->inserir($pdf);
        $this->assertEquals(2, $pdf->obterNúmeroDePáginas());
    }

    public function testeInserçãoEmPdfExistenteCarimbaFolhaDeRostoComStatusDeSubmissão(): void {
        $folhaDeRosto = $this->obterFolhaDeRostoParaTeste();
        $pdf = new Pdf($this->caminhoDoPdfTeste);
        
        $folhaDeRosto->inserir($pdf);
        
        $this->converterPdfEmTexto($pdf);
        $textoEsperado = "Situação: " . $this->status;
        $resultadoDaProcura = $this->procurarEmArquivoDeTexto($textoEsperado, $this->pdfComoTexto);
        $this->assertEquals($textoEsperado, $resultadoDaProcura);
    }

    public function testeInserçãoEmPdfExistenteCarimbaDoi(): void {
        $folhaDeRosto = $this->obterFolhaDeRostoParaTeste();
        $pdf = new Pdf($this->caminhoDoPdfTeste);
        
        $folhaDeRosto->inserir($pdf);
        
        $this->converterPdfEmTexto($pdf);
        $textoEsperado = "DOI: " . $this->doi;
        $resultadoDaProcura = $this->procurarEmArquivoDeTexto($textoEsperado, $this->pdfComoTexto);
        $this->assertEquals($textoEsperado, $resultadoDaProcura);
       
    }

    public function testeInserçãoEmPdfExistenteCarimbaChecklist(): void {
        $folhaDeRosto = $this->obterFolhaDeRostoParaTeste();
        $pdf = new Pdf($this->caminhoDoPdfTeste);
        
        $folhaDeRosto->inserir($pdf);
        
        $this->converterPdfEmTexto($pdf);

        $rótuloEsperadoLinha1 = "Autore(a)s reconhecem que aceitaram os requisitos abaixo no";
        $rótuloEsperadoLinha2 = 'momento da submissão:';
        $resultadoDaProcuraRótuloLinha1 = $this->procurarEmArquivoDeTexto($rótuloEsperadoLinha1, $this->pdfComoTexto);
        $this->assertEquals($rótuloEsperadoLinha1, $resultadoDaProcuraRótuloLinha1);
        $resultadoDaProcuraRótuloLinha2 = $this->procurarEmArquivoDeTexto($rótuloEsperadoLinha2, $this->pdfComoTexto);
        $this->assertEquals($rótuloEsperadoLinha2, $resultadoDaProcuraRótuloLinha2);

        $primeiroItem = $this->checklist[0];
        $resultadoDaProcuraPrimeiroItemDaChecklist = $this->procurarEmArquivoDeTexto($primeiroItem, $this->pdfComoTexto);
        $this->assertEquals($primeiroItem, $resultadoDaProcuraPrimeiroItemDaChecklist);
        $segundoItem = $this->checklist[1];
        $resultadoDaProcuraSegundoItemDaChecklist = $this->procurarEmArquivoDeTexto($segundoItem, $this->pdfComoTexto);
        $this->assertEquals($segundoItem, $resultadoDaProcuraSegundoItemDaChecklist);
    }

    public function testeInserçãoEmPdfExistenteCarimbaLogo(): void {
        $folhaDeRosto = $this->obterFolhaDeRostoParaTeste();
        $pdf = new Pdf($this->caminhoDoPdfTeste);
        
        $folhaDeRosto->inserir($pdf);
        
        $imagemExtraida = $this->extrairImagemDePdf($pdf);
        $this->imagensSãoIguais(new imagick($this->logo), new imagick($imagemExtraida));
        unlink($imagemExtraida);
    }    

    public function testeInserçãoEmPdfExistenteNãoModificaPdfOriginal(): void {
        $folhaDeRosto = $this->obterFolhaDeRostoParaTeste();
        $pdfNovo = new Pdf($this->caminhoDoPdfTeste);
        $folhaDeRosto->inserir($pdfNovo);
        $pdfOriginal = new Pdf($this->cópiaDoPdfTesteParaRestaurar);

        $arquivoDaImagemDoPdfOriginal = 'imagem_pdf_original.jpg';
        $arquivoDaImagemDoPdfComFolhaDeRosto = 'imagem_pdf_folhaderosto.jpg';
        $imagemDoPdfOriginal = $this->converterPdfEmImagem($pdfOriginal->obterCaminho().'[0]', $arquivoDaImagemDoPdfOriginal);
        $imagemDoPdfComFolhaDeRosto = $this->converterPdfEmImagem($pdfNovo->obterCaminho().'[1]', $arquivoDaImagemDoPdfComFolhaDeRosto);
        $this->imagensSãoIguais($imagemDoPdfOriginal, $imagemDoPdfComFolhaDeRosto);
        unlink($arquivoDaImagemDoPdfOriginal);
        unlink($arquivoDaImagemDoPdfComFolhaDeRosto);
    }
}
?>