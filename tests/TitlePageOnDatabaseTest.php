<?php

use PKP\tests\DatabaseTestCase;
use APP\submission\Submission;
use PKP\submissionFile\SubmissionFile;
use APP\plugins\generic\titlePageForPreprint\tests\TitlePageTestsDAO;
use APP\plugins\generic\titlePageForPreprint\classes\GalleyAdapterFactory;
use PKP\services\PKPSchemaService;

class TitlePageOnDatabaseTest extends DatabaseTestCase
{
    private function checkIfLastRevisionHasTitlePage()
    {
        $submissionFileDao = $this->createMock(SubmissionFileDAO::class);
        $submissionFile = $this->createMock(SubmissionFile::class);
        $obj = new stdClass();
        $obj->fileId = 1;
        $obj->path = "files/document.pdf";
        $submissionFileDao->method('getRevisions')
            ->willReturn(new \Illuminate\Support\Collection([$obj]));
        $galleyAdapterFactory = new GalleyAdapterFactory($submissionFileDao);
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
        $submissionFile = $this->createMock(SubmissionFile::class);
        $titlePageTestsDao = new TitlePageTestsDAO();
        $numberOfRevisions = 1;
        $newRevisoes = json_encode([$numberOfRevisions]);
        $titlePageTestsDao->updateRevisionsWithTitlePageSettingFromSubmissionFile($submissionFile, $newRevisoes);

        $this->assertTrue($this->checkIfLastRevisionHasTitlePage());
    }
}
