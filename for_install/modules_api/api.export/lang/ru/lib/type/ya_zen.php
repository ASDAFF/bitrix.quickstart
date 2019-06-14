<?
$MESS['YA_ZEN'] = array(
	 "CODE"        => "ya_zen",
	 "GROUP"       => "Яндекс",
	 "NAME"        => "Яндекс.Дзен",
	 "DESCRIPTION" => "Разметка ленты RSS, <a href=\"https://yandex.ru/support/zen/publishers/rss-modify.html\" target=\"_blank\">подробнее...</a>",
	 "DATE_FORMAT" => "D, d M Y H:i:s O", //Wed, 02 Oct 2002 15:00:00 +0300
	 "FIELDS"      => array(
			"guid"            => array(
				 "CODE"         => "guid",
				 "NAME"         => "Уникальный идентификатор.",
				 "REQUIRED"     => "Y",
				 "TYPE"         => array('FIELD'),
				 "VALUE"        => array("ID"),
				 "USE_FUNCTION" => "Y",
				 "FUNCTION"     => "Api\Export\Ya\Dzen::getGuid",
			),
			"title"           => array(
				 "CODE"     => "title",
				 "NAME"     => "Заголовок публикации.",
				 "REQUIRED" => 'Y',
				 "TYPE"     => array('FIELD'),
				 "VALUE"    => array("NAME"),
			),
			"link"            => array(
				 "CODE"     => "link",
				 "NAME"     => "URL публикации.",
				 "REQUIRED" => 'Y',
				 "TYPE"     => array('FIELD'),
				 "VALUE"    => array("DETAIL_PAGE_URL"),
			),
			"pdalink"         => array(
				 "CODE" => "pdalink",
				 "NAME" => "Ссылка на адаптированную для мобильных устройств версию статьи.",
			),
			"amplink"         => array(
				 "CODE" => "amplink",
				 "NAME" => "Ссылка на AMP-версию статьи.",
			),
			"media:rating"    => array(
				 "CODE" => "media:rating",
				 "NAME" => "Возрастной рейтинг. Строго ограниченные значения:<br>
    «adult» — контент, который можно показывать только взрослым;<br>
    «nonadult» — контент, который можно показывать взрослым и детям от 13 лет.",
			),
			"pubDate"         => array(
				 "CODE"              => "pubDate",
				 "NAME"              => "Дата и время публикации в формате RFC822, то есть «Wed, 02 Oct 2002 15:00:00 +0300».<br>Если этого элемента нет, Дзен будет считать дату публикации равной дате загрузки ленты RSS.",
				 "REQUIRED"          => 'Y',
				 "TYPE"              => array('FIELD'),
				 "VALUE"             => array("DATE_CREATE"),
				 "USE_DATE_FORMAT"   => "Y",
				 "DATE_FORMAT_VALUE" => "D, d M Y H:i:s O",
			),
			"author"          => array(
				 "CODE"       => "author",
				 "REQUIRED"   => 'Y',
				 "NAME"       => "Имя автора публикации",
				 "USE_TEXT"   => "Y",
				 "TEXT_VALUE" => "Умный Битрикс",
			),
			"category"        => array(
				 "CODE" => "category",
				 "NAME" => "Тематика публикации. Публикация может относиться сразу к нескольким тематикам.",
			),
			/*"enclosure"       => array(
				 "CODE"     => "enclosure",
				 "NAME"     => "Описание изображений, аудио- и видеофайлов в публикации.",
				 "REQUIRED" => 'Y',
				 "TYPE"     => array('FIELD'),
				 "VALUE"    => array("DETAIL_PICTURE"),
			),*/
			"description"     => array(
				 "CODE"         => "description",
				 "NAME"         => "Краткая аннотация публикации.",
				 "TYPE"         => array('FIELD'),
				 "VALUE"        => array("PREVIEW_TEXT"),
				 "USE_FUNCTION" => "Y",
				 "FUNCTION"     => "Api\Export\Ya\Dzen::getPreviewText",
			),
			"content:encoded" => array(
				 "CODE"         => "content:encoded",
				 "NAME"         => "Полный текст публикации (рекомендованный объем — не менее 300 знаков с пробелами) или видеоролик. Содержит внутри элементы для размещения медиаконтента.",
				 "REQUIRED"     => 'Y',
				 "TYPE"         => array('FIELD'),
				 "VALUE"        => array("DETAIL_TEXT"),
				 "USE_FUNCTION" => "Y",
				 "FUNCTION"     => "Api\Export\Ya\Dzen::getDetailText",
			),
			//!!!Кастомное поле не удалять!!!
			"enclosure"       => array(
				 "CODE"      => "enclosure",
				 "NAME"      => "Описание изображений, аудио- и видеофайлов в публикации.<br> <span style='color: red'>Обратите внимание!</span> Настраивать поле не нужно, оно заполняется автоматически из контента в детальном описании",
				 "IS_CUSTOM" => 1,
			),
	 ),
	 "XML_HEADER"  => '<?xml version="1.0" encoding="#ENCODING#"?>
<rss version="2.0" 
	xmlns:content="http://purl.org/rss/1.0/modules/content/" 
	xmlns:dc="http://purl.org/dc/elements/1.1/" 
	xmlns:media="http://search.yahoo.com/mrss/" 
	xmlns:atom="http://www.w3.org/2005/Atom" 
	xmlns:georss="http://www.georss.org/georss">
	<channel>
		<title>#SHOP_NAME#</title>
		<link>#SHOP_URL#</link>
		<description>#SHOP_COMPANY#</description>
		<language>#LANGUAGE_ID#</language>',
	 "XML_FOOTER"  => '	</channel>
</rss>',
	 "XML_OFFER"   => '	<item>
			<title>#title#</title>
			<link>#link#</link>
			<pdalink>#pdalink#</pdalink>
			<amplink>#amplink#</amplink>
			<guid>#guid#</guid>
			<pubDate>#pubDate#</pubDate>
			<media:rating scheme="urn:simple">#media:rating#</media:rating>
			<author>#author#</author>
			<category>#category#</category>
			<description>#description#</description>
			<content:encoded>#content:encoded#</content:encoded>
			#custom#
		</item>',
);