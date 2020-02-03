<?php
/**
 * Copyright (c) 3/2/2020 Created By/Edited By ASDAFF asdaff.asad@yandex.ru
 */

namespace Helper\Tools;

class FileLogger{

		private $file;

		function __construct($path, $rewrite = true){
			$path = $_SERVER['DOCUMENT_ROOT'].'/'.$path;
			$this->file = fopen($path, $rewrite ? 'w' : 'a');
		}

		function log($text){
			fwrite($this->file, WP::log($text, 'str nopre'));
		}

		function __destruct(){
			fclose($this->file);
		}
	}