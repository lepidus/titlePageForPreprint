<?php
class FolhaDeRosto {

    private $statusDaSubmissão;
    private $doi;
    private $logo;
    private $checklist;
    const CAMINHO_DO_PDFCPU = __DIR__. DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR. "pdfcpu" . DIRECTORY_SEPARATOR;

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

    public function inserir(pdf $pdf): pdf {
        shell_exec(self::CAMINHO_DO_PDFCPU .'pdfcpu pages insert -pages 1 -mode before '. $pdf->obterCaminho());
        shell_exec(self::CAMINHO_DO_PDFCPU .'pdfcpu stamp add -pages 1 -mode text \'umCarimboQualquer\' \'rot:0, pos:tc\' '. $pdf->obterCaminho());
        return $pdf;
    }
}
?>