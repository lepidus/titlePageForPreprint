<?php

import('lib.pkp.classes.plugins.GenericPlugin');
import('plugins.generic.TitlePageForPreprint.sources.Submission');
import('plugins.generic.TitlePageForPreprint.sources.Composition');
import('plugins.generic.TitlePageForPreprint.sources.SubmissionPress');
import('plugins.generic.TitlePageForPreprint.sources.SubmissionPressPKP');
import('plugins.generic.TitlePageForPreprint.sources.Pdf');
import('plugins.generic.TitlePageForPreprint.sources.TitlePage');
import('plugins.generic.TitlePageForPreprint.sources.Translator');
import('plugins.generic.TitlePageForPreprint.sources.TranslatorPKP');
import('plugins.generic.TitlePageForPreprint.sources.SubmissionFileSettingsDAO');
import('lib.pkp.classes.file.SubmissionFileManager');

class TitlePagePlugin extends GenericPlugin {
	const STEPS_TO_INSERT_TITLE_PAGE = 4;
	const LOGO_PATH = "plugins/generic/TitlePageForPreprint/resources/preprint_pilot.png";

	public function register($category, $path, $mainContextId = NULL) {
		$registeredPlugin = parent::register($category, $path);
		
		if ($registeredPlugin && $this->getEnabled()) {
			HookRegistry::register('Publication::publish::before', [$this, 'insertTitlePageWhenPublishing']);
		}
		return $registeredPlugin;
	}

	public function getDisplayName() {
		return 'Title Page For Preprint';
	}

	public function getDescription() {
		return 'Add a Title Page with essential information on preprints.';
	}

	public function insertTitlePageWhenPublishing($hookName, $arguments) {
		$publication = $arguments[0];
		$submission = Services::get('submission')->get($publication->getData('submissionId'));
		$context = Application::getContextDAO()->getById($submission->getContextId());
		$this->addLocaleData("pt_BR");
		$this->addLocaleData("en_US");
		$this->addLocaleData("es_ES");
		$pressData = $this->getDataForPress($submission, $publication);
		$pressData['publicationDate'] = time();	// This method is called only when publishing.
		$press = $this->getSubmissionPress($submission, $publication, $context, $pressData);
		$press->insertTitlePage();
	}

	private function getDataForPress($submission, $publication) {
		$data = array();

		$data['doi'] = $publication->getStoredPubId('doi');
		$data['doiJournal'] = $publication->getData('vorDoi');
		$data['authors'] = $this->getAuthors($publication);
		$data['submissionDate'] = strtotime($submission->getData('dateSubmitted'));

		$status = $publication->getData('relationStatus');
		$relation = array(PUBLICATION_RELATION_NONE => 'publication.relation.none', PUBLICATION_RELATION_SUBMITTED => 'publication.relation.submitted', PUBLICATION_RELATION_PUBLISHED => 'publication.relation.published');
		$data['status'] = ($status) ? ($relation[$status]) : ("");

		return $data;
	}

	public function createNewRevision($composition, $submission) {
		$submissionFile = $composition->getFile();

		$submissionFileManager = new SubmissionFileManager($submission->getContextId(), $submission->getId());
		$copyResult = $submissionFileManager->copyFileToFileStage($composition->getFileId(), $submissionFile->getRevision(), $submissionFile->getFileStage(), $composition->getFileId(), true);
		$submissionFileDao = DAORegistry::getDAO('SubmissionFileDAO');
		return $submissionFileDao->getLatestRevision($submissionFile->getFileId());
	}

	private function getAuthors($publication) {
		$userGroupIds = array_map(function($author) {
			return $author->getData('userGroupId');
		}, $publication->getData('authors'));
		$userGroups = array_map(function($userGroupId) {
			$userGroupDao = DAORegistry::getDAO('UserGroupDAO'); /* @var $userGroupDao UserGroupDAO */
			return $userGroupDao->getbyId($userGroupId);
		}, array_unique($userGroupIds));

		return $publication->getAuthorString($userGroups);
	}

	private function createNewComposition($submission, $composition) {
		$submissionFile = $composition->getFile();
		$submissionFileDao = DAORegistry::getDAO('SubmissionFileDAO');

		$id = $submissionFile->getFileId();
		$revision = $submissionFileDao->getLatestRevision($id);

		$fileSettingsDAO = new SubmissionFileSettingsDAO(); 
		DAORegistry::registerDAO('SubmissionFileSettingsDAO', $fileSettingsDAO);
		
		$setting = $fileSettingsDAO->getSetting($id, 'TitlePage');
		
		if($setting) {
			$revisions = $fileSettingsDAO->getSetting($id, 'revisions');
			$revisions = json_decode($revisions);

			if($revision->getRevision() != end($revisions)) {
				$fileSettingsDAO->updateSetting($id, 'TitlePage', 'no');
			}
		}

		$newRevision = $this->createNewRevision($composition, $submission);
		$revision = $submissionFileDao->getLatestRevision($submissionFile->getFileId());

		return new Composition($newRevision->getFilePath(), $composition->getLocale(), $newRevision->getId(), $revision->getRevision());
	}

	private function getSubmissionPress($submission, $publication, $context, $data) {
		$composition = $publication->getData('galleys');
		
		foreach ($compositions as $composition) {
			$submissionCompositions[] = $this->createNewComposition($submission, $composition);	
		}

		return new SubmissionPressPKP(self::LOGO_PATH, new Submission($data['status'], $data['doi'], $data['doiJournal'], $data['authors'], $data['submissionDate'], $data['publicationDate'], $submissionCompositions), new TranslatorPKP($context, $submission, $publication));
	}
}
