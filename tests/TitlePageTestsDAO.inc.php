<?php

use PKP\db\DAO;
use Illuminate\Support\Facades\DB;

class TitlePageTestsDAO extends DAO
{
    public function addTitlePagePresenceSettingToSubmissionFile($submissionFile, string $folhaDeRosto)
    {
        DB::table('submission_file_settings')->insert([
            'submission_file_id' => $submissionFile->getId(),
            'setting_name' => 'folhaDeRosto',
            'setting_value' => $folhaDeRosto,
            'setting_type' => 'string'
        ]);
    }

    public function addRevisionsWithTitlePageSettingToSubmissionFile($submissionFile, string $revisoes)
    {
        DB::table('submission_file_settings')->insert([
            'submission_file_id' => $submissionFile->getId(),
            'setting_name' => 'revisoes',
            'setting_value' => $revisoes,
            'setting_type' => 'string'
        ]);
    }

    public function updateRevisionsWithTitlePageSettingFromSubmissionFile($submissionFile, string $revisoes)
    {
        DB::table('submission_file_settings')
        ->where('submission_file_id', $submissionFile->getId())
        ->where('setting_name', 'revisoes')
        ->update(['setting_value' => $revisoes]);
    }

    public function insertTestFile(string $path, string $mimetype): int
    {
        $id = DB::table('files')->insertGetId([
            'path' => $path,
            'mimetype' => $mimetype
        ]);

        return $id;
    }

    public function restoreTables(array $tables)
    {
        foreach ($tables as $table) {
            $sqls = array(
                "DELETE FROM $table",
                "INSERT INTO $table SELECT * FROM backup_$table",
                "DROP TABLE backup_$table"
            );
            foreach ($sqls as $sql) {
                $this->update($sql, [], true, false);
            }
        }
    }
}
