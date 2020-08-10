<?php

class TradutorPKP implements Tradutor {

    private $contexto;
    private $submissão;

    public function __construct($contexto, $submissão) {
        $this->contexto = $contexto;
        $this->submissão = $submissão;

    }

    public function traduzir($chave, $locale) {
        AppLocale::requireComponents(LOCALE_COMPONENT_PKP_COMMON, $locale);
        AppLocale::requireComponents(LOCALE_COMPONENT_PKP_SUBMISSION, $locale);
        AppLocale::requireComponents(LOCALE_COMPONENT_APP_SUBMISSION, $locale);

        return __($chave, null, $locale);
    }

    public function obterCheckListTraduzida($locale) {
        $checklistBruta = $this->contexto->getData('submissionChecklist')[$locale];
        foreach ($checklistBruta as $itemDaChecklist) {
			$checklist[] = $itemDaChecklist['content'];
        }
        return $checklist;
    }
    public function obterTítuloTraduzido($locale){
        return $this->submissão->getTitle($locale);
    }

    public function obterDataTraduzida($data, $locale){
        $dateFormat = ['en_US' => '%Y/%m/%d', 'es_ES' => '%d/%m/%Y', 'pt_BR' => '%d/%m/%Y'];
        return strftime($dateFormat[$locale], $data);
    }
}