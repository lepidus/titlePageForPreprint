<?php
import('lib.pkp.tests.DatabaseTestCase');
import('classes.submission.Submission');
import('lib.pkp.classes.file.PKPFile');
import('lib.pkp.classes.submission.SubmissionFile');
import('plugins.generic.titlePageForPreprint.tests.TitlePageTestsDAO');
import('plugins.generic.titlePageForPreprint.classes.GalleyAdapterFactory');
import('lib.pkp.classes.services.PKPSchemaService');

class TitlePageOnDatabaseTest extends DatabaseTestCase {
    
    private $path = "files/document.pdf";
    private $fileId = 1;

    protected function getMockedDAOs() {
		return array('SubmissionFileDAO');
	}

    private function registerMockSubmissionFileDAO() {
        $submissionFileDAO = $this->getMockBuilder(SubmissionFileDAO::class)
			->setMethods(array('getById'))
			->getMock();

		$submissionFile = new SubmissionFile();

		$submissionFileDAO->expects($this->any())
                          ->method('getById')
                          ->will($this->returnValue($submissionFile));

		DAORegistry::registerDAO('SubmissionFileDAO', $submissionFileDAO);

        return $submissionFileDAO;
    }

    private function checkIfLastRevisionHasTitlePage() {
        $submissionFile = $this->registerMockSubmissionFileDAO();;

        $galleyAdapterFactory = $this->createMock(GalleyAdapterFactory::class);
        list($lastRevisionId, $lastRevisionPath) = $galleyAdapterFactory->getLatestRevision($this->fileId);

        $lastRevisionHasTitlePage = !$galleyAdapterFactory->submissionFileHasNewRevisionWithoutTitlePage($submissionFile, $lastRevisionId);
        return $lastRevisionHasTitlePage;
    }
    
    public function testCanDetectSubmissionFileHasTitlePage(): void {
        $this->assertTrue($this->checkIfLastRevisionHasTitlePage());
    }

    public function testCanDetectLegacySubmissionFileHasTitlePage(): void {
        $submissionFile = $this->registerMockSubmissionFileDAO();
        $titlePageTestsDao = $this->createMock(TitlePageTestsDAO::class);
        $numberOfRevisions = 1;
        $newRevisoes = json_encode([$numberOfRevisions]);
        $titlePageTestsDao->updateRevisionsWithTitlePageSettingFromSubmissionFile($submissionFile, $newRevisoes);

        $this->assertTrue($this->checkIfLastRevisionHasTitlePage());
    }
}