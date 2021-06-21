<?php

interface Translator {

    public function translate($key, $locale, $params = null);
    public function getTranslatedChecklist($locale);
    public function getTranslatedTitle($locale);
}