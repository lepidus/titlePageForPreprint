<?php

class TradutorParaTestes implements Tradutor {

    private $mapeamentoDeIdiomas;

    public function __construct() {
        $this->mapeamentoDeIdiomas["en_US"] = array("common.status" => "Status",
                                                    "submissions.queued" => "Queued",
                                                    "metadata.property.displayName.doi" => "DOI",
                                                    "submission.submit.submissionChecklist" => "Submission Requirements",
                                                    "item1CheckList" => "The submission has not been previously published.",
                                                    "item2CheckList" => "Where available, URLs for the references have been provided.");

        $this->mapeamentoDeIdiomas["pt_BR"] = array("common.status" => "Situação",
                                                    "submissions.queued" => "Em fila",
                                                    "metadata.property.displayName.doi" => "DOI",
                                                    "submission.submit.submissionChecklist" => "Lista de verificação da submissão",
                                                    "item1CheckList" => "A submissão não foi publicado anteriormente.",
                                                    "item2CheckList" => "As URLs das referências foram fornecidas.");
        
    }

    public function traduzir($chave, $locale) {
        $idioma = $this->mapeamentoDeIdiomas[$locale];
        return $idioma[$chave];
    }

    public function obterCheckListTraduzida($locale) {
        return array($this->mapeamentoDeIdiomas[$locale]["item1CheckList"], $this->mapeamentoDeIdiomas[$locale]["item2CheckList"]);
    }
}