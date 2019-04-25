<?php

require __DIR__.'/../core.php';

Logger::send("|СТАРТ| - Скрипт запущен. Парсинг из ".PARSER_NAME);

$pause = 3;
$options = [
	CURLOPT_HTTPHEADER => [
		"Host: prist.ru",
		"Connection: keep-alive",
		"Cache-Control: max-age=0",
		"User-Agent: Mozilla/5.0 (Windows NT 10.0) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/63.0.3239.84 Safari/537.36",
		"Upgrade-Insecure-Requests: 1",
		"Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8",
		"Accept-Encoding: deflate",
		"Accept-Language: ru-RU,ru;q=0.9,en-US;q=0.8,en;q=0.7",
	]
];

$brands = [];

// Наполнение массива $brands
function getBrands($link) {
	global $pause, $options, $brands;
	$brands = [];
	$html = Request::curl($link, $pause, $options);
	$html = iconv('windows-1251', 'utf-8', $html);
	$dom = phpQuery::newDocument($html)->find('a.o');
	foreach ($dom as $a) {
		$brand = trim(pq($a)->text());
		if (!in_array($brand, $brands) and $brand != 'Прочие') {
			$brands[] = $brand;
		}
		unset($a, $brand);
	}
	$dom->unloadDocument();
	unset($link, $html, $dom);
}

// Список основного оглавления раздела
function getHeaders($link) {
	global $pause, $options;
	getBrands($link);
	$html = Request::curl($link, $pause, $options);
	$html = iconv('windows-1251', 'utf-8', $html);
	$dom = phpQuery::newDocument($html);
	$elements = $dom->find("div[style='background-color: #F9F9F9; padding: 5px; margin: 5px;']");
	$headers = [];
	foreach ($elements as $element) {
		$headers[] = [
			'title' => pq($element)->find('h2>a')->text(),
			'link' => 'http://prist.ru'.pq($element)->find('h2>a')->attr('href')
		];
		unset($element);
	}
	$dom->unloadDocument();
	unset($link, $pause, $html, $dom, $elements);
	return $headers;
}

// Подзаголовок
function getSubsection($dom) {
	$subsections = $dom->find('a[href^=/produce/e-cat/]');
	$current = count($subsections)-1;
	$subsection = trim($subsections->eq($current)->text());
	$subsection = preg_replace('/[^\d\w\s\.,:\?\!<>\(\)\-\+\$\*\^\/\@\;\#\%\[\]\{\}]/u', ' ', $subsection);
	unset($subsections, $current);
	return $subsection;
}

// Раздел
function getTab($dom) {
	$tabs = $dom->find('a[href^=/produce/prices/]');
	$current = count($tabs)-1;
	$tab = trim($tabs->eq($current)->text());
	$tab = preg_replace('/[^\d\w\s\.,:\?\!<>\(\)\-\+\$\*\^\/\@\;\#\%\[\]\{\}]/u', ' ', $tab);
	unset($tabs, $current);
	return $tab;
}

// Цена
function getPrice($dom) {
	$price = $dom->find('p:contains("Цена")')->parent()->html();
	$currency = $dom->find('p:contains("Цена")')->parent()->text();
	$currency = str_replace(' ', '', $currency);
	if (preg_match('/.*\d+\D\s*\$.*/u', $currency)) {
		$currency = '2';
	} else {
		$currency = '1';
	}
	$price = str_replace(' ', '', $price);
	preg_match('/.*<p.*<s>.*[^\d](?<s>\d+)[^\d].*<\/s>.*red.*[^\d](?<c>\d+)[^\d].*p>.*/', $price, $matchStock);
	preg_match('/.*<p.*[^\d](?<c>\d+)[^\d].*p>.*/', $price, $matchNDS);
	if (stristr($price, 'позапросу') !== false) {
		unset($price, $currency, $matchStock, $matchNDS);
		return [
			'num' => 0,
			'disc' => 0,
			'currency' => '1',
			'str' => 'По запросу'
		];
	} elseif (!empty($matchStock['c']) and !empty($matchStock['s'])) {
		if (stristr($price, 'Ценанатоварнаскладе') !== false) {
			$c = (int)$matchStock['c'];
			$s = (int)$matchStock['s'];
			unset($price, $matchStock, $matchNDS);
			return [
				'num' => $s,
				'disc' => $s - $c,
				'currency' => $currency,
				'str' => "С НДС: $s. На складе: $c"
			];
		}
	} elseif (!empty($matchNDS['c'])) {
		$c = (int)$matchNDS['c'];
		unset($price, $matchStock, $matchNDS);
		return [
			'num' => $c,
			'disc' => 0,
			'currency' => $currency,
			'str' => "С НДС: $c"
		];
	} else {
		unset($price, $currency, $matchStock, $matchNDS);
		return [
			'num' => 0,
			'disc' => 0,
			'currency' => '1',
			'str' => 'Неизвестно'
		];
	}
}

// Раздел
function getGuarantee($dom) {
	$guarantee = $dom->find('p:contains("Срок гарантии")+p')->text();
	preg_match('/.*[^\d](?<term>\d+)[^\d].*/', $guarantee, $match);
	unset($guarantee);
	if (!empty($match['term'])) {
		return $match['term'];
	}
	unset($match);
	return 'н/д';
}

// Основные данные
function getData($dom) {
	$data = $dom->find('#main')->html();
	$data = str_replace('%', '% ', $data);
	$data = preg_replace('/[^\d\w\s\.\…,:\?\!<>\(\)\-\+\$\*\^\/\@\;\#\%\[\]\{\}="\'±°µ]/u', '', $data);
	$data = preg_replace('/<table.*\/table>/uis', '', $data);
	$data = preg_replace('/(<\/?\w+)(?:\s(?:[^<>\/]|\/[^<>])*)?(\/?>)/ui', '$1$2', $data);
	$data = preg_replace(['/<a>/','/<\/a>/', '/\s{2,}/', '/gt;/', '/lt;/'], ['', '', ' ', '>', '<'], $data);
	return $data;
}

// Дополнительно
function getAdditionally($dom) {
	$data = $dom->find('#descr')->html();
	$data = str_replace('%', '% ', $data);
	$data = preg_replace('/[^\d\w\s\.\…,:\?\!<>\(\)\-\+\$\*\^\/\@\;\#\%\[\]\{\}="\'±°µ]/u', '', $data);
	$data = preg_replace('/<table.*\/table>/uis', '', $data);
	$data = preg_replace('/(<\/?\w+)(?:\s(?:[^<>\/]|\/[^<>])*)?(\/?>)/ui', '$1$2', $data);
	$data = preg_replace(['/<a>/','/<\/a>/', '/\s{2,}/', '/gt;/', '/lt;/'], ['', '', ' ', '>', '<'], $data);
	return $data;
}

// Таблица характеристик
function getDataTable($dom) {
	$res = '';
	$data = $dom->find('#main>table');
	if (count($data) < 1) {
		unset($data, $res);
		return '';
	}
	foreach ($data as $table) {
		$res .= '<br><table class="ttd">'.pq($table)->html().'</table>';
	}
	$data = $res;
	unset($res);
	$data = str_replace('%', '% ', $data);
	$data = preg_replace('/[^\d\w\s\.\…,:\?\!<>\(\)\-\+\$\*\^\/\@\;\#\%\[\]\{\}="\'±°µ]/u', '', $data);
	$data = preg_replace(['/<a[^<]*>/','/<\/a>/', '/\s{2,}/', '/gt;/', '/lt;/'], ['', '', ' ', '>', '<'], $data);
	return $data;
}

// Производитель
function getManufacturer($dom) {
	global $brands;
	$data = $dom->find("p[style='text-align: justify;']")->text();
	foreach ($brands as $brand) {
		if (strpos($data, $brand) !== false) {
			unset($data);
			$brand = preg_replace('/[^\d\w\s\.,:\?\!<>\(\)\-\+\$\*\^\/\@\;\#\%\[\]\{\}]/u', ' ', $brand);
			return $brand;
		}
		unset($brand);
	}
	unset($data);
	return '';
}

// Изображения
function getImages($dom) {
	$images = [];
	$hrefs = $dom->find('a[href^=/produces/catalogue/photos_big/]');
	if (count($hrefs) < 1) {
		unset($hrefs);
		$hrefs = $dom->find('img[src^=/produces/catalogue/photos/]');
		foreach ($hrefs as $href) {
			$images[] = 'http://prist.ru'.pq($href)->attr('src');
			unset($href);
		}
	} else {
		foreach ($hrefs as $href) {
			$images[] = 'http://prist.ru'.pq($href)->attr('href');
			unset($href);
		}
	}
	unset($hrefs);
	return $images;
}

// Артикуль товара
function getCode($link) {
	preg_match('/.*id=-?(?<code>\d+)[^\d]?.*/', $link, $match);
	return $match['code'];
}

// Разбор страницы товара
function parseGood($link, $title) {
	global $pause, $options;
	$html = Request::curl($link, $pause, $options);
	$html = iconv('windows-1251', 'utf-8', $html);
	$html = preg_replace(['/(>[^<])(>)/ui', '/(<)([^>]+<)/ui'], ['$1&gt;', '&lt;$2'], $html);
	$dom = phpQuery::newDocument($html);
	$name = trim($dom->find('h1.card>span')->text());
	$data = [
		'link' => $link,
		'code' => getCode($link),
		'tab' => getTab($dom),
		'section' => $title,
		'subsection' => getSubsection($dom),
		'name' => urlencode($name),
		'altName' => urlencode($name),
		'priceNum' => getPrice($dom)['num'],
		'discount' => getPrice($dom)['disc'],
		'priceStr' => getPrice($dom)['str'],
		'currency' => getPrice($dom)['currency'],
		'guarantee' => getGuarantee($dom),
		'data' => getData($dom),
		'additionally' => getAdditionally($dom).getDataTable($dom),
		'manufacturer' => getManufacturer($dom),
		'images' => getImages($dom),
	];
	$data = json_encode($data);
	$params = [
		CURLOPT_POST => true,
		CURLOPT_POSTFIELDS => "data=$data&token=".TOKEN
	];
	$response = Request::curl(BITRIX, $pause, $params);
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
	$dom->unloadDocument();
	unset($link, $title, $html, $dom, $data, $params, $response);
}

// Проверка на последнюю страницу
function lastPage($match) {
	$cur = (int)$match['cur'];
	$last = (int)$match['last'];
	if ($cur == $last) {
		unset($match, $cur, $last);
		return true;
	}
	unset($match, $cur, $last);
	return false;
}

// Обход ссылок объявлений
function sectionParse($section) {
	global $pause, $options;
	for ($p = 1; true; $p++) {
		Logger::send('|КАТЕГОРИЯ| - Обход категории: "'.$section['title'].'". Страница: '.$p);
		$html = Request::curl($section['link'].'&p='.$p, $pause, $options);
		$html = iconv('windows-1251', 'utf-8', $html);
		preg_match('/.*<strong>(?<cur>\d+)<\/strong>\s*из\s*<strong>(?<last>\d+)<\/strong>.*/', $html, $match);
		$dom = phpQuery::newDocument($html);
		$a = $dom->find('a[href^=/produce/card/]');
		$cur = '';
		foreach ($a as $href) {
			$link = 'http://prist.ru'.pq($href)->attr('href');
			if ($link != $a) {
				$a = $link;
				parseGood($link, $section['title']);
			}
			unset($href, $link);
		}
		$dom->unloadDocument();
		unset($html, $dom, $a, $cur);
		if (lastPage($match)) {
			unset($match);
			break;
		}
		unset($match);
	}
	Logger::send('|КАТЕГОРИЯ| - Окончен обход категории: "'.$section['title'].'".');
	unset($section);
}

Logger::send('|РАЗДЕЛ| - Начат обход раздела "Измерительные приборы".');
foreach (getHeaders('http://prist.ru/produce/prices/meas.htm') as $section) {
	sectionParse($section);
	unset($section);
}
Logger::send('|РАЗДЕЛ| - Окончен обход раздела "Измерительные приборы".');

Logger::send('|РАЗДЕЛ| - Начат обход раздела "Паяльно-ремонтное оборудование".');
foreach (getHeaders('http://prist.ru/produce/prices/sold.htm') as $section) {
	sectionParse($section);
	unset($section);
}
Logger::send('|РАЗДЕЛ| - Окончен обход раздела "Паяльно-ремонтное оборудование".');

Logger::send('|РАЗДЕЛ| - Начат обход раздела "Испытательное оборудование".');
foreach (getHeaders('http://prist.ru/produce/prices/test.htm') as $section) {
	sectionParse($section);
	unset($section);
}
Logger::send('|РАЗДЕЛ| - Окончен обход раздела "Испытательное оборудование".');