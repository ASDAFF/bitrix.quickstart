<?php

namespace Admitad\Tracking\Admitad;

use Admitad\Api\Api;
use Bitrix\Main\Config\Option;
use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Web\Json;
use Bitrix\Sale\Internals\OrderPropsTable;
use Bitrix\Sale\Internals\PersonTypeTable;

Loc::loadMessages(__FILE__);

class Admitad
{
	const MODULE_ID = 'admitad.tracking';
	const PARAM_NAME = 'admitad_uid';
	const ADMITAD_COOKIE_KEY = '_aid';

	protected $api;
	protected $redirectUri = '';
	protected $scope = 'advertiser_info';

	/**
	 * @return bool
	 */
	public function isExpired()
	{
		return $this->getExpiresIn() >= time();
	}

	/**
	 * @return bool|null|string
	 */
	public static function getExpiresIn()
	{
		return Option::get(static::MODULE_ID, 'EXPIRES_IN');
	}

	/**
	 * @return bool|null|string
	 */
	public static function getClientId()
	{
		return Option::get(static::MODULE_ID, 'CLIENT_ID');
	}

	/**
	 * @return bool|null|string
	 */
	public static function getClientSecret()
	{
		return Option::get(static::MODULE_ID, 'CLIENT_SECRET');
	}

	/**
	 * @return bool|null|string
	 */
	public static function getToken()
	{
		return Option::get(static::MODULE_ID, 'ACCESS_TOKEN');
	}

	/**
	 * @return bool|null|string
	 */
	public static function getRefreshToken()
	{
		return Option::get(static::MODULE_ID, 'REFRESH_TOKEN');
	}

	/**
	 * @return bool|null|string
	 */
	public static function getParamName()
	{
		return Option::get(static::MODULE_ID, 'PARAM_NAME', static::PARAM_NAME);
	}

	/**
	 * @return bool|null|string
	 */
	public static function getPostbackKey()
	{
		return Option::get(static::MODULE_ID, 'POSTBACK_KEY');
	}

	/**
	 * @return bool|null|string
	 */
	public static function getCampaignCode()
	{
		return Option::get(static::MODULE_ID, 'CAMPAIGN_CODE', static::PARAM_NAME);
	}

	/**
	 * @return bool|null|string
	 */
	public static function getConfiguration()
	{
		return Option::get(static::MODULE_ID, 'CONFIGURATION', '{}');
	}

	/**
	 * @param $value
	 *
	 * @return $this
	 */
	public function setExpiresIn($value)
	{
		Option::set(static::MODULE_ID, 'EXPIRES_IN', $value);

		return $this;
	}

	/**
	 * @param $value
	 *
	 * @return $this
	 */
	public function setClientId($value)
	{
		Option::set(static::MODULE_ID, 'CLIENT_ID', $value);

		return $this;
	}

	/**
	 * @param $value
	 *
	 * @return $this
	 */
	public function setClientSecret($value)
	{
		Option::set(static::MODULE_ID, 'CLIENT_SECRET', $value);

		return $this;
	}

	/**
	 * @param $value
	 *
	 * @return $this
	 */
	public function setAccessToken($value)
	{
		Option::set(static::MODULE_ID, 'ACCESS_TOKEN', $value);

		return $this;
	}

	/**
	 * @param $value
	 *
	 * @return $this
	 */
	public function setPostbackKey($value)
	{
		Option::set(static::MODULE_ID, 'POSTBACK_KEY', $value);

		return $this;
	}

	/**
	 * @param $value
	 *
	 * @return $this
	 */
	public function setCampaignCode($value)
	{
		Option::set(static::MODULE_ID, 'CAMPAIGN_CODE', $value);

		return $this;
	}

	/**
	 * @param $value
	 *
	 * @return $this
	 */
	public function setRefreshToken($value)
	{
		Option::set(static::MODULE_ID, 'REFRESH_TOKEN', $value);

		return $this;
	}

	/**
	 * @param $value
	 *
	 * @return $this
	 */
	public function setParamName($value)
	{
		Option::set(static::MODULE_ID, 'PARAM_NAME', $value);

		return $this;
	}

	/**
	 * @param $value
	 *
	 * @return $this
	 */
	public function setConfiguration($value)
	{
		Option::set(static::MODULE_ID, 'CONFIGURATION', Json::encode($value));

		return $this;
	}

	public function revokeKeys()
	{
		Option::set(static::MODULE_ID, 'ACCESS_TOKEN', null);
		Option::set(static::MODULE_ID, 'REFRESH_TOKEN', null);
	}

	public function getAdvertiserInfo()
	{
		$result = $this->getApi()->get('/advertiser/info/');
		$data = reset($result->getArrayResult());

		array_walk_recursive($data, function (&$name) {
			$name = iconv('utf-8', LANG_CHARSET, $name);
		});

		return $data;
	}

	/**
	 * @return Api
	 */
	public function getApi()
	{
		$this->api = new Api($this->getToken());

		if ($this->isExpired()) {
			$this->api->refreshToken($this->getClientId(), $this->getClientSecret(), $this->getRefreshToken());
		}

		return $this->api;
	}

	/**
	 * @return \Admitad\Api\Response
	 */
	public function authorizeClient()
	{
		$this->api = new Api();

		$response = $this->api->authorizeClient($this->getClientId(), $this->getClientSecret(), $this->scope);
		$data = $response->getArrayResult();

		if (!empty($data['access_token']) and !empty($data['refresh_token']) and !empty($data['expires_in'])) {
			$this
				->setAccessToken($data['access_token'])
				->setRefreshToken($data['refresh_token'])
//				->setExpiresIn(time() + $data['expires_in'])
			;
		}

		return $response;
	}
}