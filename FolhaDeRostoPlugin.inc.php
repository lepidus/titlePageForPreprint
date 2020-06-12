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
	private $passoParaInserirFolhaDeRosto = 4;
	const CAMINHO_DA_LOGO = "plugins/generic/carimbo-do-pdf/recursos/preprint_pilot.png";

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

	public function criaNovaRevisão($composição, $submissão){
		$arquivoDaSubmissão = $composição->getFile();
		$gerenciadorDeArquivosDeSubmissão = new SubmissionFileManager($submissão->getContextId(), $submissão->getId());
		$resultadoDaCópia = $gerenciadorDeArquivosDeSubmissão->copyFileToFileStage($composição->getFileId(), $arquivoDaSubmissão->getRevision(), $arquivoDaSubmissão->getFileStage(), $composição->getFileId(), true);
		$submissionFileDao = DAORegistry::getDAO('SubmissionFileDAO');
		return $submissionFileDao->getLatestRevision($arquivoDaSubmissão->getFileId());
	}

	private function obterPrensaDeSubmissões($submissão, $formulário) {
		$composições = $submissão->getGalleys();
		$doi = $submissão->getStoredPubId('doi');
		$status = $submissão->getStatusKey();
		$autores = $submissão->getAuthorString();
		$dataDeSubmissão = $submissão->getData('lastModified');
		
		$contexto = $formulário->context;
		
		error_log('Data curta '. $datestr2);

		foreach ($composições as $composição) {
			$novaRevisão = $this->criaNovaRevisão($composição, $submissão);
			$composiçõesDaSubmissão[] = new Composicao($novaRevisão->getFilePath(), $composição->getLocale());
		}
			return new PrensaDeSubmissoes(self::CAMINHO_DA_LOGO, new Submissao($status, $doi, $autores, $dataDeSubmissão, $composiçõesDaSubmissão), new TradutorPKP($contexto, $submissão));
	}
}
