<?php
use PHPUnit\Framework\TestCase;

final class PdfTest extends TestCase {

    public function testeObterNúmeroDePáginasQuandoTemUmaPágina() : void {
        $pdf = new Pdf("testes" . DIRECTORY_SEPARATOR . "testeUmaPagina.pdf");
        $this->assertEquals(1, $pdf->obterNúmeroDePáginas());
    }

    public function testeObterNúmeroDePáginasQuandoTemDuasPáginas() : void {
        $pdf = new Pdf("testes" . DIRECTORY_SEPARATOR . "testeDuasPaginas.pdf");
        $this->assertEquals(2, $pdf->obterNúmeroDePáginas());
    }

    public function testeObterCaminho() : void {
        $caminho = "testes" . DIRECTORY_SEPARATOR . "testeDuasPaginas.pdf";
        $pdf = new Pdf("testes" . DIRECTORY_SEPARATOR . "testeDuasPaginas.pdf");
        $this->assertEquals($caminho, $pdf->obterCaminho());
    }

    public function testeCriaçãoArquivoNãoPdf() : void {
        $this->expectException(InvalidArgumentException::class);
        $pdf = new Pdf("testes" . DIRECTORY_SEPARATOR . "logo_semCanalAlfa.png");
    }

    public function testeÉPDF() : void {
        $this->assertTrue(Pdf::éPdf("testes" . DIRECTORY_SEPARATOR . "testeDuasPaginas.pdf"));
    }

    public function testeNãoÉPDF() : void {
        $this->assertFalse(Pdf::éPdf("testes" . DIRECTORY_SEPARATOR . "arquivoNaoPdf.odt"));
    }
}
?> 