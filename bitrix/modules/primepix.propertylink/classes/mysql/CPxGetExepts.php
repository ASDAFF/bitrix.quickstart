<?php

namespace Primepix;

/**
 * db operations 4 exeptions
 */


class CPxGetExepts extends CAllCPxGetExepts {
	const TABLE_EXEPT = 'px_propertylink_exept';

	private $DB = NULL;

	public function __construct() {

		global $DB;

		$this->DB = $DB;
	}

	protected function getList() {
		$strSQL = sprintf("SELECT * FROM `%s`",
				self::TABLE_EXEPT);

		return $this->DB->Query($strSQL, FALSE, 'FILE: '.__FILE__.'<br>LINE: '.__LINE__);
	}


	public function addExept($iblock, $code) {
		$strSQL = sprintf("INSERT INTO `%s` (CODE, IBLOCK_ID) VALUES ('%s', '%s')",
				self::TABLE_EXEPT,
				$code,
				$iblock);

		return $this->DB->Query($strSQL, FALSE, 'FILE: '.__FILE__.'<br>LINE: '.__LINE__);
	}

	public function removeExept($iblock, $code) {
		$strSQL = sprintf("DELETE FROM `%s` WHERE CODE = '%s' AND IBLOCK_ID ='%s'",
				self::TABLE_EXEPT,
				$code,
				$iblock);

		return $this->DB->Query($strSQL, FALSE, 'FILE: '.__FILE__.'<br>LINE: '.__LINE__);
	}

	public function removeAll() {
		$strSQL = sprintf("TRUNCATE `%s` ",
				self::TABLE_EXEPT);

		return $this->DB->Query($strSQL, FALSE, 'FILE: '.__FILE__.'<br>LINE: '.__LINE__);
	}

	protected function getExps($iblock) {
		$strSQL = sprintf("SELECT CODE FROM `%s` WHERE IBLOCK_ID ='%s'",
				self::TABLE_EXEPT,
				$iblock);

		return $this->DB->Query($strSQL, FALSE, 'FILE: '.__FILE__.'<br>LINE: '.__LINE__);
	}

}