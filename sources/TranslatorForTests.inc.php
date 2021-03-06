<?php

class TranslatorForTests implements Translator {

    private $languageMap;

    public function __construct() {
        $this->languageMap["en_US"] = array("common.status" => "Status",
                                                    "publication.relation.none" => "Preprint has not been submitted for publication",
                                                    "publication.relation.submitted" => "Preprint has been submitted for publication in journal",
                                                    "publication.relation.published" => "Preprint has been published in a journal as an article",
                                                    "metadata.property.displayName.doi" => "DOI",
                                                    "plugins.generic.titlePageForPreprint.checklistLabel" => "This preprint was submitted under the following conditions",
                                                    "plugins.generic.titlePageForPreprint.submissionDate" => "Date submitted",
                                                    "plugins.generic.titlePageForPreprint.publicationDate" => "Date published",
                                                    "plugins.generic.titlePageForPreprint.headerText" => "SciELO Preprints - this preprint has not been peer reviewed",
                                                    "item1CheckList" => "The submission has not been previously published.",
                                                    "item2CheckList" => "Where available, URLs for the references have been provided.",
                                                    "title" => "So spoke Zaratustra");

        $this->languageMap["pt_BR"] = array("common.status" => "Situação",
                                                    "publication.relation.none" => "O preprint não foi submetido para publicação",
                                                    "publication.relation.submitted" => "O preprint foi submetido para publicação em um periódico",
                                                    "publication.relation.published" => "O preprint foi publicado em um periódico como um artigo",
                                                    "metadata.property.displayName.doi" => "DOI",
                                                    "plugins.generic.titlePageForPreprint.checklistLabel" => "Este preprint foi submetido sob as seguintes condições",
                                                    "plugins.generic.titlePageForPreprint.submissionDate" => "Data de submissão",
                                                    "plugins.generic.titlePageForPreprint.publicationDate" => "Data de postagem",
                                                    "plugins.generic.titlePageForPreprint.headerText" => "SciELO Preprints - este preprint não foi revisado por pares",
                                                    "item1CheckList" => "A submissão não foi publicado anteriormente.",
                                                    "item2CheckList" => "As URLs das referências foram fornecidas.",
                                                    "title" => "Assim Falou Zaratustra");
        
    }

    public function translate($key, $locale, $params = null) {
        $language = $this->languageMap[$locale];
        return $language[$key];
    }

    public function getTranslatedChecklist($locale) {
        return array($this->languageMap[$locale]["item1CheckList"], $this->languageMap[$locale]["item2CheckList"]);
    }

    public function getTranslatedTitle($locale) {
        return $this->languageMap[$locale]["title"];
    }
    
    public function getTranslatedDate($date){
        return $date;
    }
}