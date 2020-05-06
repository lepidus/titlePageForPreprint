<?php

import('lib.pkp.classes.plugins.GenericPlugin');
class CarimboDoPdfPlugin extends GenericPlugin {

	public function register($category, $path, $mainContextId = NULL) {
		$success = parent::register($category, $path);
		if ($success && $this->getEnabled()) {
			error_log('plugin registrado');		
			// Display the publication statement on the article details page
			HookRegistry::register('SubmissionHandler::saveSubmit', [$this, 'carimbarPdf']);
		}
		return $success;
	}

	public function getDisplayName() {
		return 'CarimboDoPdfPlugin';
	}

	public function getDescription() {
		return 'CarimboDoPdfPlugin';
	}

	public function carimbarPdf($hookName, $args) {
		error_log($hookName);		
		error_log('carimbarPdf no passo ' . $args[0]);
	}
}
