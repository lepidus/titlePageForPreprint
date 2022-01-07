<?php
class Pdf {
    private $path;
    private $originalPath;
    
    public function __construct(string $path){
        if (!self::isPdf($path)) {
            throw new InvalidArgumentException('File is not PDF.'); 
        }

        $this->originalPath = $path;
        $this->path = $this->generateFileCopy($path);
    }

    public function getNumberOfPages(): int {
        $countPagesCommandLine = shell_exec("pdfinfo ". $this->path ." | grep 'Pages:'");
        $pagesAsText = explode(":", $countPagesCommandLine);
        return (int)$pagesAsText[1];
    }

    public function getPageOrientation(): string {
        $sizePagesCommandLine = shell_exec("pdfinfo -box -f 1 -l 1 {$this->path} | grep '1 size: '");
        preg_match('~(\d+(\.\d+)?) x (\d+(\.\d+)?)~', $sizePagesCommandLine, $occurrences);
        $width = (float) $occurrences[1];
        $height = (float) $occurrences[3];

        return ($width > $height) ? ("L") : ("P");
    }

    public function getPath(): string {
        return $this->path;
    }

    public function getOriginalPath(): string {
        return $this->originalPath;
    }

    public static function isPdf(string $path): bool {
        return mime_content_type($path) == "application/pdf";
    }

    private static function generateFileCopy($path): string {
        $securityCopy = "original-file-copy.pdf";
        copy($path, $securityCopy);
        return $securityCopy;
    }
}