<?php

class Translator
{
    private $context;
    private $submission;
    private $publication;

    public function __construct($context, $publication)
    {
        $this->context = $context;
        $this->publication = $publication;
    }

    public function translate($key, $locale, $params = null)
    {
        AppLocale::requireComponents(LOCALE_COMPONENT_PKP_COMMON, $locale);
        AppLocale::requireComponents(LOCALE_COMPONENT_PKP_SUBMISSION, $locale);
        AppLocale::requireComponents(LOCALE_COMPONENT_APP_SUBMISSION, $locale);

        return __($key, $params, $locale);
    }

    public function getTranslatedChecklist($locale)
    {
        $rawChecklist = $this->context->getData('submissionChecklist')[$locale];
        foreach ($rawChecklist as $checklistItem) {
            $checklist[] = $checklistItem['content'];
        }
        return $checklist;
    }

    public function getTranslatedTitle($locale)
    {
        return $this->publication->getLocalizedTitle($locale);
    }
}
