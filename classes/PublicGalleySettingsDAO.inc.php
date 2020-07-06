<?php 


class PublicGalleySettingsDAO extends DAO{
/**
	 * Get the settings table name.
	 * @return string
	 */
	protected function _getTableName() {
		return 'publication_galley_settings';
	}
/**
	 * Get the primary key column name.
	 */
	protected function _getPrimaryKeyColumn() {
		return 'galley_id';
	}

	/**
	 * Get the cache name.
	* 
	*/
	protected function _getCacheName() {
		return 'publicationGalleySettings';
	}
	
    function updateSetting($id, $name, $value, $type = null, $isLocalized = false) {
		$keyFields = array('setting_name', 'locale', $this->_getPrimaryKeyColumn());
		if (!$isLocalized) {
			$value = $this->convertToDB($value, $type);
			error_log("valor da conversão: ". $value);
			error_log("nome da tabela: ". $this->_getTableName());
			error_log("nome da chave primária: ". $this->_getPrimaryKeyColumn());

			
			$this->replace($this->_getTableName(),
				array(
					$this->_getPrimaryKeyColumn() => $id,
					'setting_name' => $name,
					'setting_value' => $value,
					'locale' => 'pt_BR'
				),
				$keyFields
			);
			error_log("passou o replace");

		} else {
			if (is_array($value)) foreach ($value as $locale => $localeValue) {
				$this->update('DELETE FROM ' . $this->_getTableName() . ' WHERE ' . $this->_getPrimaryKeyColumn() . ' = ? AND setting_name = ? AND locale = ?', array($id, $name, $locale));
				if (empty($localeValue)) continue;
				$type = null;
				$this->update('INSERT INTO ' . $this->_getTableName() . '
					(' . $this->_getPrimaryKeyColumn() . ', setting_name, setting_value, setting_type, locale)
					VALUES (?, ?, ?, ?, ?)',
					array(
						$id, $name, $this->convertToDB($localeValue, $type), $type, $locale
					)
				);
			}
		}
	}
	
}
