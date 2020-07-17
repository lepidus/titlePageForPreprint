<?php

class TradutorParaTestes implements Tradutor {

    private $mapeamentoDeIdiomas;

    public function __construct() {
        $this->mapeamentoDeIdiomas["en_US"] = array("common.status" => "Status",
                                                    "submissions.queued" => "Queued",
                                                    "metadata.property.displayName.doi" => "DOI",
                                                    "plugins.generic.folhaDeRostoDoPDF.rotuloDaChecklist" => "This preprint was submitted under the following conditions",
                                                    "common.dateSubmitted" => "Date submitted",
                                                    "item1CheckList" => "The submission has not been previously published.",
                                                    "item2CheckList" => "Where available, URLs for the references have been provided.",
                                                    "titulo" => "So spoke Zaratustra");

        $this->mapeamentoDeIdiomas["pt_BR"] = array("common.status" => "Situação",
                                                    "submissions.queued" => "Em fila",
                                                    "metadata.property.displayName.doi" => "DOI",
                                                    "plugins.generic.folhaDeRostoDoPDF.rotuloDaChecklist" => "Este preprint foi submetido sob as seguintes condições",
                                                    "common.dateSubmitted" => "Data de submissão",
                                                    "item1CheckList" => "A submissão não foi publicado anteriormente.",
                                                    "item2CheckList" => "As URLs das referências foram fornecidas.",
                                                    "titulo" => "Assim Falou Zaratustra");
        
    }

    public function traduzir($chave, $locale) {
        $idioma = $this->mapeamentoDeIdiomas[$locale];
        return $idioma[$chave];
    }

    public function obterCheckListTraduzida($locale) {
        return array($this->mapeamentoDeIdiomas[$locale]["item1CheckList"], $this->mapeamentoDeIdiomas[$locale]["item2CheckList"]);
    }

    public function obterTítuloTraduzido($locale) {
        return $this->mapeamentoDeIdiomas[$locale]["titulo"];
    }
    public function obterDataTraduzida($data){
        return $data;
    }
}