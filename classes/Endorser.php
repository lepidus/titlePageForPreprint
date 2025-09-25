<?php

namespace APP\plugins\generic\titlePageForPreprint\classes;

class Endorser
{
    private $name;
    private $orcid;

    public function __construct(string $name, string $orcid)
    {
        $this->name = $name;
        $this->orcid = $orcid;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getOrcid(): string
    {
        return $this->orcid;
    }
}
