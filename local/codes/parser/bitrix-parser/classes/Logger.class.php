<?php

/**
 * Логирование
 */
class Logger
{
	/**
	 * Вывод лога в консоль
	 */
	static public function console($message)
	{
		echo "[".date('H:i:s')."]: ".mb_convert_encoding($message, 'UTF-8', 'auto')."\n";
	}
	
	/**
	 * Запись лога в файл
	 */
	static public function file($message, $dir = false)
	{
		file_put_contents(self::dir($dir)."/logs_".date('Y-m-d').".txt", "[".date('H:i:s')."]: ".mb_convert_encoding($message, 'UTF-8', 'auto')."\r\n", FILE_APPEND);
	}
	
	/**
	 * Запись лога в файл и вывод в консоль
	 */
	static public function send($message, $dir = false)
	{
		echo "[".date('H:i:s')."]: ".$message."\n";
		file_put_contents(self::dir($dir)."/logs_".date('Y-m-d').".txt", "[".date('H:i:s')."]: ".mb_convert_encoding($message, 'UTF-8', 'auto')."\r\n", FILE_APPEND);
	}
	
	/**
	 * Создание нужных директорий и возврат пути
	 */
	static private function dir($name = false)
	{
		if (!$name) {
			$name = PARSER_NAME;
		}
		if (!file_exists(LOGS) or !is_dir(LOGS)) {
			mkdir(LOGS);
		}
		if (!file_exists(LOGS.'/'.$name) or !is_dir(LOGS.'/'.$name)) {
			mkdir(LOGS.'/'.$name);
		}
		return LOGS.'/'.$name;
	}
}
