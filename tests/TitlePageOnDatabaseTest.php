<?php
import('lib.pkp.tests.DatabaseTestCase');
import('classes.submission.Submission');
import('lib.pkp.classes.file.PKPFile');
import('lib.pkp.classes.submission.SubmissionFile');
import('plugins.generic.titlePageForPreprint.tests.TitlePageTestsDAO');
import('plugins.generic.titlePageForPreprint.TitlePagePlugin');
import('lib.pkp.classes.services.PKPSchemaService');

class TitlePageOnDatabaseTest extends DatabaseTestCase {
    
    private $submissionFile;
    private $submissionId;
    private $fileId;
    private $contextId = 1;
    private $fileStage = SUBMISSION_FILE_PROOF;
    private $createdAt = '2021-06-14 16:23:00';
    private $updatedAt = '2021-06-14 16:24:00';
    private $tablesToRestore = array("submission_file_revisions", "submission_files", "submission_file_settings", "files", "submissions", "submission_settings");

    public function setUp() : void {
        parent::setUp();
        PKPTestHelper::backupTables($this->tablesToRestore, $this);
        $this->submissionId = $this->createSubmission();
        $this->fileId = $this->createFile();
        $this->submissionFile = $this->createSubmissionFile();
        $this->addTitlePageDataToSubmissionFile();
    }

    public function tearDown(): void {
        parent::tearDown();
        $titlePageTestsDao = new TitlePageTestsDAO();
        $titlePageTestsDao->restoreTables($this->tablesToRestore);
    }

    private function createSubmission(): int {
        $submissionDao = DAORegistry::getDAO('SubmissionDAO');
        $submission = new Submission();
        $submission->setData('contextId', $this->contextId);

        return $submissionDao->insertObject($submission);
    }

    private function createFile(): int {
        $titlePageTestsDao = new TitlePageTestsDAO();

        $path = "files/document.pdf";
        $mimetype = "application/pdf";

        return $titlePageTestsDao->insertTestFile($path, $mimetype);
    }

    private function createSubmissionFile() {
        $submissionFile = new SubmissionFile();
        $submissionFile->setData('submissionId', $this->submissionId);
        $submissionFile->setData('fileId', $this->fileId);
        $submissionFile->setData('fileStage', $this->fileStage);
        $submissionFile->setData('createdAt', $this->createdAt);
        $submissionFile->setData('updatedAt', $this->updatedAt);

        $submissionFileDao = DAORegistry::getDAO('SubmissionFileDAO');
        $submissionFileId = $submissionFileDao->insertObject($submissionFile);

        return $submissionFileDao->getById($submissionFileId);
    }

    private function addTitlePageDataToSubmissionFile() {
        $titlePageTestsDao = new TitlePageTestsDAO();

        $titlePageTestsDao->addFolhaDeRostoSettingToSubmissionFile($this->submissionFile, 'sim');
        
        $revisoes = json_encode([$this->fileId]);
        $titlePageTestsDao->addRevisoesSettingToSubmissionFile($this->submissionFile, $revisoes);
    }

    private function checkIfLastRevisionHasTitlePage() {
        $titlePagePlugin = new TitlePagePlugin();
        list($lastRevisionId, $lastRevisionPath) = $titlePagePlugin->getLatestRevision($this->submissionFile->getId());

        $lastRevisionHasTitlePage = !$titlePagePlugin->submissionFileHasNewRevisionWithoutTitlePage($this->submissionFile, $lastRevisionId);
        return $lastRevisionHasTitlePage;
    }
    
    public function testCanDetectSubmissionFileHasTitlePage(): void {
        $this->assertTrue($this->checkIfLastRevisionHasTitlePage());
    }

    public function testCanDetectLegacySubmissionFileHasTitlePage(): void {
        $titlePageTestsDao = new TitlePageTestsDAO();
        $numberOfRevisions = 1;
        $newRevisoes = json_encode([$numberOfRevisions]);
        $titlePageTestsDao->updateRevisoesFromSubmissionFile($this->submissionFile, $newRevisoes);

        $this->assertTrue($this->checkIfLastRevisionHasTitlePage());
    }
}