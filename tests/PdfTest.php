<?php

use PKP\tests\PKPTestCase;
use APP\plugins\generic\titlePageForPreprint\classes\Pdf;
use APP\plugins\generic\titlePageForPreprint\tests\PdfHandlingTest;

class PdfTest extends PKPTestCase
{
    public function testGetNumberOfPagesWhenPdfHasOnePage(): void
    {
        $pdf = new Pdf(PdfHandlingTest::TESTS_DIRECTORY . PdfHandlingTest::ASSETS_DIRECTORY .  "testOnePage.pdf");
        $this->assertEquals(1, $pdf->getNumberOfPages());
    }

    public function testGetNumberOfPagesWhenPdfHasTwoPages(): void
    {
        $pdf = new Pdf(PdfHandlingTest::TESTS_DIRECTORY . PdfHandlingTest::ASSETS_DIRECTORY . "testTwoPages.pdf");
        $this->assertEquals(2, $pdf->getNumberOfPages());
    }

    public function testGetPath(): void
    {
        $path = PdfHandlingTest::TESTS_DIRECTORY . PdfHandlingTest::ASSETS_DIRECTORY . "testTwoPages.pdf";
        $pdf = new Pdf(PdfHandlingTest::TESTS_DIRECTORY . PdfHandlingTest::ASSETS_DIRECTORY . "testTwoPages.pdf");
        $this->assertEquals($path, $pdf->getPath());
    }

    public function testCreateFileNotPdf(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $pdf = new Pdf(PdfHandlingTest::TESTS_DIRECTORY . PdfHandlingTest::ASSETS_DIRECTORY . "logo_noAlphaChannel.png");
    }

    public function testIsPdf(): void
    {
        $this->assertTrue(Pdf::isPdf(PdfHandlingTest::TESTS_DIRECTORY . PdfHandlingTest::ASSETS_DIRECTORY . "testTwoPages.pdf"));
    }

    public function testIsNotPdf(): void
    {
        $this->assertFalse(Pdf::isPdf(PdfHandlingTest::TESTS_DIRECTORY . PdfHandlingTest::ASSETS_DIRECTORY . "fileNotPdf.odt"));
    }
}
?> 