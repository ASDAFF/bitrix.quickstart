<?php
class CLWComponents {
	
	//Подключает стили и js файлы указанного плагина
	static function ConnectPlugin($PLUGIN){
		if (!empty($PLUGIN)){$PLUGIN.='/';}else{return false;}
		global $APPLICATION, $LWCOMPONENTS;
		$DIR=$_SERVER['DOCUMENT_ROOT'].$LWCOMPONENTS['PLUGIN_DIR'].$PLUGIN.'/';
		if (@file_exists($DIR)){
			$arFiles=scandir($DIR);
			foreach ($arFiles as $File){
				if(preg_match('/\.(css)/', $File)){
					$APPLICATION->SetAdditionalCSS($LWCOMPONENTS['PLUGIN_DIR'].$PLUGIN.$File);
				}
				if(preg_match('/\.(js)/', $File)){
					$APPLICATION->AddHeadScript($LWCOMPONENTS['PLUGIN_DIR'].$PLUGIN.$File);
				}
			}
			return true;
		} else {
			return false;	
		}
	}
	
	//Возвращает массив всех зарегистрированных плагинов
	static function GetPluginList(){
		global $LWCOMPONENTS;
		
		$DIR=$_SERVER['DOCUMENT_ROOT'].$LWCOMPONENTS['PLUGIN_DIR'];
		if (@file_exists($DIR)){
			$arFiles=scandir($DIR);	
			foreach($arFiles as $File){
				if ($File<>'.' and $File<>'..'){
					$arResult[]=$File;
				}
			}
			return $arResult;
		} else{
			return false;	
		}
	}
	
	
	//Подключает дополнительные расширения
	static function ConnectExtension($EXTENSION, $CREATE_OBJET=true){
		global $LWCOMPONENTS;
		
		$FileExtension=$_SERVER['DOCUMENT_ROOT'].$LWCOMPONENTS['EXTENSION_DIR'].$EXTENSION.'/'.$EXTENSION.'.php';
		if (@file_exists($FileExtension)){
			require_once($FileExtension);
			if ($CREATE_OBJET){
				return new $EXTENSION;
			} else {
				return true;
			}
		} else {
			return false;
		}
	}
	
	//Подключает классы дополнительного расширения
	static function ConnectExtensionClass($EXTENSION, $CLASS, $CREATE_OBJET=true){
		global $LWCOMPONENTS;
		
		$FileExtensionClass=$_SERVER['DOCUMENT_ROOT'].$LWCOMPONENTS['EXTENSION_DIR'].$EXTENSION.'/classes/'.$CLASS.'.php';
		if (@file_exists($FileExtensionClass)){
			$require_once($FileExtensionClass);
			if ($CREATE_OBJET){
				return new $CLASS;
			} else {
				return true;
			}
		} else {
			return false;
		}
	}
	
}