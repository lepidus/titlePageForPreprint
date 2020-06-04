<?php
class Submissao {

    public $status;
    public $doi;
    public $caminhoDaComposição;

    public function __construct(string $status, string $doi, string $caminho) {
        $this->status = $status;
        $this->doi = $doi;
        $this->caminhoDaComposição = $caminho;
    }
}