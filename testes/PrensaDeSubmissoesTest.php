<?php
require_once ("ManipulacaoDePdfTest.php");

class PrensaDeSubmissoesTest extends ManipulacaoDePdfTest {

    public function testeComSomenteUmPdfFolhaDeRostoDeveSerIncluida(): void {   
        $caminhoDaComposição = $this->caminhoDoPdfTeste;
        $submissão = new Submissao($this->status, $this->doi, array($caminhoDaComposição));
        $prensa = new PrensaDeSubmissoes($this->logo, $this->checklist, $submissão);

        $prensa->inserirFolhasDeRosto();
        
        $pdfDaComposição = new Pdf($caminhoDaComposição);
        $this->assertEquals(2, $pdfDaComposição->obterNúmeroDePáginas());
    }

    public function testeComMaisDeUmPdfFolhaDeRostoDeveSerIncluida(): void {
        $caminhoDaPrimeiraComposição = $this->caminhoDoPdfTeste;
        $caminhoDaSegundaComposição = $this->caminhoDoPdfTeste2;
        $submissão = new Submissao($this->status, $this->doi, array($caminhoDaPrimeiraComposição, $caminhoDaSegundaComposição));
        
        $prensa = new PrensaDeSubmissoes($this->logo, $this->checklist, $submissão);
        $prensa->inserirFolhasDeRosto();

        $pdfDaPrimeiraComposição = new Pdf($caminhoDaPrimeiraComposição);
        $pdfDaSegundaComposição = new Pdf($caminhoDaSegundaComposição);

        $this->assertEquals(2, $pdfDaPrimeiraComposição->obterNúmeroDePáginas());
        $this->assertEquals(3, $pdfDaSegundaComposição->obterNúmeroDePáginas());
    }

    public function testeDeveIgnorarArquivosNãoPdf(): void {
        $caminhoDaPrimeiraComposição = $this->caminhoDoPdfTeste;
        $caminhoDaSegundaComposição = "testes" . DIRECTORY_SEPARATOR . "arquivoNaoPdf.odt";
        $submissão = new Submissao($this->status, $this->doi, array($caminhoDaPrimeiraComposição, $caminhoDaSegundaComposição));
        
        $hashDaComposiçãoNãoPdf = md5_file($caminhoDaSegundaComposição);
        $prensa = new PrensaDeSubmissoes($this->logo, $this->checklist, $submissão);
        $prensa->inserirFolhasDeRosto();

        $pdfDaPrimeiraComposição = new Pdf($caminhoDaPrimeiraComposição);

        $this->assertEquals(2, $pdfDaPrimeiraComposição->obterNúmeroDePáginas());
        $this->assertEquals($hashDaComposiçãoNãoPdf, md5_file($caminhoDaSegundaComposição));
    }
}