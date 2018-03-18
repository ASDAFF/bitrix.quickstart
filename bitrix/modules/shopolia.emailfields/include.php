<?
/**
 * Основной класс модуля. Хранит в себе все необходимые процедуры
 * @author Aleksandras Ostroumovas (info@shopolia.com)
 * @link http://www.shopolia.com/
 * @version 1.0.1
 * @todo 
 */
 
global $APPLICATION, $MESS, $DBType;
IncludeModuleLangFile(__FILE__);

CModule::AddAutoloadClasses(
	"shopolia.emailfields",
	array(
		"CShopoliaEmailFieldsHandlers" => "classes/".$DBType."/CShopoliaEmailFieldsHandlers.php", // Различные хэндлеры модуля
	)
);