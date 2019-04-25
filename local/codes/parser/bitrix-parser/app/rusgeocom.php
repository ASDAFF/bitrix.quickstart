<?php

require __DIR__.'/../core.php';

Logger::send("|СТАРТ| - Скрипт запущен. Парсинг из ".PARSER_NAME);

$pause = 3;
$options = [
	CURLOPT_HTTPHEADER => [
		"Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8",
		"Accept-Encoding: deflate",
		"Accept-Language: ru-RU,ru;q=0.9,en-US;q=0.8,en;q=0.7",
		"Upgrade-Insecure-Requests: 1",
		"User-Agent: Mozilla/5.0 (Windows NT 10.0) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/63.0.3239.84 Safari/537.36",
	]
];

// Производители
$manufacturers = [
	'Topcon',
	'Sokkia',
	'Leica',
	'Trimble',
	'Spectra Precision',
	'Nikon',
	'УОМЗ',
	'Z+F',
	'Bosch',
	'Condtrol',
	'Nedo',
	'Fluke Networks',
	'Fluke',
	'Stabila',
	'Geo Fennel',
	'Vega',
	'Redtrace',
	'Skil',
	'PLS',
	'Berger',
	'Логис',
	'Radar Systems',
	'Radiodetection',
	'Mala',
	'Credo',
	'Topocad',
	'Pythagoras',
	'Testo',
	'Flir',
	'Seek Thermal',
	'CEM',
	'RGK',
	'Стройприбор',
];

// Массив ссылок на категории товаров
function categories() {
	global $pause, $options;
	$html = Request::curl('http://www.rusgeocom.ru/', $pause, $options);
	if (empty($html)) {
		Logger::send('|ОШИБКА| - Не удалось получить ответ со стороны сервера при сборе ссылок на категории.');
		exit;
	}
	$dom = phpQuery::newDocument($html);
	unset($html);
	$categories = $dom->find('ul>li>ul>li>a');
	$links = [];
	foreach ($categories as $category) {
		$links[] = 'http://www.rusgeocom.ru'.trim(pq($category)->attr('href'));
		unset($category);
	}
	$dom->unloadDocument();
	unset($categories, $dom);
	if (empty($links)) {
		Logger::send('|ОШИБКА| - Не удалось собрать ссылки накатегории.');
		exit;
	}
	return $links;
}

// Отправка товара на запись
function write($data) {
	global $pause;
	$name = urldecode($data['name']);
	$data = json_encode($data);
	$params = [
		CURLOPT_POST => true,
		CURLOPT_POSTFIELDS => "data=$data&token=".TOKEN
	];
	$response = Request::curl(BITRIX, $pause, $params);
	unset($params);
	switch ($response) {
		case 1:
			Logger::send('|ТОВАР| - Товар: "'.$name.'" добавлен.');
			break;
		case 2:
			Logger::send('|ТОВАР| - Товар: "'.$name.'" обновлен.');
			break;
		case 3:
			Logger::send('|ТОВАР| - Товар: "'.$name.'" не обновлен из-за приоритетов.');
			break;
		default:
			Logger::send('|ТОВАР| - Товар: "'.$name.'" не добавлен. Неизвестная ошибка.');
	}
	unset($response, $data, $name);
}

// Артикул товара
function getCode($obj) {
	return 'RGC'.trim($obj->find('#ELEMENT_ID')->attr('value'));
//	$art = trim(preg_replace('/\D/ui', '', $obj->find('div.product-cart__articul')->eq(0)->text()));
//	if (empty($art)) {
//		preg_match('/.*urls:\'.*\/(?<code>.*)\'.*/', $obj->find('script')->text(), $match);
//		if (isset($match['code']) and !empty($match['code'])) {
//			$art = trim($match['code']);
//			$art = preg_replace('/\.\w+$/', '', $art);
//		} else {
//			$art = preg_replace('/[^a-z0-9\s]/i', '', $obj->find('div.product-cart__title')->eq(0)->text());
//			$art = preg_replace('/\s{2,}/i', ' ', $art);
//			$art = trim($art);
//			$art = preg_replace('/\s/', '-', $art);
//			$art = mb_strtolower($art);
//		}
//	}
//	unset($obj);
//	return $art;
}

// Раздел
function getTab($obj) {
	return trim($obj->find('li.show')->eq(0)->find('a')->eq(0)->text());
}

// Секция
function getSection($obj) {
	return trim($obj->find('div.catalog-filter-title')->eq(0)->text());
}

// Подзаголовок
function getSubsection($obj) {
	return trim($obj->find('div.catalog-filter-link')->eq(0)->find('a.active')->eq(0)->text());
}

// Цена
function getPrice($obj) {
	$price = trim($obj->find('meta[itemprop=price]')->attr('content'));
	if (empty($price)) {
		$price = 0;
	}
	$oldPrice = trim($obj->find('div.product-cart__price-old')->eq(0)->text());
	if (empty($oldPrice)) {
		$oldPrice = $price;
	} else {
		$oldPrice = preg_replace('/\D/u', '', $oldPrice);
	}
	$currency = trim($obj->find('meta[itemprop=priceCurrency]')->attr('content'));
	if (empty($currency) or $currency == 'RUR') {
		$currency = 1;
	} else {
		$currency = 2;
	}
	return [
		'num' => $oldPrice,
		'disc' => $oldPrice - $price,
		'currency' => $currency
	];
}

// Основные данные
function getData($obj) {
	$data = $obj->find('div.cont')->eq(0)->html();
	$data = preg_replace('/<div\s*class="usloviya_aktsii">.*?<\/div>/uis', '', $data);
	$data = preg_replace(['/<noindex>/', '/<\/noindex>/'], '', $data);
	$data = str_replace('%', '% ', $data);
	$data = preg_replace('/[^\d\w\s\.\…,:\?\!<>\(\)\-\+\$\*\^\/\@\;\#\%\[\]\{\}="\'±°µ]/u', '', $data);
	$data = preg_replace('/<table.*\/table>/uis', '', $data);
	$data = preg_replace('/(<\/?\w+)(?:\s(?:[^<>\/]|\/[^<>])*)?(\/?>)/ui', '$1$2', $data);
	$data = preg_replace(['/<a>/','/<\/a>/', '/\s{2,}/', '/gt;/', '/lt;/'], ['', '', ' ', '>', '<'], $data);
	$data = trim($data);
	return $data;
}

// Таблица характеристик
function getDataTable($obj) {
	$res = '';
	$data = $obj->find('table.tech');
	unset($obj);
	if (count($data) < 1) {
		unset($data, $res);
		return '';
	}
	foreach ($data as $table) {
		$res .= '<table class="ttd" style="width: 100%;">'.pq($table)->html().'</table>';
	}
	$data = $res;
	unset($res);
	$data = str_replace('%', '% ', $data);
	$data = preg_replace('/[^\d\w\s\.\…,:\?\!<>\(\)\-\+\$\*\^\/\@\;\#\%\[\]\{\}="\'±°µ]/u', '', $data);
	$data = preg_replace(['/<a[^<]*>/','/<\/a>/', '/\s{2,}/', '/gt;/', '/lt;/'], ['', '', ' ', '>', '<'], $data);
	$data = str_replace(' class="tech_item"', '', $data);
	return $data;
}

// Изображения
function getImages($obj) {
	$images = [];
	$imgs = $obj->find('div.popup-product-cart__image-top-inner>img');
	unset($obj);
	if (count($imgs) > 0) {
		foreach ($imgs as $img) {
			$images[] = 'http://www.rusgeocom.ru'.trim(pq($img)->attr('src'));
			unset($img);
		}
	}
	unset($imgs);
	return $images;
}

// Производитель
function getManufacturer($obj) {
	global $manufacturers;
	
	// Ищем в тайтле
	$title = trim($obj->find('title')->eq(0)->text());
	foreach ($manufacturers as $manufacture) {
		if (stristr(mb_strtolower($title), mb_strtolower($manufacture)) !== false) {
			unset($title, $obj);
			return $manufacture;
		}
		unset($manufacture);
	}
	unset($title);
	
	// Ищем в имени
	$name = trim($obj->find('div.product-cart__title')->eq(0)->text());
	foreach ($manufacturers as $manufacture) {
		if (stristr(mb_strtolower($name), mb_strtolower($manufacture)) !== false) {
			unset($name, $obj);
			return $manufacture;
		}
		unset($manufacture);
	}
	unset($name);
	
	// Ищем в крошках
	$breadcrumbs = trim($obj->find('a.breadcrumbs-new__link')->text());
	foreach ($manufacturers as $manufacture) {
		if (stristr(mb_strtolower($breadcrumbs), mb_strtolower($manufacture)) !== false) {
			unset($breadcrumbs, $obj);
			return $manufacture;
		}
		unset($manufacture);
	}
	unset($breadcrumbs, $obj);
	return '';
}

// Имя
function getName($obj) {
	return urlencode(str_replace('"', '\"', trim($obj->find('div.product-cart__title')->eq(0)->text())));
}

// Альтернативное имя
function getAltName($obj) {
	$count = count($obj->find('a.breadcrumbs-new__link')) - 1;
	return urlencode(str_replace('"', '\"', trim($obj->find('a.breadcrumbs-new__link')->eq($count)->text())));
}

// Разбор страницы товара
function good($link) {
	global $pause, $options;
	$html = Request::curl($link, $pause, $options);
	$html = preg_replace(['/(>[^<])(>)/ui', '/(<)([^>]+<)/ui'], ['$1&gt;', '&lt;$2'], $html);
	if (empty($html)) {
		Logger::send('|ОШИБКА| - Не удалось получить ответ со страницы товара: "'.$link.'"');
		exit;
	}
	$dom = phpQuery::newDocument($html);
	unset($html);
	$breadcrumbs = trim(mb_strtolower($dom->find('a.breadcrumbs-new__link')->text()));
	if (stristr($breadcrumbs, 'б/у') !== false or stristr($breadcrumbs, 'уцененные товары') !== false) {
		$dom->unloadDocument();
		unset($breadcrumbs, $dom, $link);
		return null;
	}
	unset($breadcrumbs);
	write([
		'link' => $link,
		'code' => getCode($dom),
		'tab' => getTab($dom),
		'section' => getSection($dom),
		'subsection' => getSubsection($dom),
		'name' => getName($dom),
		'altName' => getAltName($dom),
		'priceNum' => getPrice($dom)['num'],
		'discount' => getPrice($dom)['disc'],
		'priceStr' => 'Отсутствует',
		'currency' => getPrice($dom)['currency'],
		'guarantee' => 'н/д',
		'data' => getData($dom),
		'additionally' => getDataTable($dom),
		'manufacturer' => getManufacturer($dom),
		'images' => getImages($dom),
	]);
	$dom->unloadDocument();
	unset($link, $dom);
}

// Получить кол-во страниц на странице категории
function number($link) {
	global $pause, $options;
	$html = Request::curl($link, $pause, $options);
	if (empty($html)) {
		Logger::send('|ОШИБКА| - Не удалось получить кол-во страниц категории: "'.$link.'"');
		exit;
	}
	unset($link);
	$dom = phpQuery::newDocument($html);
	unset($html);
	$numbers = [];
	$pages = $dom->find('a.ditto_page');
	foreach ($pages as $page) {
		$numbers[] = pq($page)->text();
		unset($page);
	}
	$dom->unloadDocument();
	unset($dom, $pages);
	if (empty($numbers)) {
		return 1;
	}
	return max($numbers);
}

// Обход товаров на страницах категорий
function page($link) {
	global $pause, $options;
	for ($page = 1; $page <= number($link); $page++) {
		$html = Request::curl($link.'?PAGEN_1='.$page, $pause, $options);
		if (empty($html)) {
			Logger::send('|ОШИБКА| - Не удалось получить ответ сервера при переходе на страницу категории: "'.$link.'?PAGEN_1='.$page.'"');
			exit;
		}
		$dom = phpQuery::newDocument($html);
		unset($html);
		$goods = $dom->find('span.goods-item-wrap>a');
		foreach ($goods as $good) {
			$href = trim(pq($good)->attr('href'));
			if ($href == '#' or !$href or empty($href)) {
				unset($good, $href);
				continue;
			}
			good('http://www.rusgeocom.ru'.$href);
			unset($good, $href);
		}
		$dom->unloadDocument();
		unset($dom, $goods);
	}
	unset($link);
}

foreach (categories() as $link) {
	page($link);
	unset($link);
}