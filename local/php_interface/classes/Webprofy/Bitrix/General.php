<?
	namespace Webprofy\Bitrix;

	class General{
		/*
			Метод устанавливает CSS и JS, автоматически подгружает их из папок:
				/local/templates/sitename/css/
				/local/templates/sitename/js/

			Пример передаваемых данных:
				$settings = array(
					'js' => array(
						'priority' => array( // те скрипты, которые следует загрузить первыми
							'jcf.js',
							'angular.min.js',
						)
					)
				)
		*/
		function setJSandCSS($settings){
			global $APPLICATION;

			foreach(array(
				array(
					'js',
					'/js/',
					'AddHeadScript',
				),
				array(
					'css',
					'/css/',
					'SetAdditionalCSS',
				)
				
			) as $a){
				list($type, $folder, $applicationFunctionName) = $a;
				$path = SITE_TEMPLATE_PATH.$folder;
				$realPath = $_SERVER['DOCUMENT_ROOT'].$path;

				if(!is_dir($realPath)){
					return;
				}
			
				$files = scandir($realPath);

				foreach($files as $i => $name){
					if($name == '.' || $name == '..'){
						unset($files[$i]);
					}
					if($name[0] == '_'){
						unset($files[$i]);
						array_unshift($files, $name);
					}
				}

				if(isset($settings[$type]['priority'])){
					foreach(array_reverse($settings[$type]['priority']) as $name){
						foreach($files as $i => $name_){
							if($name == $name_){
								unset($files[$i]);
							}
						}
						array_unshift($files, $name);
					}
				}

				foreach($files as $name){
					if($name == '.' || $name == '..'){
						continue;
					}
					$filePath = $path.$name;
					if(!file_exists($_SERVER['DOCUMENT_ROOT'].$filePath)){
						continue;
					}
					$APPLICATION->{$applicationFunctionName}($filePath);
				}	
			}

			$path = SITE_TEMPLATE_PATH.'/css/';
			$realPath = $_SERVER['DOCUMENT_ROOT'].$path;

			if(is_dir($path)){
				foreach(scandir($path) as $name){
					if($name == '.' || $name == '..'){
						continue;
					}
					$APPLICATION->SetAdditionalCSS($path.$name);
				}	
			}

		}
	}
