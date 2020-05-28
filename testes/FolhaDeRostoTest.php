<?php
use PHPUnit\Framework\TestCase;

final class FolhaDeRostoTest extends TestCase {
    private $status = "STATUS_QUEUED";
    private $doi = "10.1000/182";
    private $logo =  DIRECTORY_SEPARATOR . "caminho-logo"  . DIRECTORY_SEPARATOR . "logo.png"; 
    private $checklist = array("A submissão não foi publicado anteriormente.", "As URLs das referências foram fornecidas.");
    
    private function obterFolhaDeRostoParaTeste(): FolhaDeRosto {
        return new FolhaDeRosto($this->status, $this->doi, $this->logo, $this->checklist);
    }

    private function arquivosSãoIdênticos($a, $b) {
        // Check if filesize is different
        if (filesize($a) !== filesize($b))
            return false;

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
        $caminhoDoPdfTeste = "testes" . DIRECTORY_SEPARATOR . "testeUmaPagina.pdf";
        $cópiaDoPdfTesteParaRestaurar = "testes" . DIRECTORY_SEPARATOR . "testeUmaPagina_copia.pdf";
        copy($caminhoDoPdfTeste, $cópiaDoPdfTesteParaRestaurar);

        $pdf = new Pdf($caminhoDoPdfTeste);
        $pdf = $folhaDeRosto->inserir($pdf);
        $this->assertEquals(2, $pdf->obterNúmeroDePáginas());
        
        $this->assertTrue(unlink($caminhoDoPdfTeste));
        rename($cópiaDoPdfTesteParaRestaurar, $caminhoDoPdfTeste);
    }

    public function testeInserçãoEmPdfExistenteCarimbaFolhaDeRosto(): void { //fatorar!
        $folhaDeRosto = $this->obterFolhaDeRostoParaTeste();
        $caminhoDoPdfTeste = "testes" . DIRECTORY_SEPARATOR . "testeUmaPagina.pdf";
        $cópiaDoPdfTesteParaRestaurar = "testes" . DIRECTORY_SEPARATOR . "testeUmaPagina_copia.pdf";
        copy($caminhoDoPdfTeste, $cópiaDoPdfTesteParaRestaurar);
        $pdfEsperado = "testes" . DIRECTORY_SEPARATOR . "testePdfComFolhaDeRosto.pdf";

        $pdf = new Pdf($caminhoDoPdfTeste);
        $pdf = $folhaDeRosto->inserir($pdf);

        shell_exec("pdftotext ". $pdf->obterCaminho());
        $procuraDoCarimbo = shell_exec("grep 'umCarimboQualquer' testes/testeUmaPagina.txt");
        $this->assertEquals("umCarimboQualquer\n", $procuraDoCarimbo);

        $this->assertTrue(unlink($caminhoDoPdfTeste));
        rename($cópiaDoPdfTesteParaRestaurar, $caminhoDoPdfTeste);
    }
}
?>