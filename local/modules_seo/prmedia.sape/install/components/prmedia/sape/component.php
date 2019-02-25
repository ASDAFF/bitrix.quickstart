<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$sape_id = COption::GetOptionString('prmedia.sape', 'sape_id');

if(!defined('_SAPE_USER'))
{
        define('_SAPE_USER', $sape_id); 
}
require_once($_SERVER['DOCUMENT_ROOT'].'/'._SAPE_USER.'/sape.php'); 

$o['force_show_code'] = true;

if(SITE_CHARSET == 'UTF-8') 
{
	$o[charset] = 'utf-8';
	$sape = new SAPE_client($o);
}
else $sape = new SAPE_client($o);

if($arParams['COUNT'] != 0) $arResult['SAPE_CODE'] = $sape->return_links($arParams['COUNT']);
else $arResult['SAPE_CODE'] = $sape->return_links();

$this->IncludeComponentTemplate();
?>