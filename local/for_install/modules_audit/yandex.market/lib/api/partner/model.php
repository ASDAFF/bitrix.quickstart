<?php
namespace Yandex\Market\Api\Partner;

class Model extends Base
{
	public function get($modelId)
	{
		$fullServiceUrl = $this->getServiceUrl('models/' . $modelId);

		$arResult = $this->queryGet($fullServiceUrl);
		/*
		 * TODO
		 * print_r($arResult);
		die;*/

		if (\strtoupper($arResult['content']['status']) === 'OK')
		{
			return $arResult['content']['opinions'];
		}

		throw new \Exception('Data receiving error: ' . implode(PHP_EOL, $arResult['errors']));
	}
}
