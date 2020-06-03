<?php

import('lib.pkp.classes.plugins.GenericPlugin');


class FolhaDeRostoPlugin extends GenericPlugin {

	public function register($category, $path, $mainContextId = NULL) {
		$success = parent::register($category, $path);
		if ($success && $this->getEnabled()) {
			// Display the publication statement on the article details page
			HookRegistry::register('SubmissionHandler::saveSubmit', [$this, 'inserirFolhaDeRosto']);
		}
		return $success;
	}

	public function getDisplayName() {
		return 'FolhaDeRostoPlugin';
	}

	public function getDescription() {
		return 'FolhaDeRostoPlugin';
	}

	public function inserirFolhaDeRosto($nomeDoGancho, $args) {
		error_log($nomeDoGancho);
		
		$passo = $args[0];

		error_log('inserida folha de rosto no passo ' . $passo);

		$submissão = $args[1];

		if ($passo == 2) {
			$arquivos = $submissão->getGalleys();
			$doi = $submissão->getStoredPubId('doi');
			$status = $submissão->getStatusKey();
			
			foreach ($arquivos as $arquivo) {
				$documento = $arquivo->getFile();
				$caminhoDoPdf = $documento->getFilePath(); 
				$locale = $arquivo->getLocale();
			}
			$checklist = $submissão->getLocalizedData('submissionChecklist'); //se conseguir pegar o context dá pra usar esse método
			error_log('Doi '. $doi);
			error_log('Status '. $status);
			error_log('Locale '. $locale);
			error_log('Checklist'. $checklist[0]);			
		}
		if($passo == 3){
			$arquivos = $submissão->getGalleys();
			$doi = $submissão->getStoredPubId('doi');
			$status = $submissão->getStatusKey();
			
			foreach ($arquivos as $arquivo) {
				$documento = $arquivo->getFile();
				$caminhoDoPdf = $documento->getFilePath(); 
				$locale = $arquivo->getLocale();
			}
			$checklist = $submissão->getLocalizedData('submissionChecklist');
			error_log('Doi '. $doi);
			error_log('Status '. $status);
			error_log('Locale '. $locale);
			error_log('Checklist'. $checklist);


		}

	}
}