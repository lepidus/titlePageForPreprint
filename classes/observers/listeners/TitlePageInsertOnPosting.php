<?php

namespace APP\plugins\generic\titlePageForPreprint\classes\observers\listeners;

use Illuminate\Events\Dispatcher;
use PKP\observers\events\PublicationPublished;

class TitlePageInsertOnPosting
{
    public function subscribe(Dispatcher $events): void
    {
        $events->listen(
            PublicationPublished::class,
            TitlePageInsertOnPosting::class
        );
    }

    public function handle(PublicationPublished $event): void
    {
        // Insert title page
    }
}
