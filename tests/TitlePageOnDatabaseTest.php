<?php

use APP\facades\Repo;
use PKP\tests\DatabaseTestCase;
use APP\submission\Submission;
use PKP\submissionFile\SubmissionFile;
use PKP\submissionFile\DAO as SubmissionFileDAO;
use APP\core\Request;
use PKP\services\PKPSchemaService;
use APP\plugins\generic\titlePageForPreprint\tests\TitlePageTestsDAO;
use APP\plugins\generic\titlePageForPreprint\classes\GalleyAdapterFactory;

class TitlePageOnDatabaseTest extends DatabaseTestCase
{
    private function getSubmissionFileRepoMock()
    {
        $schemaService = new PKPSchemaService();
        $submissionFileDAO = new SubmissionFileDAO($schemaService);
        $request = new Request();

        $submissionFileRepo = $this->getMockBuilder(Repo::submissionFile()::class)
            ->setConstructorArgs([$submissionFileDAO, $request, $schemaService])
            ->onlyMethods(['getRevisions'])
            ->getMock();

        $obj = new stdClass();
        $obj->fileId = 115;
        $obj->path = "files/document.pdf";

        $submissionFileRepo->expects($this->any())
            ->method('getRevisions')
            ->will($this->returnValue(new \Illuminate\Support\Collection([$obj])));

        return $submissionFileRepo;
    }

    private function checkIfLastRevisionHasTitlePage()
    {
        $mockSubmissionFileRepo = $this->getSubmissionFileRepoMock();
        $submissionFile = new SubmissionFile();
        $submissionFile->setId(360);

        $galleyAdapterFactory = new GalleyAdapterFactory($mockSubmissionFileRepo);
        list($lastRevisionId) = $galleyAdapterFactory->getLatestRevision($submissionFile->getId());
        $lastRevisionHasTitlePage = !$galleyAdapterFactory->submissionFileHasNewRevisionWithoutTitlePage($submissionFile, $lastRevisionId);
        return $lastRevisionHasTitlePage;
    }

    public function testCanDetectSubmissionFileHasTitlePage(): void
    {
        $this->assertTrue($this->checkIfLastRevisionHasTitlePage());
    }

    public function testCanDetectLegacySubmissionFileHasTitlePage(): void
    {
        $submissionFile = new SubmissionFile();
        $submissionFile->setId(360);
        $titlePageTestsDao = new TitlePageTestsDAO();
        $numberOfRevisions = 1;
        $newRevisoes = json_encode([$numberOfRevisions]);
        $titlePageTestsDao->updateRevisionsWithTitlePageSettingFromSubmissionFile($submissionFile, $newRevisoes);

        $this->assertTrue($this->checkIfLastRevisionHasTitlePage());
    }
}
