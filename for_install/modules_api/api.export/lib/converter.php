<?php

namespace Api\Export;

//use \Bitrix\Main\Text\HtmlConverter;
//use \Bitrix\Main\Text\HtmlFilter;

class Converter extends \Bitrix\Main\Text\Converter
{
	public function encode($text, $textType = "")
	{
		if(is_object($text))
			return $text;

		return htmlspecialcharsbx($text);
	}

	public function decode($text, $textType = "")
	{
		if(is_object($text))
			return $text;

		return htmlspecialcharsbx($text);
	}

	/*
	public function encode($text, $textType = "")
	{
		//return HtmlFilter::encode($text);
		//return HtmlConverter::encode($text);
	}

	public function decode($text, $textType = "")
	{
		return nl2br($text);
		//return HtmlConverter::decode($text);
	}
	*/

	/*public function encode($text, $textType = "")
	{
		if ($text instanceof \Bitrix\Main\Type\DateTime)
			return $text->format('Y-m-d H:i:s');

		return \Bitrix\Main\Text\String::htmlEncode($text);
	}

	public function decode($text, $textType = "")
	{
		if (is_object($text))
			return $text;

		return \Bitrix\Main\Text\String::htmlDecode($text);
	}*/
}
