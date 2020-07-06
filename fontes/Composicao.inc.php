<?php
class Composicao {

    public $arquivo;
    public $locale;
    public $identificador;

    public function __construct(string $caminhoDoArquivo, string $locale, int $identificador) {
        $this->arquivo = $caminhoDoArquivo;
        $this->locale = $locale;
        $this->identificador = $identificador;
    }
}