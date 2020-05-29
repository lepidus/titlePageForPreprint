<?php
class Pdf {
    private $caminho;
    
    public function __construct(string $caminho){
        $this->caminho = $caminho;
    }

    public function obterNúmeroDePáginas(): int {
        $linhaDaContagemDePáginas = shell_exec("pdfinfo ". $this->caminho ." | grep 'Pages'");
        $paginasComoTexto = explode(":", $linhaDaContagemDePáginas);
        return (int)$paginasComoTexto[1];
    }

    public function obterCaminho(): string {
        return $this->caminho;
    }
}