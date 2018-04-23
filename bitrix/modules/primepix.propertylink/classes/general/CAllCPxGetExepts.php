<?php

namespace Primepix;

/**
 * common logic 4 exeptions
 */


abstract class CAllCPxGetExepts {

	public function getExpList() {
		$result = array();

		$dbRes = $this->getList();

		while($row = $dbRes->Fetch()) {

			$result[ $row['ID'] ][] = $row['IBLOCK_ID'];
			$result[ $row['ID'] ][] = $row['CODE'];
		}

		return $result;
	}

	public function getIblockExp($iblock) {
		$result = array();

		$dbRes = $this->getExps($iblock);

		while($row = $dbRes->Fetch()) {

			$result[] = $row['CODE'];
		}

		return $result;
	}

	protected abstract function getList();
	protected abstract function getExps($iblock);

}