<?php
use PHPUnit\Framework\TestCase;

final class FolhaDeRostoTest extends TestCase {
    
    private $status = "STATUS_QUEUED";
    private $doi = "10.1000/182";
    private $logo = "/caminho-logo/logo.png";
    
    private function obterFolhaDeRostoParaTeste(): FolhaDeRosto {
        return new FolhaDeRosto($this->status, $this->doi, $this->logo);
    }

    public function testTemStatusDeSubmissão(): void {
        $folhaDeRosto = $this->obterFolhaDeRostoParaTeste();
        $this->assertEquals($this->status, $folhaDeRosto->obterStatusDeSubmissão());
    }
    
    public function testTemDoi(): void {
        $folhaDeRosto = $this->obterFolhaDeRostoParaTeste();
        $this->assertEquals($this->doi, $folhaDeRosto->obterDOI());
    }

    public function testTemLogo(): void {
        $folhaDeRosto = $this->obterFolhaDeRostoParaTeste();
        $this->assertEquals($this->logo, $folhaDeRosto->obterLogo());
    }

    //logo
    //doi
    //checklist
    //estado -> submissão ou publicação?
}
?>