<?php

/**
 * @file plugins/reports/titlePageForPreprint/sources/TitlePageDAO.inc.php
 *
 * @class TitlePageDAO
 * @ingroup plugins_generic_titlePageForPreprint
 *
 * Operations for retrieving data for the title page
 */

import('lib.pkp.classes.db.DAO');

use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Support\Collection;

class TitlePageDAO extends DAO {

    public function getNumberOfRevisions(int $submissionFileId): int {
        $numberOfRevisions = Capsule::table('submission_file_revisions')
		->where('submission_file_id', $submissionFileId)
		->count();

        return $numberOfRevisions;
    }

}