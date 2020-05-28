<?php
use PHPUnit\Framework\TestCase;

final class PdfTest extends TestCase {

    public function testObterNúmeroDePáginasQuandoTemUmaPágina() {
        $pdf = new Pdf("testes" . DIRECTORY_SEPARATOR . "testeUmaPagina.pdf");
        $this->assertEquals(1, $pdf->obterNúmeroDePáginas());
    }

    public function testObterNúmeroDePáginasQuandoTemDuasPáginas() {
        $pdf = new Pdf("testes" . DIRECTORY_SEPARATOR . "testeDuasPaginas.pdf");
        $this->assertEquals(2, $pdf->obterNúmeroDePáginas());
    }

    public function testObterCaminho() {
        $caminho = "testes" . DIRECTORY_SEPARATOR . "testeDuasPaginas.pdf";
        $pdf = new Pdf("testes" . DIRECTORY_SEPARATOR . "testeDuasPaginas.pdf");
        $this->assertEquals($caminho, $pdf->obterCaminho());
    }
}
?> 