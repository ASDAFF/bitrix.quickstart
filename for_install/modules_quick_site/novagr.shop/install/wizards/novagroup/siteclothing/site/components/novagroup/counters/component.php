<?
if(!defined("B_PROLOG_INCLUDED")||B_PROLOG_INCLUDED!==true)die();

if( CModule::IncludeModule("iblock") ) {
} else {
	die("IBLOCK MODULE NOT INSTALLED");
}

if(!isset($arParams["CACHE_TIME"]))
	$arParams["CACHE_TIME"] = 36000000;

// ���� ��� ��������� ���� (�� ���� ����� ��������� ������ � ������� �������� ���)
if ($this->StartResultCache(false))
{
    $system = new Novagroup_Classes_General_System(0,"counters");
    $arResult = $system->getElement();

	$this -> IncludeComponentTemplate();
}


?>