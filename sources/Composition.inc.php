<?php
class Composition {

    public $file;
    public $locale;
    public $identifier;
    public $revision;

    public function __construct(string $filePath, string $locale, int $identifier, int $revision) {
        $this->file = $filePath;
        $this->locale = $locale;
        $this->identifier = $identifier;
        $this->revision = $revision;
    }
}