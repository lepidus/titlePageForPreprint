<?php

use PHPUnit\Framework\TestCase;

class SubmissaoTest extends TestCase {

    
    private $status = "publication.relation.none";
    private $doi = "10.1000/182";
    private $doiJournal = "https://doi.org/10.1590/1413-81232020256.1.10792020";
    private $autores = "Clarice Linspector, Atila Iamarino";
    private $dataDeSubmissão = "10/06/2020";
    private $dataDePublicação = "12/06/2020";
    private $composições = array();
    
    private function obterSubmissãoParaTeste() {
        return new Submissao($this->status, $this->doi, $this->doiJournal, $this->autores, $this->dataDeSubmissão, $this->dataDePublicação, $this->composições);
    }

    public function testeTemStatusDeSubmissão(): void {
        $submissão = $this->obterSubmissãoParaTeste();
        $this->assertEquals($this->status, $submissão->obterStatus());
    }
    
    public function testeTemDoi(): void {
        $submissão = $this->obterSubmissãoParaTeste();
        $this->assertEquals($this->doi, $submissão->obterDOI());
    }

    public function testeDoiNãoInformado(): void {
        $submissão = new Submissao($this->status, null, $this->doiJournal, $this->autores, $this->dataDeSubmissão, $this->dataDePublicação, $this->composições);
        $this->assertEquals("Não informado", $submissão->obterDOI());
    }

    public function testeTemDoiJournal(): void {
        $submissão = $this->obterSubmissãoParaTeste();
        $this->assertEquals($this->doiJournal, $submissão->obterDOIJournal());
    }

    public function testeDoiJournalNãoInformado(): void {
        $submissão = new Submissao($this->status, $this->doi, null, $this->autores, $this->dataDeSubmissão, $this->dataDePublicação, $this->composições);
        $this->assertEquals("Não informado", $submissão->obterDOIJournal());
    }

    public function testeTemAutores(): void {
        $submissão = $this->obterSubmissãoParaTeste();
        $this->assertEquals($this->autores, $submissão->obterAutores());
    }

    public function testeTemComposições(): void {
        $submissão = $this->obterSubmissãoParaTeste();
        $this->assertEquals($this->composições, $submissão->obterComposições());
    }

    public function testeDataDeSubmissão(): void {
        $submissão = $this->obterSubmissãoParaTeste();
        $this->assertEquals($this->dataDeSubmissão, $submissão->obterDataDeSubmissão());
    }

    public function testeDataDePublicação(): void {
        $submissão = $this->obterSubmissãoParaTeste();
        $this->assertEquals($this->dataDePublicação, $submissão->obterDataDePublicação());
    }
}
?>