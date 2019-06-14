<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) { die(); }

$this->IncludeLangFile('template.php');

$arResult['ERRORS'] = [];

include __DIR__ . '/modifier/tags.php';
include __DIR__ . '/modifier/source-type-enum.php';
include __DIR__ . '/modifier/source-field-enum.php';
include __DIR__ . '/modifier/type-map.php';
include __DIR__ . '/modifier/recommendation.php';
include __DIR__ . '/modifier/default-sources.php';

$arResult['SOURCE_TYPE_ENUM_MAP'] = array_flip(array_keys($arResult['SOURCE_TYPE_ENUM']));