<?php
namespace Yandex\Market\Api\OAuth2;

use Bitrix\Main\Web\HttpClient;

class Provider
{
	protected $appId;
	protected $appSecret;
	protected $httpClient;
	protected $oauthServerHost = 'oauth.yandex.ru';
	protected $oauthServerProtocol = 'https';
	protected $requestedScope = array('market:partner-api');

	public function __construct($parameters)
	{
		foreach ($parameters as $code => $value)
		{
			if (property_exists($this, $code))
			{
				$this->$code = $value;
			}
		}

		if ($this->httpClient === null)
		{
			$this->httpClient = new HttpClient();
		}
	}

	protected function buildLinkToServer($path, $parameters = null)
	{
		if (is_array($parameters))
		{
			$parameters = http_build_query($parameters);
		}

		if ($path[0] !== '/')
		{
			$path = '/' . $path;
		}

		return $this->oauthServerProtocol . '://' . $this->oauthServerHost . $path . ($parameters ? '?' . $parameters : '');
	}

	public function getAuthorizationLink($scope = array())
	{
		return $this->buildLinkToServer('/authorize', array(
			'response_type' => 'code',
			'client_id' => $this->appId,
			'scope' => implode(' ', $scope ?: $this->requestedScope),
		));
	}

	public function redirectClientToAuthorization()
	{
		LocalRedirect($this->getAuthorizationLink(), true, '303 See Other');
	}

	public function fetchTokenByCode($code)
	{
		// $this->httpClient->clearHeaders();
		$this->httpClient->setHeader('Accept', 'application/json');

		$url = $this->buildLinkToServer('/token');

		$this->httpClient->setAuthorization($this->appId, $this->appSecret);
		$rsResult = $this->httpClient->post($url, [
			'grant_type' => 'authorization_code',
			'code' => $code,
		]);

		$arResult = [
			'status' => $this->httpClient->getStatus(),
			'errors' => $this->httpClient->getError(),
		];

		if (!$arResult['errors'])
		{
			$arResult['content'] = $this->decodeResponse($rsResult);

			if (isset($arResult['content']['error']))
			{
				throw new \Exception($arResult['content']['error'] . ': ' . $arResult['content']['error_description']);
			}

			if (isset($arResult['content']['access_token']))
			{
				$addResult = Token\Table::add([
					'TOKEN_TYPE' => $arResult['content']['token_type'],
					'ACCESS_TOKEN' => $arResult['content']['access_token'],
					'REFRESH_TOKEN' => $arResult['content']['refresh_token'],
					'EXPIRES_AT' => \Bitrix\Main\Type\DateTime::createFromTimestamp(time()
						+ (int)$arResult['content']['expires_in']),
					'SCOPE' => '/' . implode('/', $this->requestedScope) . '/',
				]);

				if ($addResult->isSuccess())
				{
					return Token\Model::loadById($addResult->getId());
				}
			}
		}

		throw new \Exception('Generic error ' . $arResult['status'], (int)$arResult['status']);
	}

	protected function decodeResponse($res)
	{
		return json_decode($res, true);
	}
}
