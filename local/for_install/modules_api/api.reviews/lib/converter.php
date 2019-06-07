<?php
namespace Api\Reviews;

//use \Bitrix\Main\Text\HtmlConverter;
//use \Bitrix\Main\Text\HtmlFilter;

class Converter extends \Bitrix\Main\Text\Converter
{
	public function encode($text, $textType = "")
	{
		if (is_object($text))
			return $text;

		return Tools::formatText($text);
	}

	public function decode($text, $textType = "")
	{
		if (is_object($text))
			return $text;

		return Tools::formatText($text);
	}

	public static function replace($text, $allow = array())
	{
		//if(in_array('ANCHOR',$allow))
			//$text = preg_replace("#[[:alpha:]]+://[^<>[:space:]]+[[:alnum:]/]#", '<!--noindex--><a rel="nofollow" target="_blank" href="\\0">\\0</a><!--/noindex-->', $text);

		$text = preg_replace(
			 "#[[:alpha:]]+://[^<>[:space:]]+[[:alnum:]/]#",
			 '<span class="api-noindex" data-url="\\0">\\0</span>',
			 $text
		);


		return $text;
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
