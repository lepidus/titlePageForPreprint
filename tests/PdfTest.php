<?php
use PHPUnit\Framework\TestCase;
import('plugins.generic.titlePageForPreprint.classes.Pdf');

final class PdfTest extends TestCase {

    protected const TESTS_DIRECTORY = 'plugins' . DIRECTORY_SEPARATOR . 'generic' . DIRECTORY_SEPARATOR . 'titlePageForPreprint' . DIRECTORY_SEPARATOR . 'tests' . DIRECTORY_SEPARATOR;

    public function testGetNumberOfPagesWhenPdfHasOnePage() : void {
        $pdf = new Pdf(self::TESTS_DIRECTORY . "testOnePage.pdf");
        $this->assertEquals(1, $pdf->getNumberOfPages());
    }

    public function testGetNumberOfPagesWhenPdfHasTwoPages() : void {
        $pdf = new Pdf(self::TESTS_DIRECTORY . "testTwoPages.pdf");
        $this->assertEquals(2, $pdf->getNumberOfPages());
    }

    public function testGetPath() : void {
        $path = self::TESTS_DIRECTORY . "testTwoPages.pdf";
        $pdf = new Pdf(self::TESTS_DIRECTORY . "testTwoPages.pdf");
        $this->assertEquals($path, $pdf->getPath());
    }

    public function testCreateFileNotPdf() : void {
        $this->expectException(InvalidArgumentException::class);
        $pdf = new Pdf(self::TESTS_DIRECTORY . "logo_noAlphaChannel.png");
    }

    public function testIsPdf() : void {
        $this->assertTrue(Pdf::isPdf(self::TESTS_DIRECTORY . "testTwoPages.pdf"));
    }

    public function testIsNotPdf() : void {
        $this->assertFalse(Pdf::isPdf(self::TESTS_DIRECTORY . "fileNotPdf.odt"));
    }
}
?> 