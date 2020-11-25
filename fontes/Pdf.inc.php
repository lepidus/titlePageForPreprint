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

    public function obterOrientacaoPagina(): string {
        $linhaDoTamanhoPagina = shell_exec("pdfinfo -box -f 1 -l 1 {$this->caminho} | grep '1 size: '");
        preg_match('~(\d+\.\d+) x (\d+\.\d+)~', $linhaDoTamanhoPagina, $ocorrencias);
        $largura = (float) $ocorrencias[1];
        $altura = (float) $ocorrencias[2];

        return ($largura > $altura) ? ("L") : ("P");
    }

    public function obterCaminho(): string {
        return $this->caminho;
    }

    public static function éPdf(string $caminho): bool {
        return mime_content_type($caminho) == "application/pdf";
    }
}