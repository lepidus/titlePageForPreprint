<?php
class Pdf {
    private $path;
    const CPDF_PATH = __DIR__ . "/../tools/cpdf";
    
    public function __construct(string $path){
        if (!self::isPdf($path)) {
            throw new InvalidArgumentException('File is not PDF.'); 
        }

        $this->path = $path;
    }

    public function getNumberOfPages(): int {
        $numberOfPages = exec(self::CPDF_PATH . " -pages {$this->path}");
        
        return (int)$numberOfPages;
    }

    public function getPath(): string {
        return $this->path;
    }

    public static function isPdf(string $path): bool {
        return mime_content_type($path) == "application/pdf";
    }
}