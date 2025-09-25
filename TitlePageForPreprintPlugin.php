<?php

/**
 * @file plugins/generic/TitlePageForPreprint/TitlePageForPreprintPlugin.inc.php
 *
 * Copyright (c) 2020-2024 Lepidus Tecnologia
 * Copyright (c) 2020-2024 SciELO
 * Distributed under the GNU GPL v3. For full terms see LICENSE or https://www.gnu.org/licenses/gpl-3.0.txt
 *
 * @class TitlePageForPreprintPlugin
 * @ingroup plugins_generic_TitlePageForPreprint
 *
 * @brief Plugin class for the TitlePageForPreprint plugin.
 */

namespace APP\plugins\generic\titlePageForPreprint;

use PKP\plugins\GenericPlugin;
use PKP\plugins\Hook;
use APP\core\Application;
use APP\facades\Repo;
use APP\submission\Submission;
use Illuminate\Support\Facades\Event;
use APP\plugins\generic\titlePageForPreprint\classes\SubmissionPressFactory;
use APP\plugins\generic\titlePageForPreprint\classes\SubmissionFileUpdater;
use APP\plugins\generic\titlePageForPreprint\classes\TitlePageRequirements;
use APP\plugins\generic\titlePageForPreprint\classes\observers\listeners\TitlePageInsertOnPosting;

class TitlePageForPreprintPlugin extends GenericPlugin
{
    public function register($category, $path, $mainContextId = null)
    {
        $success = parent::register($category, $path, $mainContextId);

        if (Application::isUnderMaintenance()) {
            return $success;
        }

        if ($success && $this->getEnabled($mainContextId)) {
            Event::subscribe(new TitlePageInsertOnPosting());

            Hook::add('Publication::edit', [$this, 'insertTitlePageWhenChangeRelation']);
            Hook::add('Schema::get::submissionFile', [$this, 'modifySubmissionFileSchema']);
        }

        return $success;
    }

    public function setEnabled($enabled)
    {
        if ($enabled) {
            $titlePageRequirements = new TitlePageRequirements();
            $titlePageRequirements->checkRequirements();
        }
        parent::setEnabled($enabled);
    }

    public function getDisplayName()
    {
        return __('plugins.generic.titlePageForPreprint.displayName');
    }

    public function getDescription()
    {
        return __('plugins.generic.titlePageForPreprint.description');
    }

    public function modifySubmissionFileSchema($hookName, $params)
    {
        $schema = & $params[0];

        $schema->properties->{'folhaDeRosto'} = (object) [
            'type' => 'string',
            'apiSummary' => true,
            'validation' => ['nullable'],
        ];
        $schema->properties->{'revisoes'} = (object) [
            'type' => 'string',
            'apiSummary' => true,
            'validation' => ['nullable'],
        ];

        return false;
    }

    public function insertTitlePageWhenChangeRelation($hookName, $arguments)
    {
        $titlePageRequirements = new TitlePageRequirements();

        if ($titlePageRequirements->checkRequirements()) {
            $params = $arguments[2];
            $publication = $arguments[0];

            if (array_key_exists('relationStatus', $params) && ($publication->getData('status') == Submission::STATUS_PUBLISHED)) {
                $this->insertTitlePageInPreprint($publication);
            }
        }
    }

    public function insertTitlePageInPreprint($publication)
    {
        $submission = Repo::submission()->get($publication->getData('submissionId'));
        $context = Application::getContextDAO()->getById($submission->getContextId());
        $this->addLocaleData("pt_BR");
        $this->addLocaleData("en");
        $this->addLocaleData("es");
        $submissionPressFactory = new SubmissionPressFactory();
        $submissionFileUpdater = new SubmissionFileUpdater();
        $press = $submissionPressFactory->createSubmissionPress($submission, $publication, $context);
        $press->insertTitlePage($submissionFileUpdater);
    }
}
