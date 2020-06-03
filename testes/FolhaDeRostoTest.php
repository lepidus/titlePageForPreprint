<?php
use PHPUnit\Framework\TestCase;

final class FolhaDeRostoTest extends TestCase {
    private $status = "STATUS_QUEUED";
    private $doi = "10.1000/182";
    private $logo = 'testes' . DIRECTORY_SEPARATOR . "logo_semCanalAlfa.png"; 
    private $checklist = array("A submissão não foi publicado anteriormente.", "As URLs das referências foram fornecidas.");
    
    protected function setUp(): void {
        $this->caminhoDoPdfTeste = "testes" . DIRECTORY_SEPARATOR . "testeUmaPagina.pdf";
        $this->cópiaDoPdfTesteParaRestaurar = "testes" . DIRECTORY_SEPARATOR . "testeUmaPagina_copia.pdf";
        copy($this->caminhoDoPdfTeste, $this->cópiaDoPdfTesteParaRestaurar);
        $this->pdfComoTexto = "testes" . DIRECTORY_SEPARATOR . "testeUmaPagina.txt";
    }
    
    protected function tearDown(): void {
        $this->assertTrue(unlink($this->caminhoDoPdfTeste));
        rename($this->cópiaDoPdfTesteParaRestaurar, $this->caminhoDoPdfTeste);
        if (file_exists($this->pdfComoTexto)) {
            unlink($this->pdfComoTexto);
        }
    }

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
        $resultadoDaProcura = $this->procurarEmArquivoDeTexto($this->status, $this->pdfComoTexto);
        $this->assertEquals($this->status, $resultadoDaProcura);
    }

    public function testeInserçãoEmPdfExistenteCarimbaDoi(): void {
        $folhaDeRosto = $this->obterFolhaDeRostoParaTeste();
        $pdf = new Pdf($this->caminhoDoPdfTeste);
        
        $folhaDeRosto->inserir($pdf);
        
        $this->converterPdfEmTexto($pdf);
        $resultadoDaProcura = $this->procurarEmArquivoDeTexto($this->doi, $this->pdfComoTexto);
        $this->assertEquals($this->doi, $resultadoDaProcura);
       
    }

    public function testeInserçãoEmPdfExistenteCarimbaChecklist(): void {
        $folhaDeRosto = $this->obterFolhaDeRostoParaTeste();
        $pdf = new Pdf($this->caminhoDoPdfTeste);
        
        $folhaDeRosto->inserir($pdf);
        
        $this->converterPdfEmTexto($pdf);
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

    public function testeInserçãoEmPdfExistenteNãoModificaPdfOriginal(): void{
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