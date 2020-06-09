<?php

class TradutorParaTestes implements Tradutor {

    private $mapeamentoDeIdiomas;

    public function __construct() {
        $this->mapeamentoDeIdiomas["en_US"] = array("common.status" => "Status",
                                                    "submissions.queued" => "Queued",
                                                    "plugins.pubIds.doi.editor.doi" => "DOI",
                                                    "submission.submit.submissionChecklist" => "Submission Requirements");

        $this->mapeamentoDeIdiomas["pt_BR"] = array("common.status" => "Situação",
                                                    "submissions.queued" => "Em fila",
                                                    "plugins.pubIds.doi.editor.doi" => "DOI",
                                                    "submission.submit.submissionChecklist" => "Lista de verificação da submissão");
        
    }

    public function traduzir($chave, $locale) {
        $idioma = $this->mapeamentoDeIdiomas[$locale];
        return $idioma[$chave];
    }
}