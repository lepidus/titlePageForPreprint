<?php
require_once ("ManipulacaoDePdfTest.php");

class PrensaDeSubmissoesTest extends ManipulacaoDePdfTest {

    public function testeComSomenteUmPdfFolhaDeRostoDeveSerIncluida(): void {   
        $caminhoDaComposição = $this->caminhoDoPdfTeste;
        $composição = new Composicao($caminhoDaComposição, $this->locale);
        $submissão = new Submissao($this->status, $this->doi, array($composição));
        $prensa = new PrensaDeSubmissoes($this->logo, $submissão, $this->tradutor);

        $prensa->inserirFolhasDeRosto();
        
        $pdfDaComposição = new Pdf($caminhoDaComposição);
        $this->assertEquals(2, $pdfDaComposição->obterNúmeroDePáginas());
    }

    public function testeComMaisDeUmPdfFolhaDeRostoDeveSerIncluida(): void {
        $caminhoDaPrimeiraComposição = $this->caminhoDoPdfTeste;
        $caminhoDaSegundaComposição = $this->caminhoDoPdfTeste2;
        $primeiraComposição = new Composicao($caminhoDaPrimeiraComposição, $this->locale);
        $segundaComposição = new Composicao($caminhoDaSegundaComposição, "en_US");
        $submissão = new Submissao($this->status, $this->doi, array($primeiraComposição, $segundaComposição));
        
        $prensa = new PrensaDeSubmissoes($this->logo, $submissão, $this->tradutor);
        $prensa->inserirFolhasDeRosto();

        $pdfDaPrimeiraComposição = new Pdf($caminhoDaPrimeiraComposição);
        $pdfDaSegundaComposição = new Pdf($caminhoDaSegundaComposição);

        $this->assertEquals(2, $pdfDaPrimeiraComposição->obterNúmeroDePáginas());
        $this->assertEquals(3, $pdfDaSegundaComposição->obterNúmeroDePáginas());
    }

    public function testeDeveIgnorarArquivosNãoPdf(): void {
        $caminhoDaPrimeiraComposição = $this->caminhoDoPdfTeste;
        $caminhoDaSegundaComposição = "testes" . DIRECTORY_SEPARATOR . "arquivoNaoPdf.odt";
        $primeiraComposição = new Composicao($caminhoDaPrimeiraComposição, $this->locale);
        $segundaComposição = new Composicao($caminhoDaSegundaComposição, $this->locale);
        $submissão = new Submissao($this->status, $this->doi, array($primeiraComposição, $segundaComposição));

        $hashDaComposiçãoNãoPdf = md5_file($caminhoDaSegundaComposição);
        $prensa = new PrensaDeSubmissoes($this->logo, $submissão, $this->tradutor);
        $prensa->inserirFolhasDeRosto();

        $pdfDaPrimeiraComposição = new Pdf($caminhoDaPrimeiraComposição);

        $this->assertEquals(2, $pdfDaPrimeiraComposição->obterNúmeroDePáginas());
        $this->assertEquals($hashDaComposiçãoNãoPdf, md5_file($caminhoDaSegundaComposição));
    }
}