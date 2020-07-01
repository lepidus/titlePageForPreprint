<?php

import('lib.pkp.classes.plugins.GenericPlugin');
import('plugins.generic.folhaDeRostoDoPDF.fontes.Submissao');
import('plugins.generic.folhaDeRostoDoPDF.fontes.Composicao');
import('plugins.generic.folhaDeRostoDoPDF.fontes.PrensaDeSubmissoes');
import('plugins.generic.folhaDeRostoDoPDF.fontes.Pdf');
import('plugins.generic.folhaDeRostoDoPDF.fontes.FolhaDeRosto');
import('plugins.generic.folhaDeRostoDoPDF.fontes.Tradutor');
import('plugins.generic.folhaDeRostoDoPDF.fontes.TradutorPKP');
import('lib.pkp.classes.file.SubmissionFileManager');

class FolhaDeRostoPlugin extends GenericPlugin {
	const PASSO_PARA_INSERIR_FOLHA_DE_ROSTO = 4;
	const CAMINHO_DA_LOGO = "plugins/generic/folhaDeRostoDoPDF/recursos/preprint_pilot.png";

	public function register($category, $path, $mainContextId = NULL) {
		$pluginRegistrado = parent::register($category, $path);
		
		if ($pluginRegistrado && $this->getEnabled()) {
			HookRegistry::register('Publication::publish::before', [$this, 'inserirFolhaDeRostoQuandoPublicar']);
		}
		return $pluginRegistrado;
	}

	public function getDisplayName() {
		return 'FolhaDeRostoDoPDF';
	}
	


	public function getDescription() {
		return 'FolhaDeRostoDoPDF';
	}

	public function inserirFolhaDeRostoQuandoPublicar($nomeDoGancho, $argumentos){
		$publicação = $argumentos[0];
		$submissão = Services::get('submission')->get($publicação->getData('submissionId'));
		$contextDao = Application::getContextDAO();
		$contexto = $contextDao->getById($submissão->getContextId());

		$this->addLocaleData("pt_BR");
		$this->addLocaleData("en_US");
		$this->addLocaleData("es_ES");
		$prensa = $this->obterPrensaDeSubmissões($submissão,  $contexto);
		$prensa->inserirFolhasDeRosto();
	}

	public function criaNovaRevisão($composição, $submissão){
		$arquivoDaSubmissão = $composição->getFile();
		$gerenciadorDeArquivosDeSubmissão = new SubmissionFileManager($submissão->getContextId(), $submissão->getId());
		$resultadoDaCópia = $gerenciadorDeArquivosDeSubmissão->copyFileToFileStage($composição->getFileId(), $arquivoDaSubmissão->getRevision(), $arquivoDaSubmissão->getFileStage(), $composição->getFileId(), true);
		$submissionFileDao = DAORegistry::getDAO('SubmissionFileDAO');
		return $submissionFileDao->getLatestRevision($arquivoDaSubmissão->getFileId());
	}

	private function obterPrensaDeSubmissões($submissão, $contexto) {
		$composições = $submissão->getGalleys();
		$doi = $submissão->getStoredPubId('doi');
		$status = $submissão->getStatusKey();
		$autores = $submissão->getAuthorString();
		$dataDeSubmissão = strtotime($submissão->getData('lastModified'));


		foreach ($composições as $composição) {
			$novaRevisão = $this->criaNovaRevisão($composição, $submissão);
			$composiçõesDaSubmissão[] = new Composicao($novaRevisão->getFilePath(), $composição->getLocale());
		}
			return new PrensaDeSubmissoes(self::CAMINHO_DA_LOGO, new Submissao($status, $doi, $autores, $dataDeSubmissão, $composiçõesDaSubmissão), new TradutorPKP($contexto, $submissão));
	}
}
