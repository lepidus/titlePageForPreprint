<?php

class TradutorParaTestes implements Tradutor {

    private $mapeamentoDeIdiomas;

    public function __construct() {
        $this->mapeamentoDeIdiomas["en_US"] = array("common.status" => "Status",
                                                    "publication.relation.none" => "Preprint has not been submitted for publication",
                                                    "publication.relation.submitted" => "Preprint has been submitted for publication in journal",
                                                    "publication.relation.published" => "Preprint has been published in a journal as an article",
                                                    "metadata.property.displayName.doi" => "DOI",
                                                    "plugins.generic.folhaDeRostoDoPDF.rotuloDaChecklist" => "This preprint was submitted under the following conditions",
                                                    "plugins.generic.folhaDeRostoDoPDF.dataSubmissao" => "Date submitted",
                                                    "plugins.generic.folhaDeRostoDoPDF.dataPublicacao" => "Date published",
                                                    "plugins.generic.folhaDeRostoDoPDF.textoCabecalho" => "SciELO Preprints - this preprint has not been peer reviewed",
                                                    "item1CheckList" => "The submission has not been previously published.",
                                                    "item2CheckList" => "Where available, URLs for the references have been provided.",
                                                    "titulo" => "So spoke Zaratustra");

        $this->mapeamentoDeIdiomas["pt_BR"] = array("common.status" => "Situação",
                                                    "publication.relation.none" => "O preprint não foi submetido para publicação",
                                                    "publication.relation.submitted" => "O preprint foi submetido para publicação em um periódico",
                                                    "publication.relation.published" => "O preprint foi publicado em um periódico como um artigo",
                                                    "metadata.property.displayName.doi" => "DOI",
                                                    "plugins.generic.folhaDeRostoDoPDF.rotuloDaChecklist" => "Este preprint foi submetido sob as seguintes condições",
                                                    "plugins.generic.folhaDeRostoDoPDF.dataSubmissao" => "Data de submissão",
                                                    "plugins.generic.folhaDeRostoDoPDF.dataPublicacao" => "Data de postagem",
                                                    "plugins.generic.folhaDeRostoDoPDF.textoCabecalho" => "SciELO Preprints - este preprint não foi revisado por pares",
                                                    "item1CheckList" => "A submissão não foi publicado anteriormente.",
                                                    "item2CheckList" => "As URLs das referências foram fornecidas.",
                                                    "titulo" => "Assim Falou Zaratustra");
        
    }

    public function traduzir($chave, $locale, $params = null) {
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