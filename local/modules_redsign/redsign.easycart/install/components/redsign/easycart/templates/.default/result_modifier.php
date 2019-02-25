<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

if( IntVal($arParams['Z_INDEX'])<1 )
{
	$arParams['Z_INDEX'] = 500;
}