<?php
/**
 * @file plugins/generic/TitlePageForPreprint/TitlePagePlugin.inc.php
 *
 * Copyright (c) 2020-2021 Lepidus Tecnologia
 * Copyright (c) 2020-2021 SciELO
 * Distributed under the GNU GPL v3. For full terms see LICENSE or https://www.gnu.org/licenses/gpl-3.0.txt
 *
 * @class TitlePagePlugin
 * @ingroup plugins_generic_TitlePageForPreprint
 *
 * @brief Plugin class for the TitlePageForPreprint plugin.
 */
import('lib.pkp.classes.plugins.GenericPlugin');
import('plugins.generic.titlePageForPreprint.classes.SubmissionPressFactory');
import('plugins.generic.titlePageForPreprint.classes.SubmissionFileUpdater');
import('plugins.generic.titlePageForPreprint.classes.TitlePageRequirements');

class TitlePagePlugin extends GenericPlugin
{
    public function register($category, $path, $mainContextId = null)
    {
        $registeredPlugin = parent::register($category, $path);

        if ($registeredPlugin && $this->getEnabled()) {
            HookRegistry::register('Publication::publish::before', [$this, 'insertTitlePageWhenPublishing']);
            HookRegistry::register('Publication::edit', [$this, 'insertTitlePageWhenChangeRelation']);
            HookRegistry::register('Schema::get::submissionFile', array($this, 'modifySubmissionFileSchema'));
        }
        return $registeredPlugin;
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

    public function insertTitlePageWhenPublishing($hookName, $arguments)
    {
        $titlePageRequirements = new TitlePageRequirements();

        if ($titlePageRequirements->checkRequirements()) {
            $publication = $arguments[0];
            $this->insertTitlePageInPreprint($publication);
        }
    }

    public function insertTitlePageWhenChangeRelation($hookName, $arguments)
    {
        $titlePageRequirements = new TitlePageRequirements();

        if ($titlePageRequirements->checkRequirements()) {
            $params = $arguments[2];
            $publication = $arguments[0];

            if (array_key_exists('relationStatus', $params) && ($publication->getData('status') == STATUS_PUBLISHED)) {
                $this->insertTitlePageInPreprint($publication);
            }
        }
    }

    public function insertTitlePageInPreprint($publication)
    {
        $submission = Services::get('submission')->get($publication->getData('submissionId'));
        $context = Application::getContextDAO()->getById($submission->getContextId());
        $this->addLocaleData("pt_BR");
        $this->addLocaleData("en_US");
        $this->addLocaleData("es_ES");
        $submissionPressFactory = new SubmissionPressFactory();
        $submissionFileUpdater = new SubmissionFileUpdater();
        $press = $submissionPressFactory->createSubmissionPress($submission, $publication, $context);
        $press->insertTitlePage($submissionFileUpdater);
    }
}
