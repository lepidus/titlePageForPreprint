<?php

namespace APP\plugins\generic\titlePageForPreprint\classes;

use APP\facades\Repo;
use PKP\file\PKPPublicFileManager;
use APP\plugins\generic\titlePageForPreprint\classes\SubmissionPress;
use APP\plugins\generic\titlePageForPreprint\classes\SubmissionModel;
use APP\plugins\generic\titlePageForPreprint\classes\GalleyAdapterFactory;

class SubmissionPressFactory
{
    public function createSubmissionPress($submission, $publication, $context): SubmissionPress
    {
        $checklist = $this->getContextChecklist($context);
        $logoPath = $this->getLogoPath($context);
        $dataPress = $this->getDataForPress($submission, $publication);
        $galleys = $publication->getData('galleys');

        foreach ($galleys as $galley) {
            $submissionFileRepo = Repo::submissionFile();
            $galleyAdapterFactory = new GalleyAdapterFactory($submissionFileRepo);
            $submissionGalleys[] = $galleyAdapterFactory->createGalleyAdapter($submission, $galley);
        }

        return new SubmissionPress(
            $logoPath,
            new SubmissionModel(
                $dataPress['title'],
                $dataPress['status'],
                $dataPress['doi'],
                $dataPress['doiJournal'],
                $dataPress['authors'],
                $dataPress['submissionDate'],
                $dataPress['publicationDate'],
                $dataPress['endorserName'],
                $dataPress['endorserOrcid'],
                $dataPress['version'],
                $dataPress['versionJustification'],
                $submissionGalleys
            ),
            $checklist,
            $logoPath
        );
    }

    private function getLogoPath($context)
    {
        $publicFileManager = new PublicFileManager();
        $filesPath = $publicFileManager->getContextFilesPath($context->getId());
        $logoFilePath = $context->getLocalizedData('pageHeaderLogoImage')['uploadName'];

        return $filesPath . DIRECTORY_SEPARATOR . $logoFilePath;
    }

    private function getAuthors($publication)
    {
        $userGroupIds = array_map(function ($author) {
            return $author->getData('userGroupId');
        }, $publication->getData('authors'));
        $userGroups = array_map(function ($userGroupId) {
            return Repo::userGroup()->get($userGroupId);
        }, array_unique($userGroupIds));

        return $publication->getAuthorString($userGroups);
    }

    private function getDataForPress($submission, $publication)
    {
        $data = array();

        $data['title'] = $publication->getTitles();
        $data['doi'] = $publication->getStoredPubId('doi');
        $data['doiJournal'] = $publication->getData('vorDoi');
        $data['authors'] = $this->getAuthors($publication);
        $data['version'] = $publication->getData('version');
        $data['versionJustification'] = $publication->getData('versionJustification');

        $dateSubmitted = strtotime($submission->getData('dateSubmitted'));
        $data['submissionDate'] = date('Y-m-d', $dateSubmitted);
        $datePublished = strtotime($publication->getData('datePublished'));
        $data['publicationDate'] = date('Y-m-d', $datePublished);

        $data['endorserName'] = $publication->getData('endorserName');
        $data['endorserOrcid'] = $publication->getData('endorserOrcid');

        $status = $publication->getData('relationStatus');
        $relation = array(PUBLICATION_RELATION_NONE => 'publication.relation.none', PUBLICATION_RELATION_SUBMITTED => 'publication.relation.submitted', PUBLICATION_RELATION_PUBLISHED => 'publication.relation.published');
        $data['status'] = ($status) ? ($relation[$status]) : ("");

        return $data;
    }
}
