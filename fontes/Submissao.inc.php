<?php
class Submissao {

    public $status;
    public $doi;
    public $composições;

    public function __construct(string $status, string $doi, array $composições) {
        $this->status = $status;
        $this->doi = $doi;
        $this->composições = $composições;
    }
}