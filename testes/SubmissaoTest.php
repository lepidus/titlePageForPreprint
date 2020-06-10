<?php

use PHPUnit\Framework\TestCase;

class SubmissaoTest extends TestCase {

    
    private $status = 'submissions.queued';
    private $doi = "10.1000/182";
    private $autores = "Clarice Linspector, Atila Iamarino";
    private $composições = array();
    
    private function obterSubmissãoParaTeste() {
        return new Submissao($this->status, $this->doi, $this->autores, $this->composições);
    }

    public function testeTemStatusDeSubmissão(): void {
        $submissão = $this->obterSubmissãoParaTeste();
        $this->assertEquals($this->status, $submissão->obterStatus());
    }
    
    public function testeTemDoi(): void {
        $submissão = $this->obterSubmissãoParaTeste();
        $this->assertEquals($this->doi, $submissão->obterDOI());
    }

    public function testeTemAutores(): void {
        $submissão = $this->obterSubmissãoParaTeste();
        $this->assertEquals($this->autores, $submissão->obterAutores());
    }

    public function testeTemComposições(): void {
        $submissão = $this->obterSubmissãoParaTeste();
        $this->assertEquals($this->composições, $submissão->obterComposições());
    }
}
?>