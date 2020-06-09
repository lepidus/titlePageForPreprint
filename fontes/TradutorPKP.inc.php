<?php

class TradutorPKP implements Tradutor {

    private $contexto;

    public function __construct($contexto) {
        $this->contexto = $contexto;
    }

    public function traduzir($chave, $locale) {
        AppLocale::requireComponents(LOCALE_COMPONENT_PKP_COMMON, $locale);
        AppLocale::requireComponents(LOCALE_COMPONENT_PKP_SUBMISSION, $locale);
        return __($chave, null, $locale);
    }

    public function obterCheckListTraduzida($locale) {
        $checklistBruta = $this->contexto->getLocalizedData('submissionChecklist', $locale);
        foreach ($checklistBruta as $itemDaChecklist) {
			$checklist[] = $itemDaChecklist['content'];
        }
        return $checklist;
    }
}