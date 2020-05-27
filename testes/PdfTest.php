<?php
use PHPUnit\Framework\TestCase;

final class PdfTest extends TestCase {

    public function testObterNúmeroDePáginasQuandoTemUmaPágina() {
        $pdf = new Pdf("testes/testeUmaPagina.pdf");
        $this->assertEquals(1, $pdf->obterNúmeroDePáginas());
    }

    public function testObterNúmeroDePáginasQuandoTemDuasPáginas() {
        $pdf = new Pdf("testes/testeDuasPaginas.pdf");
        $this->assertEquals(2, $pdf->obterNúmeroDePáginas());
    }
}
?> 