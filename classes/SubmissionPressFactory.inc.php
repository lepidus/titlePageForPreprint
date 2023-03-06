<?php

import('plugins.generic.titlePageForPreprint.classes.SubmissionPress');
import('plugins.generic.titlePageForPreprint.classes.SubmissionModel');
import('plugins.generic.titlePageForPreprint.classes.Translator');
import('plugins.generic.titlePageForPreprint.classes.GalleyAdapterFactory');

class SubmissionPressFactory
{
    public function createSubmissionPress($submission, $publication, $context, $preprintViewUrl): SubmissionPress
    {
        $logoPath = $this->getLogoPath($context);
        $dataPress = $this->getDataForPress($submission, $publication);
        $galleys = $publication->getData('galleys');

        foreach ($galleys as $galley) {
            $submissionFileDao = DAORegistry::getDAO('SubmissionFileDAO');
            $galleyAdapterFactory = new GalleyAdapterFactory($submissionFileDao);
            $submissionGalleys[] = $galleyAdapterFactory->createGalleyAdapter($submission, $galley);
        }

        return new SubmissionPress(
            $logoPath,
            new SubmissionModel($dataPress['status'], $dataPress['doi'], $dataPress['doiJournal'], $dataPress['authors'], $dataPress['submissionDate'], $dataPress['publicationDate'], $preprintViewUrl, $dataPress['version'], $submissionGalleys),
            new Translator($context, $submission, $publication)
        );
    }

    public function getLogoPath($context)
    {
        $publicFileManager = new PublicFileManager();
        $filesPath = $publicFileManager->getContextFilesPath($context->getId());
        $logoFilePath = $context->getLocalizedPageHeaderLogo()['uploadName'];

        return $filesPath . DIRECTORY_SEPARATOR . $logoFilePath;
    }

    private function getAuthors($publication)
    {
        $userGroupIds = array_map(function ($author) {
            return $author->getData('userGroupId');
        }, $publication->getData('authors'));
        $userGroups = array_map(function ($userGroupId) {
            $userGroupDao = DAORegistry::getDAO('UserGroupDAO');
            return $userGroupDao->getbyId($userGroupId);
        }, array_unique($userGroupIds));

        return $publication->getAuthorString($userGroups);
    }

    private function getDataForPress($submission, $publication)
    {
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
}
