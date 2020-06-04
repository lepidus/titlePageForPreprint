<?php
require_once ("ManipulacaoDePdfTest.php");

class PrensaDeSubmissoesTest extends ManipulacaoDePdfTest {

    public function testeComSomenteUmPdfFolhaDeRostoDeveSerIncluida(): void {   
        $caminhoDaComposição = $this->caminhoDoPdfTeste;
        $submissãoComUmPdf = new Submissao($this->status, $this->doi, $caminhoDaComposição);
        $submissões = array();
        $submissões[] = $submissãoComUmPdf;
        $prensa = new PrensaDeSubmissoes($this->logo, $this->checklist, $submissões);

        $prensa->inserirFolhasDeRosto();
        
        $pdfDaComposição = new Pdf($caminhoDaComposição);
        $this->assertEquals(2, $pdfDaComposição->obterNúmeroDePáginas());
    }

    public function testeComMaisDeUmPdfFolhaDeRostoDeveSerIncluida(): void {
        $caminhoDaPrimeiraComposição = $this->caminhoDoPdfTeste;
        $caminhoDaSegundaComposição = $this->caminhoDoPdfTeste2;
        $submissãoDoPrimeiroPdf = new Submissao($this->status, $this->doi, $caminhoDaPrimeiraComposição);
        $submissãoDoSegundoPdf = new Submissao($this->status, $this->doi, $caminhoDaSegundaComposição);
        $submissões = array($submissãoDoPrimeiroPdf, $submissãoDoSegundoPdf);
        
        $prensa = new PrensaDeSubmissoes($this->logo, $this->checklist, $submissões);
        $prensa->inserirFolhasDeRosto();

        $pdfDaPrimeiraComposição = new Pdf($caminhoDaPrimeiraComposição);
        $pdfDaSegundaComposição = new Pdf($caminhoDaSegundaComposição);

        $this->assertEquals(2, $pdfDaPrimeiraComposição->obterNúmeroDePáginas());
        $this->assertEquals(3, $pdfDaSegundaComposição->obterNúmeroDePáginas());
    }

    public function testeDeveIgnorarArquivosNãoPdf(): void {
        $caminhoDaPrimeiraComposição = $this->caminhoDoPdfTeste;
        $caminhoDaSegundaComposição = "testes" . DIRECTORY_SEPARATOR . "arquivoNaoPdf.odt";
        $submissãoDoPrimeiroPdf = new Submissao($this->status, $this->doi, $caminhoDaPrimeiraComposição);
        $submissãoDoOdt = new Submissao($this->status, $this->doi, $caminhoDaSegundaComposição);
        $submissões = array($submissãoDoPrimeiroPdf, $submissãoDoOdt);
        
        $hashDaComposiçãoNãoPdf = md5_file($caminhoDaSegundaComposição);
        $prensa = new PrensaDeSubmissoes($this->logo, $this->checklist, $submissões);
        $prensa->inserirFolhasDeRosto();

        $pdfDaPrimeiraComposição = new Pdf($caminhoDaPrimeiraComposição);

        $this->assertEquals(2, $pdfDaPrimeiraComposição->obterNúmeroDePáginas());
        $this->assertEquals($hashDaComposiçãoNãoPdf, md5_file($caminhoDaSegundaComposição));
    }
}