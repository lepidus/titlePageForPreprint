<?php

class TradutorPKP implements Tradutor {

    public function traduzir($chave, $locale) {
        return __($chave, $locale);
    }
}