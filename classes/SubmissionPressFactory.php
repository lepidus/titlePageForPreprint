<?php

namespace APP\plugins\generic\titlePageForPreprint\classes;

use APP\facades\Repo;
use APP\file\PublicFileManager;
use APP\publication\Publication;
use APP\core\Application;
use APP\plugins\generic\citationStyleLanguage\CitationStyleLanguagePlugin;
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

        $submissionModel = new SubmissionModel();
        $submissionModel->setAllData([
            'title' => $dataPress['title'],
            'status' => $dataPress['status'],
            'doi' => $dataPress['doi'],
            'doiJournal' => $dataPress['doiJournal'],
            'authors' => $dataPress['authors'],
            'submissionDate' => $dataPress['submissionDate'],
            'publicationDate' => $dataPress['publicationDate'],
            'endorserName' => $dataPress['endorserName'],
            'endorserOrcid' => $dataPress['endorserOrcid'],
            'version' => $dataPress['version'],
            'versionJustification' => $dataPress['versionJustification'],
            'isTranslation' => $dataPress['isTranslation'],
            'citation' => $dataPress['citation'],
            'galleys' => $submissionGalleys
        ]);

        return new SubmissionPress($submissionModel, $checklist, $logoPath);
    }

    private function getContextChecklist($context): array
    {
        $checklist = $context->getData('submissionChecklist');

        foreach ($checklist as $locale => $checklistText) {
            preg_match_all('/<li>(.*?)<\/li>/', $checklistText, $matches);

            $checklist[$locale] = $matches[1];
        }

        return $checklist;
    }


    private function getLogoPath($context): string
    {
        $publicFileManager = new PublicFileManager();
        $filesPath = $publicFileManager->getContextFilesPath($context->getId());
        $logoFilePath = $context->getLocalizedData('pageHeaderLogoImage')['uploadName'];

        return $filesPath . DIRECTORY_SEPARATOR . $logoFilePath;
    }

    private function getAuthors($publication)
    {
        $userGroups = [];
        foreach ($publication->getData('authors') as $author) {
            $userGroupId = $author->getData('userGroupId');

            if (!isset($userGroups[$userGroupId])) {
                $userGroups[$userGroupId] = Repo::userGroup()->get($userGroupId);
            }
        }

        $traversableArray = new \ArrayObject($userGroups);

        return $publication->getAuthorString($traversableArray);
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

        $data['isTranslation'] = !is_null($publication->getData('originalDocumentDoi'));
        $data['citation'] = ($data['isTranslation'] ? $this->getSubmissionCitation($submission) : '');

        $data['endorserName'] = $publication->getData('endorserName');
        $data['endorserOrcid'] = $publication->getData('endorserOrcid');

        $status = $publication->getData('relationStatus');
        $relation = array(Publication::PUBLICATION_RELATION_NONE => 'publication.relation.none', Publication::PUBLICATION_RELATION_PUBLISHED => 'publication.relation.published');
        $data['status'] = ($status) ? ($relation[$status]) : ("");

        return $data;
    }

    private function getSubmissionCitation($submission)
    {
        $request = Application::get()->getRequest();
        $cslPlugin = new CitationStyleLanguagePlugin();

        $citation = $cslPlugin->getCitation($request, $submission, 'apa');

        return $citation;
    }
}
