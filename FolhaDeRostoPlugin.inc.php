<?php

import('lib.pkp.classes.plugins.GenericPlugin');
import('plugins.generic.carimbo-do-pdf.fontes.Submissao');
import('plugins.generic.carimbo-do-pdf.fontes.Composicao');
import('plugins.generic.carimbo-do-pdf.fontes.PrensaDeSubmissoes');
import('plugins.generic.carimbo-do-pdf.fontes.Pdf');
import('plugins.generic.carimbo-do-pdf.fontes.FolhaDeRosto');
import('plugins.generic.carimbo-do-pdf.fontes.Tradutor');
import('plugins.generic.carimbo-do-pdf.fontes.TradutorPKP');
import('lib.pkp.classes.file.SubmissionFileManager');

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
		$arquivosDeComposição = $submissão->getGalleys(); //fatorar esses nomes
		$doi = $submissão->getStoredPubId('doi');
		$status = $submissão->getStatusKey();
		$composiçõesDaSubmissão = array(); //fatorar esses nomes
		$contexto = $formulário->context;

		foreach ($arquivosDeComposição as $arquivo) {
			$submissionFile = $arquivo->getFile();
			error_log("mostrando revisão do arquivo submetido antes da cópia: ");
			error_log(print_r($submissionFile->getFileIdAndRevision(), true));
			$submissionFileManager = new SubmissionFileManager($submissão->getContextId(), $submissão->getId());
			$resultadoDaCópia = $submissionFileManager->copyFileToFileStage($arquivo->getFileId(), $submissionFile->getRevision(), $submissionFile->getFileStage(), $arquivo->getFileId(), true);
			$codigoDoNovoArquivo = $resultadoDaCópia[0];
			$submissionFileDao = DAORegistry::getDAO('SubmissionFileDAO');
			$novaRevisão = $submissionFileDao->getLatestRevision($submissionFile->getFileId());
			error_log("mostrando revisão do arquivo submetido depois da cópia: ");
			error_log(print_r($novaRevisão->getFileIdAndRevision(), true));
			$composiçõesDaSubmissão[] = new Composicao($novaRevisão->getFilePath(), $arquivo->getLocale());
		}
			$logo = "plugins/generic/carimbo-do-pdf/recursos/preprint_pilot.png";
			return new PrensaDeSubmissoes($logo, new Submissao($status, $doi, $composiçõesDaSubmissão), new TradutorPKP($contexto));
		}
}
