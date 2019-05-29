<?php
namespace Yandex\Market\Api\OAuth2\Token;

use Bitrix\Main\Type\DateTime;
use Bitrix\Main\Web\Uri;

class Model extends \Yandex\Market\Reference\Storage\Model
{
	/**
	 * Название класса таблицы
	 *
	 * @return Table
	 */
	public static function getDataClass()
	{
		return Table::getClassName();
	}

	public static function loadByScope($scope)
	{
		$tableClass = static::getDataClass();
		$query = $tableClass::getList($q = [
			'filter' => [
				'%SCOPE' => '/' . $scope . '/',
				'>EXPIRES_AT' => new DateTime(),
			],
		]);

		while ($itemData = $query->fetch())
		{
			return new static($itemData);
		}

		return null;
	}

	public function setToken(Uri $rsUrl)
	{
		$rsUrl->addParams([
			'oauth_token' => $this->getField('ACCESS_TOKEN'),
			'oauth_client_id' => '1a620730ccbd4893ad4615cf8c6025de',
		]);
	}
}
