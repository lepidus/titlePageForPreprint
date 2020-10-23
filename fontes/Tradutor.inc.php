<?php

interface Tradutor {

    public function traduzir($chave, $locale, $params = null);
    public function obterCheckListTraduzida($locale);
    public function obterTítuloTraduzido($locale);
}