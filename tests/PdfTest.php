<?php

use PKP\tests\PKPTestCase;
use APP\plugins\generic\titlePageForPreprint\classes\Pdf;

final class PdfTest extends PKPTestCase
{
    public function testGetNumberOfPagesWhenPdfHasOnePage(): void
    {
        $pdf = new Pdf(TESTS_DIRECTORY. ASSETS_DIRECTORY.  "testOnePage.pdf");
        $this->assertEquals(1, $pdf->getNumberOfPages());
    }

    public function testGetNumberOfPagesWhenPdfHasTwoPages(): void
    {
        $pdf = new Pdf(TESTS_DIRECTORY. ASSETS_DIRECTORY. "testTwoPages.pdf");
        $this->assertEquals(2, $pdf->getNumberOfPages());
    }

    public function testGetPath(): void
    {
        $path = TESTS_DIRECTORY. ASSETS_DIRECTORY. "testTwoPages.pdf";
        $pdf = new Pdf(TESTS_DIRECTORY. ASSETS_DIRECTORY. "testTwoPages.pdf");
        $this->assertEquals($path, $pdf->getPath());
    }

    public function testCreateFileNotPdf(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $pdf = new Pdf(TESTS_DIRECTORY. ASSETS_DIRECTORY. "logo_noAlphaChannel.png");
    }

    public function testIsPdf(): void
    {
        $this->assertTrue(Pdf::isPdf(TESTS_DIRECTORY. ASSETS_DIRECTORY. "testTwoPages.pdf"));
    }

    public function testIsNotPdf(): void
    {
        $this->assertFalse(Pdf::isPdf(TESTS_DIRECTORY. ASSETS_DIRECTORY. "fileNotPdf.odt"));
    }
}
?> 