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
        $pdfEsperado = "testes" . DIRECTORY_SEPARATOR . "testePdfComFolhaDeRosto.pdf";
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

    private function obterDiferençaImagens(string $caminho1, string $caminho2, int $tipo): array{
        $imagem1 = new imagick($caminho1);
        $imagem2 = new imagick($caminho2);

        if($tipo == 1){
            $imagem1->setImageFormat('jpeg');  
            $imagem1->writeImage('imagem_pdf_original.jpg');
            $imagem2->setImageFormat('jpeg');    
            $imagem2->writeImage('imagem_pdf_folhaderosto.jpg'); 
        }
        return $imagem1->compareImages($imagem2, Imagick::METRIC_MEANSQUAREERROR);
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
        
        shell_exec("pdftotext ". $pdf->obterCaminho());
        $procuraDoCarimbo = shell_exec("grep '$this->status' ". $this->pdfComoTexto);
        $this->assertEquals($this->status, trim($procuraDoCarimbo));
    }

    public function testeInserçãoEmPdfExistenteCarimbaDoi(): void {
        $folhaDeRosto = $this->obterFolhaDeRostoParaTeste();
        $pdf = new Pdf($this->caminhoDoPdfTeste);
        
        $folhaDeRosto->inserir($pdf);
        
        shell_exec("pdftotext ". $pdf->obterCaminho());
   
        $procuraDoCarimbo = shell_exec("grep '$this->doi' ". $this->pdfComoTexto);
        $this->assertEquals($this->doi, trim($procuraDoCarimbo));
       
    }

    public function testeInserçãoEmPdfExistenteCarimbaChecklist(): void {
        $folhaDeRosto = $this->obterFolhaDeRostoParaTeste();
        $pdf = new Pdf($this->caminhoDoPdfTeste);
        
        $folhaDeRosto->inserir($pdf);
        
        shell_exec("pdftotext ". $pdf->obterCaminho());
        $this->pdfComoTexto = "testes" . DIRECTORY_SEPARATOR . "testeUmaPagina.txt";
        $primeiroItem = $this->checklist[0];
        $procuraPrimeiroItemDaChecklist = shell_exec("grep '$primeiroItem' ". $this->pdfComoTexto);
        $this->assertEquals($primeiroItem, trim($procuraPrimeiroItemDaChecklist));
        $segundoItem = $this->checklist[1];
        $procuraSegundoItemDaChecklist = shell_exec("grep '$segundoItem' ". $this->pdfComoTexto);
        $this->assertEquals($segundoItem, trim($procuraSegundoItemDaChecklist));
    }

    public function testeInserçãoEmPdfExistenteCarimbaLogo(): void {
        $folhaDeRosto = $this->obterFolhaDeRostoParaTeste();
        $pdf = new Pdf($this->caminhoDoPdfTeste);
        
        $folhaDeRosto->inserir($pdf);
        
        $caminhoDaImageExtraida = "testes" . DIRECTORY_SEPARATOR;
        $resultado = shell_exec("pdfimages -f 1 -png ". $pdf->obterCaminho() . " " . $caminhoDaImageExtraida);
        $imagemExtraida = $caminhoDaImageExtraida . DIRECTORY_SEPARATOR . "-000.png";        
        $diferenca = $this->obterDiferençaImagens($this->logo, $imagemExtraida, 0);
        $this->assertSame(0.0, $diferenca[1]);
        unlink($imagemExtraida);
    }    

    public function testeInserçãoEmPdfExistentePdfOriginal(): void{
        $folhaDeRosto = $this->obterFolhaDeRostoParaTeste();
        $pdfNovo = new Pdf($this->caminhoDoPdfTeste);
        $folhaDeRosto->inserir($pdfNovo);
        $pdfOriginal = new Pdf($this->cópiaDoPdfTesteParaRestaurar);
        $diferenca = $this->obterDiferençaImagens($pdfOriginal->obterCaminho().'[0]', $pdfNovo->obterCaminho().'[1]', 1);
        $this->assertSame(0.0, $diferenca[1]);
    }
}
?>