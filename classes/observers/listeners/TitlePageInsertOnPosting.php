<?php

namespace APP\plugins\generic\titlePageForPreprint\classes\observers\listeners;

use Illuminate\Events\Dispatcher;
use PKP\observers\events\PublicationPublished;
use PKP\plugins\PluginRegistry;
use APP\facades\Repo;
use APP\plugins\generic\titlePageForPreprint\classes\TitlePageRequirements;

class TitlePageInsertOnPosting
{
    private const EVENT_SEQUENCE_LAST = -100;

    public function subscribe(Dispatcher $events): void
    {
        $events->listen(
            PublicationPublished::class,
            TitlePageInsertOnPosting::class,
            self::EVENT_SEQUENCE_LAST
        );
    }

    public function handle(PublicationPublished $event): void
    {
        $titlePageRequirements = new TitlePageRequirements();
        $publicationId = $event->publication->getId();
        $publication = Repo::publication()->get($publicationId);

        if ($titlePageRequirements->checkRequirements()) {
            $plugin = PluginRegistry::getPlugin('generic', 'titlepageforpreprintplugin');
            $plugin->insertTitlePageInPreprint($publication);
        }
    }
}
