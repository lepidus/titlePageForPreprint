<?php

class TradutorParaTestes implements Tradutor {

    public function traduzir($chave, $locale) {
        if ($locale == "en_US") {
            return "Status";
        }
        else {
            return "Situação";
        }
    }
}