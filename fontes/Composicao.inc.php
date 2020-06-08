<?php
class Composicao {

    public $arquivo;
    public $locale;

    public function __construct(string $caminhoDoArquivo, string $locale) {
        $this->arquivo = $caminhoDoArquivo;
        $this->locale = $locale;
    }
}