<?php
class Submissao {

    private $status;
    private $doi;
    private $doiJournal;
    private $composições;
    private $autores;
    private $dataDeSubmissão;
    private $dataDePublicação;

    public function __construct(string $status, $doi, $doiJournal, string $autores, string $dataDeSubmissão, string $dataDePublicação, array $composições = null) {
        $this->status = $status;
        $this->doi = ((empty($doi)) ? ("Não informado") : ($doi));
        $this->doiJournal = ((empty($doiJournal)) ? ("Não informado") : ($doiJournal));
        $this->autores = $autores;
        $this->composições = $composições;
        $this->dataDeSubmissão = $dataDeSubmissão;
        $this->dataDePublicação = $dataDePublicação;
    }

    public function obterStatus(): string {
        return $this->status;
    }

    public function obterDOI(): string {
        return $this->doi;
    }

    public function obterDOIJournal(): string {
        return $this->doiJournal;
    }

    public function obterAutores(): string {
        return $this->autores;
    }

    public function obterComposições(): array {
        return $this->composições;
    }

    public function obterDataDeSubmissão(): string {
        return $this->dataDeSubmissão;
    }

    public function obterDataDePublicação(): string {
        return $this->dataDePublicação;
    }

}