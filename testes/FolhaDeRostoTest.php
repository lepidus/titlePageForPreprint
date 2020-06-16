<?php
require_once ("ManipulacaoDePdfTest.php");

class FolhaDeRostoTest extends ManipulacaoDePdfTest {
    
    private function obterFolhaDeRostoParaTeste(): FolhaDeRosto {
        return new FolhaDeRosto(new Submissao($this->status, $this->doi, $this->autores, $this->dataDeSubmissão), $this->logo, $this->locale, $this->tradutor);
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

    public function testeInserçãoEmPdfExistenteCriaNovaPágina(): void {
        $folhaDeRosto = $this->obterFolhaDeRostoParaTeste();

        $pdf = new Pdf($this->caminhoDoPdfTeste);
        $folhaDeRosto->inserir($pdf);
        $this->assertEquals(2, $pdf->obterNúmeroDePáginas());
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

        $rótuloEsperado = "Este preprint foi submetido sob as seguintes condições:";
        $resultadoDaProcuraRótulo = $this->procurarEmArquivoDeTexto($rótuloEsperado, $this->pdfComoTexto);
        $this->assertEquals($rótuloEsperado, $resultadoDaProcuraRótulo);

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

    public function testeInserçãoEmPdfExistenteCarimbaTitulo(): void {
        $folhaDeRosto = $this->obterFolhaDeRostoParaTeste();
        $pdf = new Pdf($this->caminhoDoPdfTeste);
        
        $folhaDeRosto->inserir($pdf);
        
        $this->converterPdfEmTexto($pdf);
        $textoEsperado = $this->titulo;
        $resultadoDaProcura = $this->procurarEmArquivoDeTexto($textoEsperado, $this->pdfComoTexto);
        $this->assertEquals($textoEsperado, $resultadoDaProcura);
    }

    public function testeInserçãoEmPdfExistenteCarimbaAutores(): void {
        $folhaDeRosto = $this->obterFolhaDeRostoParaTeste();
        $pdf = new Pdf($this->caminhoDoPdfTeste);
        
        $folhaDeRosto->inserir($pdf);
        
        $this->converterPdfEmTexto($pdf);
        $textoEsperado = $this->autores;
        $resultadoDaProcura = $this->procurarEmArquivoDeTexto($textoEsperado, $this->pdfComoTexto);
        $this->assertEquals($textoEsperado, $resultadoDaProcura);
    }

    public function testeInserçãoEmPdfExistenteCarimbaDataDeSubmissão(): void {
        $folhaDeRosto = $this->obterFolhaDeRostoParaTeste();
        $pdf = new Pdf($this->caminhoDoPdfTeste);
        
        $folhaDeRosto->inserir($pdf);
        
        $this->converterPdfEmTexto($pdf);
        $textoEsperado = "Data de submissão: ". $this->dataDeSubmissão;
        $resultadoDaProcura = $this->procurarEmArquivoDeTexto($textoEsperado, $this->pdfComoTexto);
        $this->assertEquals($textoEsperado, $resultadoDaProcura);
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

    public function testeCarimbaFolhaDeRostoComRótuloDeChecklistTraduzidaParaIdiomaDaComposição(): void {
        $folhaDeRosto = new FolhaDeRosto(new Submissao($this->status, $this->doi, $this->autores, $this->dataDeSubmissão), $this->logo, "en_US", $this->tradutor);
        $pdf = new Pdf($this->caminhoDoPdfTeste);
        
        $folhaDeRosto->inserir($pdf);
        
        $this->converterPdfEmTexto($pdf);
        $textoEsperado = "This preprint was submitted under the following conditions:";
        $resultadoDaProcura = $this->procurarEmArquivoDeTexto($textoEsperado, $this->pdfComoTexto);
        $this->assertEquals($textoEsperado, $resultadoDaProcura);
    }

    public function testeCarimbaFolhaDeRostoComChecklistTraduzidaParaIdiomaDaComposição(): void {
        $folhaDeRosto = new FolhaDeRosto(new Submissao($this->status, $this->doi, $this->autores, $this->dataDeSubmissão), $this->logo, "en_US", $this->tradutor);
        $pdf = new Pdf($this->caminhoDoPdfTeste);
        
        $folhaDeRosto->inserir($pdf);
        
        $this->converterPdfEmTexto($pdf);

        $primeiroItem = "The submission has not been previously published.";
        $resultadoDaProcuraPrimeiroItemDaChecklist = $this->procurarEmArquivoDeTexto($primeiroItem, $this->pdfComoTexto);
        $this->assertEquals($primeiroItem, $resultadoDaProcuraPrimeiroItemDaChecklist);
        $segundoItem = "Where available, URLs for the references have been provided.";
        $resultadoDaProcuraSegundoItemDaChecklist = $this->procurarEmArquivoDeTexto($segundoItem, $this->pdfComoTexto);
        $this->assertEquals($segundoItem, $resultadoDaProcuraSegundoItemDaChecklist);
    }

    public function testeCarimbaFolhaDeRostoComDataDeSubmissãoTraduzidaParaIdiomaDaComposição(): void {
        $folhaDeRosto = new FolhaDeRosto(new Submissao($this->status, $this->doi, $this->autores, $this->dataDeSubmissão), $this->logo, "en_US", $this->tradutor);
        $pdf = new Pdf($this->caminhoDoPdfTeste);
        
        $folhaDeRosto->inserir($pdf);
        
        $this->converterPdfEmTexto($pdf);
        $textoEsperado = "Date submitted: ". $this->dataDeSubmissão;
        $resultadoDaProcura = $this->procurarEmArquivoDeTexto($textoEsperado, $this->pdfComoTexto);
        $this->assertEquals($textoEsperado, $resultadoDaProcura);
    }
}
?>