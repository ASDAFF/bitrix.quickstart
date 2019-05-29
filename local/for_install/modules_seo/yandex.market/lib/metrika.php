<?php

namespace Yandex\Market;

use Bitrix\Main;

class Metrika
{
	protected static $isLoaded = false;
	protected static $counterName = 'yaCounter49982011';

	public static function reachGoal($goal)
	{
		static::load();

		if (!isset($_SESSION['YANDEX_MARKET_METRIKA_GOAL_READY'][$goal]))
		{
			if (!isset($_SESSION['YANDEX_MARKET_METRIKA_GOAL_READY']))
			{
				$_SESSION['YANDEX_MARKET_METRIKA_GOAL_READY'] = [];
			}

			$_SESSION['YANDEX_MARKET_METRIKA_GOAL_READY'][$goal] = true;

			$assets = Main\Page\Asset::getInstance();

			$assets->addString('
				<script>
					yamarketMetrikaProvider.callMethod("reachGoal", ["' . $goal . '"]);
				</script>
			');
		}
	}

	public static function load()
	{
		if (static::$isLoaded) { return; }

		static::$isLoaded = true;

		\CJSCore::Init(['jquery']);

		$assets = Main\Page\Asset::getInstance();

		$assets->addJs('/bitrix/js/yandex.market/metrika.js');

		$assets->addString('
			<!-- Yandex.Metrika counter -->
			<script type="text/javascript" >
				var yamarketMetrikaProvider = new YandexMarketBitrixMetrika();
			</script>
			<noscript><div><img src="https://mc.yandex.ru/watch/49982011" style="position:absolute; left:-9999px;" alt="" /></div></noscript>
			<!-- /Yandex.Metrika counter -->
		');
	}
}