<?php
/**
 * @file plugins/generic/TitlePageForPreprint/TitlePagePlugin.inc.php
 * 
 * Copyright (c) 2020-2021 Lepidus Tecnologia
 * Copyright (c) 2020-2021 SciELO
 * Distributed under the GNU GPL v3. For full terms see LICENSE or https://www.gnu.org/licenses/gpl-3.0.txt
 *
 * @class TitlePagePlugin
 * @ingroup plugins_generic_TitlePageForPreprint
 *
 * @brief Plugin class for the TitlePageForPreprint plugin.
 */
import('lib.pkp.classes.plugins.GenericPlugin');
import('plugins.generic.titlePageForPreprint.classes.SubmissionModel');
import('plugins.generic.titlePageForPreprint.classes.GalleyAdapter');
import('plugins.generic.titlePageForPreprint.classes.SubmissionPress');
import('plugins.generic.titlePageForPreprint.classes.SubmissionPressPKP');
import('plugins.generic.titlePageForPreprint.classes.Pdf');
import('plugins.generic.titlePageForPreprint.classes.TitlePage');
import('plugins.generic.titlePageForPreprint.classes.Translator');
import('plugins.generic.titlePageForPreprint.classes.TranslatorPKP');
import('plugins.generic.titlePageForPreprint.classes.TitlePageDAO');

class TitlePagePlugin extends GenericPlugin {
	const STEPS_TO_INSERT_TITLE_PAGE = 4;

	public function register($category, $path, $mainContextId = NULL) {
		$registeredPlugin = parent::register($category, $path);
		
		if ($registeredPlugin && $this->getEnabled()) {
			
			HookRegistry::register('Publication::publish::before', [$this, 'insertTitlePageWhenPublishing']);
			HookRegistry::register('Publication::edit', [$this, 'insertTitlePageWhenChangeRelation']);
			HookRegistry::register('Schema::get::submissionFile', array($this, 'modifySubmissionFileSchema'));
		}
		return $registeredPlugin;
	}

	public function getDisplayName() {
		return __('plugins.generic.titlePageForPreprint.displayName');
	}

	public function getDescription() {
		return __('plugins.generic.titlePageForPreprint.description');
	}

	public function modifySubmissionFileSchema($hookName, $params) {
		$schema =& $params[0];

        $schema->properties->{'folhaDeRosto'} = (object) [
            'type' => 'string',
            'apiSummary' => true,
            'validation' => ['nullable'],
        ];
		$schema->properties->{'revisoes'} = (object) [
            'type' => 'string',
            'apiSummary' => true,
            'validation' => ['nullable'],
        ];

        return false;
	}

	public function getLogoPath($context) {
		$publicFileManager = new PublicFileManager();
		$filesPath = $publicFileManager->getContextFilesPath($context->getId());
		$logoFilePath = $context->getLocalizedPageHeaderLogo()['uploadName'];

		return $filesPath . DIRECTORY_SEPARATOR . $logoFilePath;
	}

	public function insertTitlePageWhenPublishing($hookName, $arguments) {
		$publication = $arguments[0];
		$this->insertTitlePageInPreprint($publication);
	}

	public function insertTitlePageWhenChangeRelation($hookName, $arguments){
		$params = $arguments[2];
		$publication = $arguments[0];
		if (array_key_exists('relationStatus',$params) && $publication->getData('datePublished')){
			$this->insertTitlePageInPreprint($publication);
		}
	}

	public function insertTitlePageInPreprint($publication){
		$submission = Services::get('submission')->get($publication->getData('submissionId'));
		$context = Application::getContextDAO()->getById($submission->getContextId());
		$this->addLocaleData("pt_BR");
		$this->addLocaleData("en_US");
		$this->addLocaleData("es_ES");
		$pressData = $this->getDataForPress($submission, $publication);
		$press = $this->getSubmissionPress($submission, $publication, $context, $pressData);
		$press->insertTitlePage();
	}


	private function getDataForPress($submission, $publication) {
		$data = array();

		$data['doi'] = $publication->getStoredPubId('doi');
		$data['doiJournal'] = $publication->getData('vorDoi');
		$data['authors'] = $this->getAuthors($publication);
		$data['version'] = $publication->getData('version');

		$dateSubmitted = strtotime($submission->getData('dateSubmitted'));
		$data['submissionDate'] = date('Y-m-d', $dateSubmitted);
		$datePublished = strtotime($publication->getData('datePublished'));
		$data['publicationDate'] = date('Y-m-d', $datePublished);

		$status = $publication->getData('relationStatus');
		$relation = array(PUBLICATION_RELATION_NONE => 'publication.relation.none', PUBLICATION_RELATION_SUBMITTED => 'publication.relation.submitted', PUBLICATION_RELATION_PUBLISHED => 'publication.relation.published');
		$data['status'] = ($status) ? ($relation[$status]) : ("");

		return $data;
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

	public function getLatestRevision($submissionFileId) {
		$submissionFileDao = DAORegistry::getDAO('SubmissionFileDAO');
		$revisions = $submissionFileDao->getRevisions($submissionFileId)->toArray();
		$lastRevision = get_object_vars($revisions[0]);

		foreach($revisions as $revision){
			$revision = get_object_vars($revision);
			if($revision['fileId'] > $lastRevision['fileId'])
				$lastRevision = $revision;
		}
		
		return [$lastRevision['fileId'], $lastRevision['path']];
	}

	public function submissionFileHasNewRevisionWithoutTitlePage($submissionFile, $lastRevisionId): bool {
		if(!empty($submissionFile->getData('folhaDeRosto'))){
			$revisionIds = $submissionFile->getData('revisoes');
			$revisionIds = json_decode($revisionIds);

			if($lastRevisionId != end($revisionIds)) {
				$titlePageDao = new TitlePageDAO();
				$numberOfRevisions = $titlePageDao->getNumberOfRevisions($submissionFile->getId());

				if($numberOfRevisions != end($revisionIds)) { //Check for legacy cases
					return true;
				}
			}
		}

		return false;
	}

	private function createGalleyAdapter($submission, $galley) {
		$submissionFile = $galley->getFile();
		list($lastRevisionId, $lastRevisionPath) = $this->getLatestRevision($submissionFile->getId());
		
		if($this->submissionFileHasNewRevisionWithoutTitlePage($submissionFile, $lastRevisionId)) {
			Services::get('submissionFile')->edit($submissionFile, [
				'folhaDeRosto' => 'nao',
			], Application::get()->getRequest());
		}

		return new GalleyAdapter($lastRevisionPath, $galley->getLocale(), $submissionFile->getId(), $lastRevisionId);
	}

	private function getSubmissionPress($submission, $publication, $context, $data) {
		$logoPath = $this->getLogoPath($context);
		$galleys = $publication->getData('galleys');
		
		foreach ($galleys as $galley) {
			$submissionGalleys[] = $this->createGalleyAdapter($submission, $galley);	
		}

		return new SubmissionPressPKP(
			$logoPath,
			new SubmissionModel($data['status'], $data['doi'], $data['doiJournal'], $data['authors'], $data['submissionDate'], $data['publicationDate'], $data['version'], $submissionGalleys),
			new TranslatorPKP($context, $submission, $publication)
		);
	}
}
