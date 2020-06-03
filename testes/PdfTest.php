<?php
use PHPUnit\Framework\TestCase;

final class PdfTest extends TestCase {

    public function testeObterNúmeroDePáginasQuandoTemUmaPágina() {
        $pdf = new Pdf("testes" . DIRECTORY_SEPARATOR . "testeUmaPagina.pdf");
        $this->assertEquals(1, $pdf->obterNúmeroDePáginas());
    }

    public function testeObterNúmeroDePáginasQuandoTemDuasPáginas() {
        $pdf = new Pdf("testes" . DIRECTORY_SEPARATOR . "testeDuasPaginas.pdf");
        $this->assertEquals(2, $pdf->obterNúmeroDePáginas());
    }

    public function testeObterCaminho() {
        $caminho = "testes" . DIRECTORY_SEPARATOR . "testeDuasPaginas.pdf";
        $pdf = new Pdf("testes" . DIRECTORY_SEPARATOR . "testeDuasPaginas.pdf");
        $this->assertEquals($caminho, $pdf->obterCaminho());
    }

    public function testeArquivoNãoPdf() {
        $this->expectException(InvalidArgumentException::class);
        $pdf = new Pdf("testes" . DIRECTORY_SEPARATOR . "logo_semCanalAlfa.png");
    }
}
?> 