<?
/************************************
*
* last update 29.10.2015
*
************************************/

IncludeModuleLangFile(__FILE__);

class RSColor
{
	private $_hex;
	private $_hsl;
	private $_rgb;

	const DEFAULT_ADJUST = 10;

	function __construct($hex)
	{
		$this->_hex = self::_checkHex($hex);
		$this->_hsl = self::hex2Hsl($this->_hex);
		$this->_rgb = self::hex2Rgb($this->_hex);
	}

	public static function hex2Hsl($color)
	{
		$color = self::_checkHex($color);

		$R = hexdec($color[0].$color[1]);
		$G = hexdec($color[2].$color[3]);
		$B = hexdec($color[4].$color[5]);
		$var_R = ($R / 255);
		$var_G = ($G / 255);
		$var_B = ($B / 255);
		$var_Min = min($var_R, $var_G, $var_B);
		$var_Max = max($var_R, $var_G, $var_B);
		$del_Max = $var_Max - $var_Min;
		$hsl = array(
            'H' => 0,
            'S' => 0,
            'L' => ($var_Max + $var_Min) / 2
        );
		if ($del_Max == 0) {
			$hsl['H'] = 0;
			$hsl['S'] = 0;
		} else {
			if ($hsl['L'] < 0.5) {
				$hsl['S'] = $del_Max / ($var_Max + $var_Min);
			} else {
				$hsl['S'] = $del_Max / (2 - $var_Max - $var_Min);
			}
			$del_R = ((($var_Max - $var_R) / 6) + ($del_Max / 2)) / $del_Max;
			$del_G = ((($var_Max - $var_G) / 6) + ($del_Max / 2)) / $del_Max;
			$del_B = ((($var_Max - $var_B) / 6) + ($del_Max / 2)) / $del_Max;
			if ($var_R == $var_Max) {
				$hsl['H'] = $del_B - $del_G;
			} else if ($var_G == $var_Max) {
				$hsl['H'] = (1 / 3) + $del_R - $del_B;
			} else if ($var_B == $var_Max) {
				$hsl['H'] = (2 / 3) + $del_G - $del_R;
			}
			if ($hsl['H'] < 0) {
				$hsl['H']++;
			}
			if ($hsl['H'] > 1) {
				$hsl['H']--;
			}
		}
		$hsl['H'] = ($hsl['H'] * 360);
		return $hsl;
	}

	public static function hsl2Hex($hsl = array())
	{
		if (empty($hsl) || !isset($hsl['H']) || !isset($hsl['S']) || !isset($hsl['L'])) {
			throw new Exception('Param was not an HSL array');
		}

		list($H, $S, $L) = array($hsl['H'] / 360, $hsl['S'], $hsl['L']);
		if ($S == 0) {
			$r = $L * 255;
			$g = $L * 255;
			$b = $L * 255;
		} else {
			if ($L < 0.5) {
				$var_2 = $L * (1 + $S);
			} else {
				$var_2 = ($L + $S) - ($S * $L);
			}
			$var_1 = 2 * $L - $var_2;
			$r = round(255 * self::_hue2Rgb($var_1, $var_2, $H + (1/3)));
			$g = round(255 * self::_hue2Rgb($var_1, $var_2, $H));
			$b = round(255 * self::_hue2Rgb($var_1, $var_2, $H - (1/3)));
		}

		$r = dechex($r);
		$g = dechex($g);
		$b = dechex($b);

		$r = (strlen(''.$r) === 1) ? '0'.$r : $r;
		$g = (strlen(''.$g) === 1) ? '0'.$g : $g;
		$b = (strlen(''.$b) === 1) ? '0'.$b : $b;
		return $r.$g.$b;
	}

	public static function hex2Rgb($color)
	{
		$color = self::_checkHex($color);

		$R = hexdec($color[0].$color[1]);
		$G = hexdec($color[2].$color[3]);
		$B = hexdec($color[4].$color[5]);
		$RGB['R'] = $R;
		$RGB['G'] = $G;
		$RGB['B'] = $B;
		return $RGB;
	}

	public static function rgb2Hex($rgb = array())
	{
		if (empty($rgb) || !isset($rgb['R']) || !isset($rgb['G']) || !isset($rgb['B'])) {
			throw new Exception('Param was not an RGB array');
		}
		return sprintf('%02X%02X%02X', $rgb['R'], $rgb['G'], $rgb['B']);
	}

	public function darken($amount = self::DEFAULT_ADJUST)
	{
		$return = clone $this;

		if ($amount) {
			$return->_hsl['L'] = ($return->_hsl['L'] * 100) - $amount;
			$return->_hsl['L'] = ($return->_hsl['L'] < 0) ? 0 : $return->_hsl['L'] / 100;
		} else {
			$return->_hsl['L'] = $return->_hsl['L'] / 2;
		}
		$return->_hex = self::hsl2Hex($return->_hsl);
		$return->_rgb = self::hex2Rgb($return->_hex);
		return $return;
	}

	public function lighten($amount = self::DEFAULT_ADJUST)
	{
		$return = clone $this;
		if ($amount) {
			$return->_hsl['L'] = ($return->_hsl['L'] * 100) + $amount;
			$return->_hsl['L'] = ($return->_hsl['L'] > 100) ? 1 : $return->_hsl['L'] / 100;
		} else {
			$return->_hsl['L'] += (1 - $return->_hsl['L']) / 2;
		}
		$return->_hex = self::hsl2Hex($return->_hsl);
		$return->_rgb = self::hex2Rgb($return->_hex);
		return $return;
	}

	public function adjustHue($deg = self::DEFAULT_ADJUST)
	{
		$return = clone $this;
		$return->_hsl['H'] = ($return->_hsl['H'] + $deg);
		$return->_hex = self::hsl2Hex($return->_hsl);
		$return->_rgb = self::hex2Rgb($return->_hex);
		return $return;
	}

	public function saturate($amount = self::DEFAULT_ADJUST)
	{
		$return = clone $this;
		if ($amount) {
			$return->_hsl['S'] = ($return->_hsl['S'] * 100) + $amount;
			$return->_hsl['S'] = ($return->_hsl['S'] > 100) ? 1 : $return->_hsl['S'] / 100;
		} else {
			$return->_hsl['S'] += (1 - $return->_hsl['S']) / 2;
		}
		$return->_hex = self::hsl2Hex($return->_hsl);
		$return->_rgb = self::hex2Rgb($return->_hex);
		return $return;
	}

	public function desaturate($amount = self::DEFAULT_ADJUST)
	{
		$return = clone $this;
		if ($amount) {
			$return->_hsl['S'] = ($return->_hsl['S'] * 100) - $amount;
			$return->_hsl['S'] = ($return->_hsl['S'] < 0) ? 0 : $return->_hsl['S'] / 100;
		} else {
			$return->_hsl['S'] = $return->_hsl['S'] / 2 ;
		}
		$return->_hex = self::hsl2Hex($return->_hsl);
		$return->_rgb = self::hex2Rgb($return->_hex);
		return $return;
	}

	public function invert()
	{
		$return = clone $this;
		$return->_rgb['R'] = 255 - $this->_rgb['R'];
		$return->_rgb['G'] = 255 - $this->_rgb['G'];
		$return->_rgb['B'] = 255 - $this->_rgb['B'];
		$return->_hex = self::rgb2Hex($return->_rgb);
		return $return;
	}

	public function getHsl()
	{
		return $this->_hsl;
	}

	public function getHex()
	{
		return $this->_hex;
	}

	public function getRgb($color = '')
	{
		if ($color == 'R' || $color == 'G' || $color == 'B') {
			return $this->_rgb[$color];
		} else {
			return $this->_rgb;
		}
	}

	public function getRgba($opacity = 1) {
			return implode(', ', $this->_rgb).', '.$opacity;
	}

	private static function _hue2Rgb($v1, $v2, $vH){
		if ($vH < 0) {
			$vH += 1;
		}
		if ($vH > 1) {
			$vH -= 1;
		}
		if ((6 * $vH) < 1) {
			return ($v1 + ($v2 - $v1) * 6 * $vH);
		}
		if ((2 * $vH) < 1) {
			return $v2;
		}
		if ((3 * $vH) < 2) {
			return ($v1 + ($v2 - $v1) * ((2 / 3) - $vH) * 6);
		}
		return $v1;
	}

	private static function _checkHex($hex)
	{
		$color = str_replace('#', '', $hex);
		if (strlen($color) == 3) {
			$color = $color[0].$color[0].$color[1].$color[1].$color[2].$color[2];
		} else if (strlen($color) != 6) {
			throw new Exception('HEX color needs to be 6 or 3 digits long');
		}
		return $color;
	}
}