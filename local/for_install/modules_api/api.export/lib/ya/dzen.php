<?php

/////////////////////////////////////////////////////////
/// яндекс.ƒзен
/////////////////////////////////////////////////////////

namespace Api\Export\Ya;

class Dzen
{
	public static function getGuid($value = '', $field = array(), $item = array(), $profile = array())
	{
		return $profile['SHOP_URL'] . '/' . $value;
	}

	public static function getPreviewText($value = '', $field = array(), $item = array(), $profile = array())
	{
		//if(strlen($text) > 0)
		//$text = trim(substr(strip_tags($text), 0, 300));

		$text = trim($value);

		if(strlen($text) > 0) {
			$text = "<![CDATA[" . static::clearSpaces($text) . "]]>";
		}

		return $text;
	}

	public static function getDetailText($value = '', $field = array(), $item = array(), $profile = array())
	{
		$text = trim($value);

		if(strlen($text) > 0) {
			preg_match_all("/<img[\s\S]*>/U", $text, $matches);

			//ќбернем все теги <img> в <figure>
			if($matches[0]) {
				foreach($matches[0] as $value) {
					$text = str_replace($value, '<figure>' . $value . '</figure>', $text);
				}
			}
		}

		if(strlen($text) > 0) {
			$text = "<![CDATA[" . static::clearSpaces($text) . "]]>";
		}

		return $text;
	}

	public static function getDetailMedia($value = '', $field = array(), $item = array(), $profile = array())
	{
		$text = trim($value);

		$result = array();
		preg_match_all("/<img[\s\S]*>/U", $text, $matches);

		if($matches[0]) {
			foreach($matches[0] as $key => $value) {
				preg_match("/src=[\"'](.+)[\"']/U", $value, $matches_src);

				$src = $matches_src[1];

				if(strlen($src) > 0) {
					if(strpos($src, "resizer2GD.php") !== false) {
						///yenisite.resizer2/resizer2GD.php?url=/upload/monosnap/2015-12-03_11.18.05.png&set=2
						$src = str_replace('/yenisite.resizer2/resizer2GD.php?url=', '', strtok($src, '&'));
					}

					//ќпредел€ем абсолютный url
					$url  = (strpos($src, "http") === false) ? $profile['SHOP_URL'] . $src : $src;

					//ќпредел€ем тип файла
					$info = getimagesize($url);

					//≈сли не определил тип файла, пробуем создать вручную из расширени€
					if(empty($info['mime'])){
						$info['mime'] = 'image/' . substr($url, -3);
					}

					$result[ $key ] = array(
						 'url'    => $url,
						 'src'    => $src,
						 'width'  => $info[0],
						 'height' => $info[1],
						 'type'   => $info['mime'],
					);
				}
			}
		}

		return $result;
	}

	protected static function clearSpaces($text)
	{
		//”берет пробелы вначале строки
		$text = preg_replace("/^\s+/im" . BX_UTF_PCRE_MODIFIER, "", $text);

		//”берет все переносы и табул€цию, все в одну строку будет
		$text = preg_replace("/[\n\r]/im" . BX_UTF_PCRE_MODIFIER, "", $text);

		return $text;
	}
}
