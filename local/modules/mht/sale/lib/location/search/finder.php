<?php
/**
 * Bitrix Framework
 * @package Bitrix\Sale\Location
 * @subpackage sale
 * @copyright 2001-2014 Bitrix
 */
namespace Bitrix\Sale\Location\Search;

use Bitrix\Main;
use Bitrix\Main\DB;
use Bitrix\Main\Localization\Loc;
use Bitrix\Sale\Location;

Loc::loadMessages(__FILE__);

final class Finder
{
	public static function find1($query)
	{
		if(!strlen($query))
			return false;

		$words = WordStatTable::parseQuery($query);

		return ChainTable::search($words);
	}

	public static function find2($query, $offset)
	{
		if(!strlen($query))
			return false;

		$words = WordStatTable::parseQuery($query);

		return WordChainTable::search($words, $offset);
	}
}

