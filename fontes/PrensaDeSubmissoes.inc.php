<?php

interface PrensaDeSubmissoes {
    public function __construct(string $logoParaFolhaDeRosto, Submissao $submissão, Tradutor $tradutor);
    public function inserirFolhasDeRosto();
}