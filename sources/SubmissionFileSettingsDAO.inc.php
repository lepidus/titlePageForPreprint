<?php 

import('lib.pkp.classes.db.SettingsDAO');

class SubmissionFileSettingsDAO extends SettingsDAO{
/**
	 * Get the settings table name.
	 * @return string
	 */
	protected function _getTableName() {
		return 'submission_file_settings';
	}
/**
	 * Get the primary key column name.
	 */
	protected function _getPrimaryKeyColumn() {
		return 'file_id';
	}

	/**
	 * Get the cache name.
	* 
	*/
	protected function _getCacheName() {
		return 'submissionFileSettings';
	}  
	
}