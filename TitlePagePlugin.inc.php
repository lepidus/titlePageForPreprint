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

class TitlePagePlugin extends GenericPlugin {
	const CPDF_PATH = __DIR__ . "/tools/cpdf";

	public function register($category, $path, $mainContextId = NULL) {
		$registeredPlugin = parent::register($category, $path);
		
		if ($registeredPlugin && $this->getEnabled()) {
			
			HookRegistry::register('Publication::publish::before', [$this, 'insertTitlePageWhenPublishing']);
			HookRegistry::register('Publication::edit', [$this, 'insertTitlePageWhenChangeRelation']);
			HookRegistry::register('Schema::get::submissionFile', array($this, 'modifySubmissionFileSchema'));

			$this->ensureCpdfExecutable();
		}
		return $registeredPlugin;
	}

	private function ensureCpdfExecutable() {
		if(!is_executable(self::CPDF_PATH)) {
			chmod(self::CPDF_PATH, 0111);
		}
	}

	public function getDisplayName() {
		return __('plugins.generic.titlePageForPreprint.displayName');
	}

	public function getDescription() {
		return __('plugins.generic.titlePageForPreprint.description');
	}

	public function modifySubmissionFileSchema($hookName, $params) {
		$schema =& $params[0];

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

	public function insertTitlePageWhenPublishing($hookName, $arguments) {
		$publication = $arguments[0];
		$this->insertTitlePageInPreprint($publication);
	}

	public function insertTitlePageWhenChangeRelation($hookName, $arguments){
		$params = $arguments[2];
		$publication = $arguments[0];
	
        if (array_key_exists('relationStatus',$params) && ($publication->getData('status') == STATUS_PUBLISHED)){
			$this->insertTitlePageInPreprint($publication);
		}
	}

	public function insertTitlePageInPreprint($publication){
		$submission = Services::get('submission')->get($publication->getData('submissionId'));
		$context = Application::getContextDAO()->getById($submission->getContextId());
		$this->addLocaleData("pt_BR");
		$this->addLocaleData("en_US");
		$this->addLocaleData("es_ES");
		$submissionPressFactory = new SubmissionPressFactory();
		$press = $submissionPressFactory->createSubmissionPress($submission, $publication, $context);
		$press->insertTitlePage();
	}


}
