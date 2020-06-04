<?php
require_once ("ManipulacaoDePdfTest.php");

final class PrensaDeSubmissoesTest extends ManipulacaoDePdfTest {
    private $logoParaFolhaDeRosto = 'testes' . DIRECTORY_SEPARATOR . "logo_semCanalAlfa.png";
    private $checklist = array("A submissão não foi publicado anteriormente.", "As URLs das referências foram fornecidas.");
    private $status = "STATUS_QUEUED";
    private $doi = "10.1000/182";

    public function testeComSomenteUmPdfFolhaDeRostoDeveSerIncluida() {   
        $caminhoDaComposição = $this->caminhoDoPdfTeste;
        $submissãoComUmPdf = new Submissao($this->status, $this->doi, $caminhoDaComposição);
        $submissões = array();
        $submissões[] = $submissãoComUmPdf;
        $prensa = new PrensaDeSubmissoes($this->logoParaFolhaDeRosto, $this->checklist, $submissões);

        $prensa->inserirFolhasDeRosto();
        
        $submissõesProcessadas = $prensa->obterSubmissões();
        $submissãoProcessada = $submissõesProcessadas[0];
        $pdfDaComposição = new Pdf($caminhoDaComposição);
        $this->assertEquals(2, $pdfDaComposição->obterNúmeroDePáginas());
    }

    public function testeComMaisDeUmPdfFolhaDeRostoDeveSerIncluida() {
        $caminhoDaPrimeiraComposição = $this->caminhoDoPdfTeste;
        $caminhoDaSegundaComposição = $this->caminhoDoPdfTeste2;
        $submissãoDoPrimeiroPdf = new Submissao($this->status, $this->doi, $caminhoDaPrimeiraComposição);
        $submissãoDoSegundoPdf = new Submissao($this->status, $this->doi, $caminhoDaSegundaComposição);
        $submissões = array($submissãoDoPrimeiroPdf, $submissãoDoSegundoPdf);
        
        $prensa = new PrensaDeSubmissoes($this->logoParaFolhaDeRosto, $this->checklist, $submissões);

        $prensa->inserirFolhasDeRosto();

        $pdfDaPrimeiraComposição = new Pdf($caminhoDaPrimeiraComposição);
        $pdfDaSegundaComposição = new Pdf($caminhoDaSegundaComposição);

        $this->assertEquals(2, $pdfDaPrimeiraComposição->obterNúmeroDePáginas());
        $this->assertEquals(3, $pdfDaSegundaComposição->obterNúmeroDePáginas());

    }
}