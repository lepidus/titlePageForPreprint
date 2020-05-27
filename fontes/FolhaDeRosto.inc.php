<?php
class FolhaDeRosto {

    private $statusDaSubmissão;
    private $doi;
    private $logo;
    private $checklist;

    public function __construct(string $status, string $doi, string $logo, array $checklist) {
        $this->statusDaSubmissão = $status;
        $this->doi = $doi;
        $this->logo = $logo;
        $this->checklist = $checklist;
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

    public function obterChecklist(): array {
        return $this->checklist;
    }
}
?>