<?php
class Submissao {

    public $status;
    public $doi;
    public $composições;

    public function __construct(string $status, $doi, array $composições) {
        $this->status = $status;
        $this->doi = $doi;

        if (empty($doi)) {
            $this->doi = "Não informado";
        }

        $this->composições = $composições;
    }
}