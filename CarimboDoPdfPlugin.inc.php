<?php

import('lib.pkp.classes.plugins.GenericPlugin');

class CarimboDoPdfPlugin extends GenericPlugin {

	public function register($category, $path, $mainContextId = NULL) {
		$success = parent::register($category, $path);
		if ($success && $this->getEnabled()) {
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

	public function carimbarPdf($nomeDoGancho, $args) {
		error_log($nomeDoGancho);		
		
		$passo = $args[0];

		error_log('carimbarPdf no passo ' . $passo);


		if ($passo == 2) {
			$submissão = $args[1];
			$arquivos = $submissão->getGalleys();
			$arquivo = $arquivos[0]->getFile();
			$caminhoDoPdf = $arquivo->getFilePath(); 

			error_log('já posso pegar o arquivo da submissão de tipo: ' . $arquivo->getFilePath());
			
			$path = "plugins/generic/carimbo-do-pdf/pdfcpu/";

			error_log(shell_exec($path .'pdfcpu pages insert -pages 1 -mode before '.$arquivo->getFilePath()));
			
		}

	}
}
