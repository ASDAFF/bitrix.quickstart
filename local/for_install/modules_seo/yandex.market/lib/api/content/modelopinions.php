<?php
/**
 * Sample:
 *
 * CModule::includeModule('yandex.market');
 * $rsOpinions = new \Yandex\Market\Api\Content\ModelOpinions('46fe7ca9-f82a-4a6a-bf9a-ff9b2ec76bae');
 * print_r($rsOpinions->get('1727683629'));
 */
namespace Yandex\Market\Api\Content;

class ModelOpinions extends Base
{
	public function get($modelId, $params = array())
	{
		$fullServiceUrl = $this->getServiceUrl('models/' . $modelId . '/opinions?' . $this->buildQueryParams($params));

		$arResult = $this->queryGet($fullServiceUrl);
		if (\strtoupper($arResult['content']['status']) === 'OK')
		{
			return $arResult['content']['opinions'];
		}

		throw new \Exception('Data receiving error: ' . implode(PHP_EOL, $arResult['errors']));
	}
}
