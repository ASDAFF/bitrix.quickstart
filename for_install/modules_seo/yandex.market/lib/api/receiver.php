<?php
namespace Yandex\Market\Api;

use Bitrix\Main\Web\HttpClient;

abstract class Receiver
{
	protected $apiVersion;
	protected $apiToken;
	protected $apiDomain;
	/** @var HttpClient $httpClient */
	protected $httpClient;
	protected $requestedUrl;

	abstract protected function getServiceUrl($method);

	abstract protected function setAuthorizationTokens();

	abstract protected function checkErrorsInResponse($arResult);

	abstract protected function getErrorsFromResponse($arResult);

	public function __construct($apiToken)
	{
		$this->apiToken = $apiToken;
	}

	protected function buildQueryParams(array $params)
	{
		return http_build_query($params, '', '&', PHP_QUERY_RFC1738);
	}

	private function prepareHttpClient()
	{
		if (null === $this->httpClient)
		{
			$this->httpClient = new HttpClient();
		}

		// $this->httpClient->clearHeaders();
		$this->httpClient->setHeader('Accept', 'application/json');

		$this->setAuthorizationTokens();

		$this->httpClient->setTimeout(5);

		return true;
	}

	protected function queryGet($url)
	{
		$this->requestedUrl = $url;
		$this->prepareHttpClient();

		$sResult = $this->httpClient->get($this->requestedUrl);

		$arResult = [
			'status' => $this->httpClient->getStatus(),
			'errors' => $this->httpClient->getError(),
		];

		if (!$arResult['errors'])
		{
			$arResult['content'] = $this->decodeResponse($sResult);

			if ($this->checkErrorsInResponse($arResult))
			{
				$arResult['errors'] = $this->getErrorsFromResponse($arResult);
			}
		}

		return $arResult;
	}

	protected function queryPost($url, $data = array(), $is_multipart = false)
	{
		$this->requestedUrl = $url;
		$this->prepareHttpClient();

		$sResult = $this->httpClient->post($this->requestedUrl, $data, $is_multipart);

		$arResult = [
			'status' => $this->httpClient->getStatus(),
			'errors' => $this->httpClient->getError(),
		];

		if (!$arResult['errors'])
		{
			$arResult['content'] = $this->decodeResponse($sResult);
		}

		return $arResult;
	}

	protected function decodeResponse($res)
	{
		return json_decode($res, true);
	}
}
