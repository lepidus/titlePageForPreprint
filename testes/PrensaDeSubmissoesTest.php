<?php
require_once ("ManipulacaoDePdfTest.php");

final class PrensaDeSubmissoesTest extends ManipulacaoDePdfTest {

    public function testeComSomenteUmPdfFolhaDeRostoDeveSerIncluida() {
        $logoParaFolhaDeRosto = 'testes' . DIRECTORY_SEPARATOR . "logo_semCanalAlfa.png";
        $checklist = array("A submissão não foi publicado anteriormente.", "As URLs das referências foram fornecidas.");
        $caminhoDaComposição = $this->caminhoDoPdfTeste;
        $submissãoComUmPdf = new Submissao("STATUS_QUEUED", "10.1000/182", $caminhoDaComposição);
        $submissões = array();
        $submissões[] = $submissãoComUmPdf;
        $prensa = new PrensaDeSubmissoes($logoParaFolhaDeRosto, $checklist, $submissões);

        $prensa->inserirFolhasDeRosto();
        
        $submissõesProcessadas = $prensa->obterSubmissões();
        $submissãoProcessada = $submissõesProcessadas[0];
        $pdfDaComposição = new Pdf($caminhoDaComposição);
        $this->assertEquals(2, $pdfDaComposição->obterNúmeroDePáginas());
    }
}