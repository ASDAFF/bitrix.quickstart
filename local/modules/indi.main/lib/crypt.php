<?php
/**
 * Individ module
 * 
 * @category	Individ
 * @link		http://individ.ru
 * @revision	$Revision$
 * @date		$Date$
 */

namespace Indi\Main;

/**
 * Простой шифровальщик из подручных средств
 */
class Crypt
{
	/**
	 * Кол-во вырезаемых за шаг символов для ключа
	 *
	 * @var integer
	 */
	protected static $cutLen = 1;
	
	/**
	 * Разделилтель размера ключа и значения ключа
	 *
	 * @var string
	 */
	protected static $keySeparator = '-';
	
	/**
	 * Шифрует строку
	 *
	 * @param string $string Строка для шифрования
	 * @param integer $keySize Базовый размер ключа для расшифровки
	 * @return stdClass Объект с полями key - ключ для расшифровки, code - зашифрованная строка
	 */
	public static function encode($string, $keySize = 128)
	{
		$string = base64_encode($string);
		$stringLen = strlen($string);
		$step = max(self::$cutLen + 1, round($stringLen / $keySize));
		
		$result = new \stdClass();
		$result->code = '';
		$result->key = $keySize . self::$keySeparator;
		for ($i = 0; $i < $stringLen; $i += $step) {
			$result->code .= substr($string, $i, $step - self::$cutLen);
			$result->key .= substr($string, $i + $step - self::$cutLen, self::$cutLen);
		}
		
		return $result;
	}
	
	/**
	 * Расшифровывает строку
	 *
	 * @param string $code Зашифрованная строка
	 * @param string $key Ключ для расшифровки
	 * @return string Расшифрованная строка
	 */
	public static function decode($code, $key)
	{
		$keyExp = explode(self::$keySeparator, $key, 2);
		$keySize = (int) $keyExp[0];
		$key = isset($keyExp[1]) ? $keyExp[1] : '';
		$keyLen = strlen($key);
		$stringLen = strlen($code) + $keyLen;
		$step = max(self::$cutLen + 1, round($stringLen / $keySize));
		$codeLen = strlen($code);
		
		$string = '';
		$num = 0;
		for ($i = 0; $i < $codeLen; $i += $step - self::$cutLen) {
			$string .= substr($code, $i, $step - self::$cutLen) . substr($key, $num, self::$cutLen);
			$num++;
		}
		
		return @base64_decode($string);
	}
	
	/**
	 * Возвращает ключ для цифровой подписи
	 *
	 * @return string
	 */
	protected static function getSignKey()
	{
		$LICENSE_KEY = '';
		@include $_SERVER['DOCUMENT_ROOT'] . '/bitrix/license_key.php';
		if ($LICENSE_KEY == '' || strtoupper($LICENSE_KEY) == 'DEMO') {
			$LICENSE_KEY = 'DEMO';
		}
		
		return base64_encode(implode(':', array(
			$_SERVER['REMOTE_ADDR'],
			@filemtime($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/classes/general/version.php'),
			$LICENSE_KEY
		)));
	}
	
	/**
	 * Возвращает цифровую подпись для строки
	 *
	 * @param string $data Строка
	 * @return string
	 */
	public static function getSign($data)
	{
		return md5($data . self::getSignKey());
	}
	
	/**
	 * Конвертирует строку в подписанные данные
	 *
	 * @param string $data Данные
	 * @return string
	 */
	public static function convertToSigned($data)
	{
		return base64_encode(
			serialize(
				array($data, self::getSign($data))
			)
		);
	}
	
	/**
	 * Конвертирует подписанные данные в строку
	 *
	 * @param string $data Подписанные данные
	 * @return string|null
	 */
	public static function convertFromSigned($data)
	{
		$data = unserialize(base64_decode($data));
		
		if (is_array($data) && self::getSign($data[0]) === $data[1]) {
			return $data[0];
		}
		
		return null;
	}
}