<?php
class Submissao {

    private $status;
    private $doi;
    private $composições;
    private $autores;
    private $dataDeSubmissão;

    public function __construct(string $status, $doi, string $autores, string $dataDeSubmissão, array $composições = null) {
        $this->status = $status;
        $this->doi = $doi;
        $this->autores = $autores;
        $this->composições = $composições;
        $this->dataDeSubmissão = $dataDeSubmissão;
    }

    public function obterStatus(): string {
        return $this->status;
    }

    public function obterDOI(): string {
        return $this->doi;
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

}