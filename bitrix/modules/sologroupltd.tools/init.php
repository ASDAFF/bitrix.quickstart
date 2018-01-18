<?
//Подключаем данный скрипт из init.php следующей строчкой(чтобы функции были доступны по всему Битриксу):
//	if (file_exists($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/sologroupltd.tools/init.php")){require_once ($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/sologroupltd.tools/init.php");}

	if(!$pls_not_include_module){//Данная переменная устанавливается при подключении модуля первый раз
		if(CModule::IncludeModuleEx('sologroupltd.tools')) $pls_not_include_module = true;//Если мы 2 раза подряд будем подключать модуль сам из себя
		// - у нас будет возникать Warning
	}

//	if(!$pls_not_include_module){//Если модуль не установлен - подключение производиться не будет
		$arFuncSoloTools = array('getibc','dump');//ПРоверяем, возможно такие функции уже здесь существуют
		foreach ($arFuncSoloTools as $ValueSoloTools) {
			if(!function_exists($ValueSoloTools)){
				if(file_exists   ($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/sologroupltd.tools/functions_$ValueSoloTools.php")){
					require_once ($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/sologroupltd.tools/functions_$ValueSoloTools.php");
				}
			}
		}
//	}
?>