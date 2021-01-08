<?php
use PHPUnit\Framework\TestCase;

final class PdfTest extends TestCase {

    public function testGetNumberOfPagesWhenPdfHasOnePage() : void {
        $pdf = new Pdf("tests" . DIRECTORY_SEPARATOR . "testOnePage.pdf");
        $this->assertEquals(1, $pdf->getNumberOfPages());
    }

    public function testGetNumberOfPagesWhenPdfHasTwoPages() : void {
        $pdf = new Pdf("tests" . DIRECTORY_SEPARATOR . "testTwoPages.pdf");
        $this->assertEquals(2, $pdf->getNumberOfPages());
    }

    public function testGetPath() : void {
        $path = "tests" . DIRECTORY_SEPARATOR . "testTwoPages.pdf";
        $pdf = new Pdf("tests" . DIRECTORY_SEPARATOR . "testTwoPages.pdf");
        $this->assertEquals($path, $pdf->getPath());
    }

    public function testCreateFileNotPdf() : void {
        $this->expectException(InvalidArgumentException::class);
        $pdf = new Pdf("tests" . DIRECTORY_SEPARATOR . "logo_noAlphaChannel.png");
    }

    public function testIsPdf() : void {
        $this->assertTrue(Pdf::isPdf("tests" . DIRECTORY_SEPARATOR . "testTwoPages.pdf"));
    }

    public function testIsNotPdf() : void {
        $this->assertFalse(Pdf::isPdf("tests" . DIRECTORY_SEPARATOR . "fileNotPdf.odt"));
    }

    public function testGetPageOrientation() : void {
        $pdf = new Pdf("tests" . DIRECTORY_SEPARATOR . "testOnePage.pdf");
        $this->assertEquals("P", $pdf->getPageOrientation());
    }
}
?> 