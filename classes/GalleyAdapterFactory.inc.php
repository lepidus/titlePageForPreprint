<?php
import('plugins.generic.titlePageForPreprint.classes.GalleyAdapter');
import('plugins.generic.titlePageForPreprint.classes.TitlePageDAO');

class GalleyAdapterFactory {
		private $submissionFileDao;

		public function __construct($submissionFileDao) {
			$this->submissionFileDao = $submissionFileDao;
		}
    
    public function createGalleyAdapter($submission, $galley): GalleyAdapter {
		$submissionFile = $galley->getFile();
		list($lastRevisionId, $lastRevisionPath) = $this->getLatestRevision($submissionFile->getId());
		
		if($this->submissionFileHasNewRevisionWithoutTitlePage($submissionFile, $lastRevisionId)) {
			Services::get('submissionFile')->edit($submissionFile, [
				'folhaDeRosto' => 'nao',
			], Application::get()->getRequest());
		}

		return new GalleyAdapter($lastRevisionPath, $galley->getLocale(), $submissionFile->getId(), $lastRevisionId);
	}

  public function getLatestRevision($submissionFileId) {
		$revisions = $this->submissionFileDao->getRevisions($submissionFileId)->toArray();
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

				if($numberOfRevisions != end($revisionIds)) {
					return true;
				}
			}
		}

		return false;
	}
}