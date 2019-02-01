<?php

namespace BitrixHelper;

class Utils
{


	/**
	 * Получаем день недели по-русски
	 * @param int $key Номер дня в формате date('N')
	 * @return string
	 */
	public static function GetWeekDay($key)
	{
		$days = array(
			1 => 'Понедельник',
			2 => 'Вторник',
			3 => 'Среда',
			4 => 'Четверг',
			5 => 'Пятница',
			6 => 'Суббота',
			7 => 'Воскресенье',
		);
		return $days[$key];
	}

	/**
	 * Возвращает размер файла в удобном формате
	 * @param int $size Размер файла в байтах
	 * @param string $lang Локализация (по умолчанию русская)
	 * @return string
	 */
	public static function GetFileSize($size, $lang = 'ru')
	{
		$langArray = array(
			'ru' => array('Кб', 'Мб'),
			'en' => array('Kb', 'Mb'),
		);
		$code = 0;
		$s = ($size / 1024);
		if ($s >= 1024) {
			$s = $s / 1024;
			$code = 1;
		}
		$result = number_format($s, 2, ', ', ' ') . '&nbsp;' . $langArray[$lang][$code];
		return $result;
	}

	/**
	 * Генерирует информацию о youtube ролике
	 * link - ссылка для fancybox и iframe, например
	 * code - код ролика (мало ли для чего пригодится)
	 * img - "скриншот" видео-ролика в высоком разрешении
	 * @param string $url Ссылка на ролик
	 * @return array
	 */
	public static function GetYoutubeLinkInfo($url)
	{
		$result = array();
		if (preg_match('/watch\?v=([^&]*)/ui', $url, $matches)) {
			$result['link'] = '//www.youtube.com/embed/' . $matches[1] . '?wmode=opaque';
			$result['code'] = $matches[1];
			$result['img'] = 'http://img.youtube.com/vi/' . $matches[1] . '/maxresdefault.jpg';
		};
		return $result;
	}

	private $yandex_cleanweb_api_key;
	private $yandex_translate_api_key;

	public static function MonthRu($month_number, $form = 1)
	{

		$m = $month_number - 1;

		$months = array(
			1 => array(
				'Январь',
				'Февраль',
				'Март',
				'Апрель',
				'Май',
				'Июнь',
				'Июль',
				'Август',
				'Сентябрь',
				'Октябрь',
				'Ноябрь',
				'Декабрь',
			),
			2 => array(
				'Января',
				'Февраля',
				'Марта',
				'Апреля',
				'Мая',
				'Июня',
				'Июля',
				'Августа',
				'Сентября',
				'Октября',
				'Ноября',
				'Декабря',
			),
			3 => array(
				'Январе',
				'Феврале',
				'Марте',
				'Апреле',
				'Мае',
				'Июне',
				'Июле',
				'Августе',
				'Сентябре',
				'Октябре',
				'Ноябре',
				'Декабре',
			)
		);
		return $months[$form][$m];
	}

	/**
	 * Генерирует "код" по url
	 * Может быть полезно, для ыгенерации css класса по url страниц
	 * /speaker-form/  => speaker-form
	 * @param bool $url Адрес страницы
	 * @return string
	 */
	public static function GetUrlCode($url = false)
	{
		if (!$url) {
			global $APPLICATION;
			$url = $APPLICATION->GetCurPage();
		}
		$result = preg_replace('/^\/(.+?)\/$/', '\\1', $url);
		$result = str_replace('/', '_', $result);
		return $result;
	}

	/**
	 * Координаты указанные в процентах через запятую переводит в абсолютные величины (для area проценты в пиксели)
	 * @param $coords string Координаты в процентах через запятую (координаты area)
	 * @param $parentWidth int Ширина (map)
	 * @param $parentHeight int Выота (map)
	 * @return string Возвращает координаты через запятую (указанные в пикселях)
	 */
	public static function AreaCoordsToPix($coords, $parentWidth, $parentHeight)
	{

		$coordsArray = explode(',', $coords);
		$resArray = array();
		$cnt = 0;
		foreach ($coordsArray as $ca) {
			$cnt++;

			if ($cnt % 2 == 1) {
				$num = $parentWidth;
				//echo '<pre>' . print_r('x', true) . '</pre>';
			} else {
				$num = $parentHeight;
				//echo '<pre>' . print_r('y', true) . '</pre>';
			}
			$resArray[] = Gleb::PercentToNum($ca, $num);
		}
		return implode(',', $resArray);
	}


	/**
	 * Вычисляет число от процента
	 * @param $percent int Проценты
	 * @param $num int Число
	 * @return mixed
	 */
	public static function PercentToNum($percent, $num)
	{
		return $num * ($percent / 100);
	}

	/**
	 * Создает массив из XML объекта
	 * @param mixed $xmlObject XML объект - результат функции simplexml_load_string(), например
	 * @return array
	 */
	public static function XmlToArray($xmlObject)
	{
		$out = array();
		foreach ((array)$xmlObject as $index => $node) {
			if (empty($node) and (is_object($node) || is_array($node))) {
				$node = '';
			}
			if (is_object($node) || is_array($node)) {
				$out[$index] = SELF::xmlToArray($node);
			} else {
				$out[$index] = $node;
			}
		}
		return $out;
	}

	/**
	 * Выводит что-л. на экран или возвращает строку с этим чем-л.
	 * @param mixed $object Массив, строка или что там еще вы хотите показать себе на экране во время разработки
	 * @param bool $for_all Показываем только админам по умолчанию. можем показывать всем подраяд, в том числе навторизованым пользовтелям
	 * @param bool $return Возвращем результат или выводим его на экран
	 * @return string
	 */
	public static function Message($object, $for_all = false, $return = true)
	{
		$text = '<pre>' . print_r($object, true) . '</pre>';
		if ($for_all) {
			if ($return == false)
				return $text;
			echo $text;
		} else {
			global $USER;
			if ($USER->IsAdmin()) {
				if ($return == false)
					return $text;
				echo $text;
			}
		}
	}


	/**
	 * Возвращает тип файла (строковый код, например xls, pdf, doc). Бывает необходимо когда надо задать класс иконки, например .icon--pdf
	 * @param $file_array array Принимает массив - результат битриксовой функции CFile::GetFileArray($file_id)
	 * @return string Тип файла
	 */
	public static function GetFileType($file_array)
	{
		$type = false;
		switch ($file_array['CONTENT_TYPE']) {
			case 'application/pdf':
				$type = 'pdf';
				break;
			case 'application/x-zip-compressed':
			case 'application/zip':
				$type = 'zip';
				break;
			case 'application/x-rar':
				$type = 'rar';
				break;
			case 'image/jpeg':
				$type = 'jpeg';
				break;
			case 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet':
				$type = 'xls';
				break;
			case 'application/vnd.openxmlformats-officedocument.spreadsheetml.document':
				$type = 'doc';
				break;
			case 'application/octet-stream':
				if (preg_match('/\.(doc|docx)$/', $file_array['FILE_NAME'])) {
					$type = 'doc';
				}
				if (preg_match('/\.(xls|xlsx)$/', $file_array['FILE_NAME'])) {
					$type = 'xls';
				}
				break;
			default:
				$type = "txt";
		}
		return $type;
	}

	/**
	 * Склонение
	 * @static
	 * @param $n int Количество
	 * @param $forms array Формы (1, 2, 5)
	 *
	 * @return string Нужная форма слова
	 */
	public static function Sklon($n, $forms)
	{
		return $n % 10 == 1 && $n % 100 != 11 ? $forms[0] : ($n % 10 >= 2 && $n % 10 <= 4 && ($n % 100 < 10 || $n % 100 >= 20) ? $forms[1] : $forms[2]);
	}

	/**
	 * Письмо с правильными заголовками через mail()
	 * @static
	 * @param $to string Email получателя
	 * @param $subject string Тема письма
	 * @param $text string тело письма
	 * @param $from mixed Email отправителя
	 *
	 * @return boolean Результат отправки письма
	 */
	public static function Pismo($to, $subject, $text, $from)
	{
		$headers = "MIME-Version: 1.0" . "\n";
		$headers .= "Content-type: text/html; charset=utf-8" . "\n";
		$headers .= 'From: ' . $from . "\n";
		$headers .= 'Reply-To: ' . $from . "\n";
		$headers .= 'Return-Path: ' . $from . "\n";
		return mail($to, $subject, $text, $headers);
	}

	public function SetCleanwebAPIKey($key)
	{
		$this->yandex_cleanweb_api_key = $key;
	}

	/**
	 * Проверка формы на спам
	 * @static
	 * @param $value mixed Одномерный массив или строка для проверки на спам
	 *
	 * @return boolean Спам (true) или нет (false)
	 */
	public function IsSpam($value)
	{
		if ($this->yandex_cleanweb_api_key) {
			$url_api = 'http://cleanweb-api.yandex.ru/1.0/';

			if (is_array($value)) {
				$value = implode(", ", $value);
			}

			$ch = curl_init();
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_URL, $url_api . 'check-spam');
			curl_setopt($ch, CURLOPT_POSTFIELDS, 'key=' . urlencode($this->yandex_cleanweb_api_key) . '&body-plain=' . urlencode($value));
			$response = new SimpleXMLElement(curl_exec($ch));
			curl_close($ch);
			return ($response->text['spam-flag'] == 'yes');
		} else {
			print "Не задан API ключ. Используйте SetCleanwebAPIKey. Получить можно тут – <a target='_blank' href='http://api.yandex.ru/key/form.xml?service=cw'>http://api.yandex.ru/key/form.xml?service=cw</a>";
			return false;
		}
	}

	/**
	 * Проверка битрикс-формы на спам
	 * @static
	 * @param $values array Массив со значениями результата формы
	 *
	 * @return boolean Спам (true) или нет (false)
	 */
	public function IsBitrixFormValuesSpam($values)
	{
		$fields_to_check = Array();
		foreach ($values as $id => $value) {
			if ((strpos($id, "form_") === 0) && $value) {
				$fields_to_check[] = $value;
			}
		}
		$string_to_check = implode(", ", $fields_to_check);

		return $this->IsSpam($string_to_check);
	}

	/**
	 * Проверка, вызвана ли страница AJAX'ом
	 * @static
	 *
	 * @return boolean true если ajax
	 */
	public static function IsAjax()
	{
		return (isset($_SERVER['HTTP_X_REQUESTED_WITH']) and $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest');
	}

	/**
	 * Получение информации о регионе по IP
	 * @static
	 * @param $ip string IP адрес (обычно $_SERVER['REMOTE_ADDR'])
	 *
	 * @return array Массив с информацией о регионе
	 */
	public static function GetIPInfo($ip)
	{
		$xml = simplexml_load_string(file_get_contents("http://ipgeobase.ru:7020/geo?ip=" . $ip));
		$result = (array)$xml->ip;
		return $result;
	}

	/**
	 * Переадресация с кодом 301
	 * @static
	 * @param $url string Адрес для переадресации
	 */
	public static function Redirect301($url)
	{
		header('HTTP/1.1 301 Moved Permanently');
		header('Location:' . $url);
	}

	/**
	 * Форматирование числа в красивый вид
	 * @static
	 * @param $number string Число для преобразования
	 * @param $decimals int (optional) Число для преобразования
	 *
	 * @return string Форматированная строка
	 */
	public static function NumFormat($number, $decimals = 0)
	{
		$thousands_sep = '&nbsp;';
		if (phpversion() < '5.4') {
			$thousands_sep = ' ';
		}
		return number_format($number, $decimals, '.', $thousands_sep);
	}

	/**
	 * Транслитерация строки
	 * @access public
	 * @param $str string Строка для транслитерации
	 *
	 * @return string
	 */
	public function translite($str)
	{
		$str = mb_strtolower($str);
		$converter = array(
			'а' => 'a', 'б' => 'b', 'в' => 'v',
			'г' => 'g', 'д' => 'd', 'е' => 'e',
			'ё' => 'e', 'ж' => 'zh', 'з' => 'z',
			'и' => 'i', 'й' => 'y', 'к' => 'k',
			'л' => 'l', 'м' => 'm', 'н' => 'n',
			'о' => 'o', 'п' => 'p', 'р' => 'r',
			'с' => 's', 'т' => 't', 'у' => 'u',
			'ф' => 'f', 'х' => 'h', 'ц' => 'c',
			'ч' => 'ch', 'ш' => 'sh', 'щ' => 'sch',
			'ь' => '', 'ы' => 'y', 'ъ' => '',
			'э' => 'e', 'ю' => 'yu', 'я' => 'ya',
		);
		$str = strtr($str, $converter);
		$str = preg_replace('~[^-a-z0-9_]+~u', '_', $str);
		$str = trim($str, "_");
		return $str;
	}


	function SetTranslateAPIKey($key)
	{
		$this->yandex_translate_api_key = $key;
	}

	/**
	 * Перевод Яндекс.Переводом
	 * @access public
	 * @param $str string Строка для перевода
	 * @param $lang string Направление перевода ("ru-en", "en")
	 * @param $format string Формат текста (plain — текст без разметки / html — текст в формате)
	 *
	 * @return string
	 */
	public function YandexTranslate($str, $lang = 'en', $format = 'plain')
	{
		if ($this->yandex_translate_api_key) {
			$url = file_get_contents('https://translate.yandex.net/api/v1.5/tr.json/translate?key=' . $this->yandex_translate_api_key . '&text=' . urlencode($str) . '&lang=' . $lang . '&format=' . $format);
			$json = json_decode($url);
			return $json->text[0];
		} else {
			print "Не задан API ключ. Используйте SetTranslateAPIKey. Получить можно тут – <a target='_blank' href='http://api.yandex.ru/key/form.xml?service=trnsl'>http://api.yandex.ru/key/form.xml?service=trnsl</a>";
			return false;
		}
	}

	/**
	 * Получение координат точки по адресу
	 * @access public
	 * @param $address string Адрес для которого нужны координаты
	 *
	 * @return string
	 */
	public function getCoordsByAddress($address)
	{
		$params = array(
			'geocode' => $address,
			'format' => 'json',
		);
		$data = json_decode(file_get_contents('http://geocode-maps.yandex.ru/1.x/?' . http_build_query($params, '', '&')));
		$coords = explode(" ", $data->response->GeoObjectCollection->featureMember[0]->GeoObject->Point->pos);
		return $coords[1] . "," . $coords[0];
	}

	/**
	 * Форматирование номера телефона
	 * @access public
	 * @param $phone string Номер телефона
	 *
	 * @return string
	 */
	public function phone_format($phone)
	{
		$format = array(
			'6' => '(4852) ##-##-##',
			'10' => '+7 (###) ###-##-##',
		);
		$phone = str_replace(" ", "", preg_replace("/[^0-9\s]/", "", trim($phone)));
		if (strlen($phone) == 11) {
			$phone = substr($phone, 1);
		} elseif (strlen($phone) == 10) {
			// continue
		} else {
			$phone = false;
		}
		if (strpos($phone, '4852') !== false) {
			$phone = str_replace('4852', '', $phone);
		}
		$phone = trim($phone);
		if (is_array($format)) {
			if (array_key_exists(strlen($phone), $format)) {
				$format = $format[strlen($phone)];
			} else {
				return false;
			}
		}
		$pattern = '/' . str_repeat('([0-9])?', substr_count($format, '#')) . '(.*)/';
		$format = preg_replace_callback(
			str_replace('#', '#', '/([#])/'),
			function () use (&$counter) {
				return '${' . (++$counter) . '}';
			},
			$format
		);

		return ($phone) ? trim(preg_replace($pattern, $format, $phone, 1)) : false;
	}

	/**
	 * Форматирование $_FILES в формат CFile::MakeFileArray()
	 * @access public
	 * @param $file_post array массив $_FILES['some']
	 *
	 * @return array
	 */
	public function reArrayFiles(&$file_post)
	{
		$file_ary = array();
		$file_count = count($file_post['name']);
		$file_keys = array_keys($file_post);
		for ($i = 0; $i < $file_count; $i++) {
			foreach ($file_keys as $key) {
				$file_ary[$i][$key] = $file_post[$key][$i];
			}
		}
		return $file_ary;
	}

	/**
	 * Подстановка года в футере
	 * @param string $year Год основания компании (создания сайта)
	 * @param string $sym Символ-разделитель годов
	 *
	 * @return string
	 */
	public function GetYear($year = '', $sym = '&mdash;')
	{
		if (!$year) $year = date("Y");
		return (($year < date("Y")) ? $year . " " . $sym . " " . date('Y') : $year);
	}
}