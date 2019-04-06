<?php
if(!defined("B_PROLOG_INCLUDED")||B_PROLOG_INCLUDED!==true)die();

/**
 * Bitrix vars
 *
 * @var array $arParams
 * @var array $arResult
 * @var CBitrixComponent $this
 * @global CMain $APPLICATION
 * @global CUser $USER
 */

	if (!CModule::IncludeModule("lightweb.components")) return;
	//��������� CSS, JS ����� ������� arcticmodal
	CLWComponents::ConnectPlugin('jquery.arcticmodal');
	//���������� JS ������������ ������� ��� �������� ����� �������� �����
	$component_dir=substr(__DIR__, strpos(__DIR__, "/bitrix"), strlen(__DIR__));
	$APPLICATION->AddHeadScript($component_dir."/js/custom.js");
	
	$this->IncludeComponentTemplate();
