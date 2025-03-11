<?
if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true) die();

$arResult = array();

// Старт кеша 
// если кеша нет, то выполняет код, заключенный в фигурные скобки,
// в противном случае вместо этой части компонента подгружается кеш.
if ($this->StartResultCache(false, false)) {
    CModule::IncludeModule('iblock');
	
    // Кешируемый код компонента
	
	// Подключение шаблона
    $this->IncludeComponentTemplate(); 
}

// Код, выполняющийся вне зависимости от кеша

?>
