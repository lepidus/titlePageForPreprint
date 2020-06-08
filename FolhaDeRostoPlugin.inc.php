<?php

import('lib.pkp.classes.plugins.GenericPlugin');
import('plugins.generic.carimbo-do-pdf.fontes.Submissao');
import('plugins.generic.carimbo-do-pdf.fontes.Composicao');
import('plugins.generic.carimbo-do-pdf.fontes.PrensaDeSubmissoes');
import('plugins.generic.carimbo-do-pdf.fontes.Pdf');
import('plugins.generic.carimbo-do-pdf.fontes.FolhaDeRosto');
import('plugins.generic.carimbo-do-pdf.fontes.Tradutor');
import('plugins.generic.carimbo-do-pdf.fontes.TradutorPKP');

class FolhaDeRostoPlugin extends GenericPlugin {
	private $passoParaInserirFolhaDeRosto = 2;

	public function register($category, $path, $mainContextId = NULL) {
		$success = parent::register($category, $path);
		if ($success && $this->getEnabled()) {
			HookRegistry::register('SubmissionHandler::saveSubmit', [$this, 'inserirFolhaDeRostoQuandoNecessario']);
		}
		return $success;
	}

	public function getDisplayName() {
		return 'FolhaDeRostoPlugin';
	}

	public function getDescription() {
		return 'FolhaDeRostoPlugin';
	}

	public function inserirFolhaDeRostoQuandoNecessario($nomeDoGancho, $args) {
		$passo = $args[0];
		
		if ($passo == $this->passoParaInserirFolhaDeRosto) {
			$prensa = $this->obterPrensaDeSubmissões($args[1],  $args[2]);
			$prensa->inserirFolhasDeRosto();
		}
	}

	private function obterPrensaDeSubmissões($submissão, $formulário) {
		$arquivosDeComposição = $submissão->getGalleys();
		$doi = $submissão->getStoredPubId('doi');
		$status = __($submissão->getStatusKey());
		$composiçõesDaSubmissão = array();
		$checklistBruta = $formulário->context->getLocalizedData('submissionChecklist');
		
		foreach ($arquivosDeComposição as $arquivo) {
			$composiçõesDaSubmissão[] = new Composicao($arquivo->getFile()->getFilePath(), $arquivo->getLocale());
		}

		foreach ($checklistBruta as $itemDaChecklist) {
			$checklist[] = $itemDaChecklist['content'];
		}
		
		$logo = "plugins/generic/carimbo-do-pdf/recursos/preprint_pilot.png";
		 
		return new PrensaDeSubmissoes($logo, $checklist, new Submissao($status, $doi, $composiçõesDaSubmissão), new TradutorPKP());
	}
}
