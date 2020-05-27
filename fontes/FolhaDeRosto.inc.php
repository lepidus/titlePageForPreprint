<?php
class FolhaDeRosto {

    private $statusDaSubmissão;
    private $doi;
    private $logo;

    public function __construct(string $status, string $doi, string $logo) {
        $this->statusDaSubmissão = $status;
        $this->doi = $doi;
        $this->logo = $logo;
    }
    
    public function obterStatusDeSubmissão(): string {
        return $this->statusDaSubmissão;
    }

    public function obterDOI(): string {
        return $this->doi;
    }

    public function obterLogo(): string {
        return $this->logo;
    }
}
?>