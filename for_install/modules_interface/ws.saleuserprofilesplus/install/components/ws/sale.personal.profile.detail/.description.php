<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arComponentDescription = array(
	"NAME" => GetMessage("WS_SPPD_DEFAULT_TEMPLATE_NAME"),
	"DESCRIPTION" => GetMessage("WS_SPPD_DEFAULT_TEMPLATE_DESCRIPTION"),
	"ICON" => "/images/sale_profile_detail.gif",
	"PATH" => array(
		"ID" => "e-store",
		"CHILD" => array(
			"ID" => "sale_personal",
			"NAME" => GetMessage("WS_SPPD_NAME")
		)
	),
);