<?php
use PHPUnit\Framework\TestCase;

final class PrensaDeComposicaoTest extends TestCase {

    public function testeComSomenteUmPdfFolhaDeRostoDeveSerIncluida() {
        $composiçãoComUmPdf = new //aqui entra a submissão do ops!
        $prensa = new PrensaDeComposicao($composiçãoComUmPdf);
        $prensa->inserirFolhasDeRosto();
        //assegure que na unica composição desta submissão tem a folha de rosto!
    }

    //com dois pdfs
    //sem pdf algum
    //com um pdf e um docx
}