<?php

import('lib.pkp.classes.plugins.GenericPlugin');

const CAMINHO_LOGO = "/plugins/themes/scielo-theme/styles/img/preprint_pilot.png";

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
		$formulário = $args[2];

		if ($passo == 2) {
			$arquivos = $submissão->getGalleys();
			$doi = $submissão->getStoredPubId('doi');
			$status = $submissão->getStatusKey();
			$logo = CAMINHO_LOGO;
			$checklist = $formulário->context->getLocalizedData('submissionChecklist');
			
			foreach ($arquivos as $arquivo) {
				$documento = $arquivo->getFile();
				$caminhoDoPdf = $documento->getFilePath(); 
			}

			error_log('Doi '. $doi);
			error_log('Status '. $status);
			error_log('Locale '. $locale);
			error_log('Checklist'. print_r($checklist, true));			
		}
	}
}

/* Para pegar o passado pelo plugin de logo
	Com isso precisa do context, os comandos abaixo foram encontrados em TemplateManager.inc.php
	'publicFilesDir' => $request->getBaseUrl() . '/' . $publicFileManager->getContextFilesPath($context->getId()),
 	'displayPageHeaderLogo' => $context->getLocalizedPageHeaderLogo()
	creio que dê pra pegar com o getLocalizedData(), mas ainda não existe outra logo
 */
