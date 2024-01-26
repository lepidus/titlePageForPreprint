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
use Illuminate\Support\Facades\DB;

class TitlePageDAO extends DAO
{
    public function getNumberOfRevisions(int $submissionFileId): int
    {
        $numberOfRevisions = DB::table('submission_file_revisions')
            ->where('submission_file_id', $submissionFileId)
            ->count();

        return $numberOfRevisions;
    }
}
