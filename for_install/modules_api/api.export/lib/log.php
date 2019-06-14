<?php

namespace Api\Export;

use \Bitrix\Main\Application;

class Log
{
	protected static function scanDir($dir, $comment, $data, $fileName, $count = 5)
	{
		if(defined('API_EXPORT_LOG') && API_EXPORT_LOG == true) {
			if(!is_dir($dir))
				mkdir($dir, 0755, true);

			$fileUrl = $dir . $fileName . '.txt';

			//if(filesize($fileUrl)>1024*500) // ќчистка если больше 500 б
			//fclose(fopen($fileUrl,'w'));

			$fopenResult = fopen($fileUrl, 'ab');
			$writeData   = $comment . "\n";
			if(strlen($data) > 0)
				$writeData = $comment . ":\t" . $data . "\n";

			if($fopenResult) {
				flock($fopenResult, LOCK_EX);
				fwrite($fopenResult, $writeData);
				fflush($fopenResult);
				flock($fopenResult, LOCK_UN);
				fclose($fopenResult);
			}

			//Save last 30 log files
			$objects = scandir($dir, 1);
			if(is_dir($dir)) {
				$i = 0;
				foreach($objects as $key => $object) {
					if($object != "." && $object != "..") {
						$i++;

						if(filetype($dir . $object) != "dir" && $i >= $count)
							unlink($dir . $object);
					}
					else {
						unset($objects[ $key ]);
					}
				}
				reset($objects);
			}
		}
	}

	public static function write($comment, $data, $fileName)
	{
		$dir = Application::getDocumentRoot() . '/bitrix/catalog_export/api_export_log/';
		self::scanDir($dir, $comment, $data, $fileName);
	}
}
