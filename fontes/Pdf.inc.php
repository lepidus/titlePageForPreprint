<?php
class Pdf {
    private $caminho;
    
    public function __construct(string $caminho){
        $this->caminho = $caminho;
    }

    public function obterNúmeroDePáginas(): int {
        $linhaDaContagemDePáginas = shell_exec("pdfcpu/pdfcpu info ". $this->caminho ." | grep 'Page count'");
        $paginasComoTexto = explode(":", $linhaDaContagemDePáginas);
        return (int)$paginasComoTexto[1];
    }
}