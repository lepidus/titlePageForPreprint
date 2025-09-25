<?php

/**
 * @file plugins/reports/titlePageForPreprint/classes/TitlePageDAO.inc.php
 *
 * @class TitlePageDAO
 * @ingroup plugins_generic_titlePageForPreprint
 *
 * Operations for retrieving data for the title page
 */

namespace APP\plugins\generic\titlePageForPreprint\classes;

use PKP\db\DAO;
use APP\submission\Submission;
use Illuminate\Support\Facades\DB;
use APP\plugins\generic\titlePageForPreprint\classes\Endorser;

class TitlePageDAO extends DAO
{
    private const ENDORSEMENT_STATUS_CONFIRMED = 1;

    public function getNumberOfRevisions(int $submissionFileId): int
    {
        $numberOfRevisions = DB::table('submission_file_revisions')
            ->where('submission_file_id', $submissionFileId)
            ->count();

        return $numberOfRevisions;
    }

    public function getEndorsersBySubmission(Submission $submission)
    {
        $publications = $submission->getData('publications')->toArray();
        $publicationsIds = array_map(fn ($publication) => $publication->getId(), $publications);

        $result = DB::table('endorsements')
            ->whereIn('publication_id', $publicationsIds)
            ->where('status', self::ENDORSEMENT_STATUS_CONFIRMED)
            ->select('name', 'orcid')
            ->get();

        $endorsers = [];
        foreach ($result as $row) {
            $endorsers[] = new Endorser(
                $row->name,
                $row->orcid
            );
        }

        return $endorsers;
    }
}
