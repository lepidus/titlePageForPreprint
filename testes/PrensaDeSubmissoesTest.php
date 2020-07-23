<?php
require_once ("ManipulacaoDePdfTest.php");

class PrensaDeSubmissoesTest extends ManipulacaoDePdfTest {

    public function testeComSomenteUmPdfFolhaDeRostoDeveSerIncluida(): void {   
        $caminhoDaComposição = $this->caminhoDoPdfTeste;
        $composição = new Composicao($caminhoDaComposição, $this->locale, 1, 2);
        $submissão = new Submissao($this->status, $this->doi, $this->autores, $this->dataDeSubmissão, array($composição));
        $prensa = new PrensaDeSubmissoesParaTestes($this->logo, $submissão, $this->tradutor);

        $prensa->inserirFolhasDeRosto();
        
        $pdfDaComposição = new Pdf($caminhoDaComposição);
        $this->assertEquals(2, $pdfDaComposição->obterNúmeroDePáginas());
    }

    public function testeComMaisDeUmPdfFolhaDeRostoDeveSerIncluida(): void {
        $caminhoDaPrimeiraComposição = $this->caminhoDoPdfTeste;
        $caminhoDaSegundaComposição = $this->caminhoDoPdfTeste2;
        $primeiraComposição = new Composicao($caminhoDaPrimeiraComposição, $this->locale, 2, 2);
        $segundaComposição = new Composicao($caminhoDaSegundaComposição, "en_US", 3, 2);
        $submissão = new Submissao($this->status, $this->doi, $this->autores, $this->dataDeSubmissão, array($primeiraComposição, $segundaComposição));
        
        $prensa = new PrensaDeSubmissoesParaTestes($this->logo, $submissão, $this->tradutor);
        $prensa->inserirFolhasDeRosto();

        $pdfDaPrimeiraComposição = new Pdf($caminhoDaPrimeiraComposição);
        $pdfDaSegundaComposição = new Pdf($caminhoDaSegundaComposição);

        $this->assertEquals(2, $pdfDaPrimeiraComposição->obterNúmeroDePáginas());
        $this->assertEquals(3, $pdfDaSegundaComposição->obterNúmeroDePáginas());
    }

    public function testeDeveIgnorarArquivosNãoPdf(): void {
        $caminhoDaPrimeiraComposição = $this->caminhoDoPdfTeste;
        $caminhoDaSegundaComposição = "testes" . DIRECTORY_SEPARATOR . "arquivoNaoPdf.odt";
        $primeiraComposição = new Composicao($caminhoDaPrimeiraComposição, $this->locale, 4, 2);
        $segundaComposição = new Composicao($caminhoDaSegundaComposição, $this->locale, 5, 2);
        $submissão = new Submissao($this->status, $this->doi, $this->autores, $this->dataDeSubmissão, array($primeiraComposição, $segundaComposição));

        $hashDaComposiçãoNãoPdf = md5_file($caminhoDaSegundaComposição);
        $prensa = new PrensaDeSubmissoesParaTestes($this->logo, $submissão, $this->tradutor);
        $prensa->inserirFolhasDeRosto();

        $pdfDaPrimeiraComposição = new Pdf($caminhoDaPrimeiraComposição);

        $this->assertEquals(2, $pdfDaPrimeiraComposição->obterNúmeroDePáginas());
        $this->assertEquals($hashDaComposiçãoNãoPdf, md5_file($caminhoDaSegundaComposição));
    }
}