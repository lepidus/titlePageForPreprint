<?php
class Composicao {

    public $arquivo;
    public $locale;
    public $identificador;
    public $revisão;

    public function __construct(string $caminhoDoArquivo, string $locale, int $identificador, int $revisão) {
        $this->arquivo = $caminhoDoArquivo;
        $this->locale = $locale;
        $this->identificador = $identificador;
        $this->revisão = $revisão;
    }
}