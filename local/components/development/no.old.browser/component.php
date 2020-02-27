<?
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
	die();
}

$arParams["IE_MIN_VERSION"] = isset($arParams["IE_MIN_VERSION"]) ? intval($arParams["IE_MIN_VERSION"]) : 9;
?>

<!--[if lt IE <?=$arParams["IE_MIN_VERSION"]?>]>
	<script data-skip-moving="true" type="text/javascript" src="<?=$this->__path?>/panel.php"></script>
<![endif]-->