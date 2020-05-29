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


		if ($passo == 2) {
			$submissão = $args[1];
			$arquivos = $submissão->getGalleys();
			
			foreach ($arquivos as $arquivo) {
				$documento = $arquivo->getFile();
				$caminhoDoPdf = $documento->getFilePath(); 
				
				$pdf = new PDF($caminhoDoPdf);
				//inserção
				//carimbação
				
			}

		}

	}
}