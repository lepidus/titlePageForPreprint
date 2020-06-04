<?php
use PHPUnit\Framework\TestCase;

class ManipulacaoDePdfTest extends TestCase {
    
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
}
?>