<?php
use PHPUnit\Framework\TestCase;

class ManipulacaoDePdfTest extends TestCase {

    protected $status = 'submissions.queued';
    protected $doi = "10.1000/182";
    protected $logo = 'testes' . DIRECTORY_SEPARATOR . "logo_semCanalAlfa.png";
    protected $checklist = array("A submissão não foi publicado anteriormente.", "As URLs das referências foram fornecidas.");
    protected $locale = "pt_BR";
    protected $titulo = "Assim Falou Zaratustra";
    protected $autores = "Cleide Silva; João Carlos";
    protected $dataDeSubmissão = "31/06/2020";
    protected $tradutor;

    protected function setUp(): void {
        $this->tradutor = new TradutorParaTestes();
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