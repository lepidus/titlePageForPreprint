<?php
use PHPUnit\Framework\TestCase;

class ManipulacaoDePdfTest extends TestCase {
    
    protected function setUp(): void {
        $this->caminhoDoPdfTeste = "testes" . DIRECTORY_SEPARATOR . "testeUmaPagina.pdf";
        $this->cópiaDoPdfTesteParaRestaurar = "testes" . DIRECTORY_SEPARATOR . "testeUmaPagina_copia.pdf";
        copy($this->caminhoDoPdfTeste, $this->cópiaDoPdfTesteParaRestaurar);
        $this->pdfComoTexto = "testes" . DIRECTORY_SEPARATOR . "testeUmaPagina.txt";

        $this->caminhoDoPdfTeste2 = "testes" . DIRECTORY_SEPARATOR . "testeDuasPaginas.pdf";
        $this->cópiaDoPdfTesteParaRestaurar2 = "testes" . DIRECTORY_SEPARATOR . "testeDuasPaginas_copia.pdf";
        copy($this->caminhoDoPdfTeste2, $this->cópiaDoPdfTesteParaRestaurar2);
    }
    
    protected function tearDown(): void {
        $this->assertTrue(unlink($this->caminhoDoPdfTeste));
        rename($this->cópiaDoPdfTesteParaRestaurar, $this->caminhoDoPdfTeste);
        
        $this->assertTrue(unlink($this->caminhoDoPdfTeste2));
        rename($this->cópiaDoPdfTesteParaRestaurar2, $this->caminhoDoPdfTeste2);

        if (file_exists($this->pdfComoTexto)) {
            unlink($this->pdfComoTexto);
        }
    }
}
?>