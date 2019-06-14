<?php
namespace Yandex\Market\Api\Content;

use Yandex\Market\Api\Receiver;

class Base extends Receiver
{
	protected $apiVersion = 'v2';
	protected $apiDomain = 'api.content.market.yandex.ru';

	protected function getServiceUrl($method)
	{
		return 'https://' . $this->apiDomain . '/' . $this->apiVersion . '/' . $method;
	}

	protected function setAuthorizationTokens()
	{
		$this->httpClient->setHeader('Authorization', $this->apiToken);
	}

	protected function checkErrorsInResponse($arResult)
	{
		return isset($arResult['content']['status']) && \strtoupper($arResult['content']['status']) === 'ERROR';
	}

	protected function getErrorsFromResponse($arResult)
	{
		$arErrors = [];
		foreach ($arResult['content']['errors'] as $arError)
		{
			$arErrors[] = $arError['message'];
		}

		return $arErrors;
	}
}
