<?php

/**
 * Запросы на сервер
 */
class Request
{
	/**
	 * Стандартные настройки курла
	 */
	static private $curlOptions = [
		CURLOPT_FOLLOWLOCATION => true,
		CURLOPT_ENCODING => "",
		CURLOPT_HTTPHEADER => [
			"User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/60.0.3112.101 Safari/537.36",
		],
	];
	
	/**
	 * Запрос курлом
	 */
	static public function curl($link = false, $pause = false, $options = false, $proxy = false)
	{
		if ($pause) {
			if (!is_numeric($pause)) {
				unset($link, $pause, $options, $proxy);
				Logger::send('|ОШИБКА| - Параметр паузы для CURL не является числом');
				return false;
			}
			sleep((int)$pause);
		}
		if (!$link) {
			unset($link, $pause, $options, $proxy);
			Logger::send('|ОШИБКА| - URL для CURL не задан');
			return false;
		}
		if (!$options) {
			$options = self::$curlOptions;
		}
		$options[CURLOPT_URL] = $link;
		$options[CURLOPT_RETURNTRANSFER] = true;
		if ($proxy) {
			$options[CURLOPT_PROXY] = $proxy;
		}
		$curl = curl_init();
		curl_setopt_array($curl, $options);
		$result = curl_exec($curl);
		curl_close($curl);
		unset($curl, $options, $pause, $proxy);
		if (!$result) {
			Logger::send("|ОШИБКА| - CURL не дал ответ от $link");
			unset($link);
			return false;
		}
		return $result;
	}
}