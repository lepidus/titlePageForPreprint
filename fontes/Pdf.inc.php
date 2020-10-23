<?php
class Pdf {
    private $caminho;
    
    public function __construct(string $caminho){
        if (!self::éPdf($caminho)) {
            throw new InvalidArgumentException('arquivo não é um PDF'); 
        }
        $this->caminho = $caminho;
    }

    public function obterNúmeroDePáginas(): int {
        $linhaDaContagemDePáginas = shell_exec("pdfinfo ". $this->caminho ." | grep 'Pages:'");
        $paginasComoTexto = explode(":", $linhaDaContagemDePáginas);
        return (int)$paginasComoTexto[1];
    }

    public function obterCaminho(): string {
        return $this->caminho;
    }

    public static function éPdf(string $caminho): bool {
        return mime_content_type($caminho) == "application/pdf";
    }
}