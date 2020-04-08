<?
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true) {
	die();
}

switch($arParams["STYLE"]) {
	case 'notetext':
		$class = 'alert-success';
		break;
	case 'errortext':
		$class = 'alert-danger';
		break;
	default:
		$class = 'alert-default';
}
?>
<div class="alert <?=$class?>">
	<?=$arParams["MESSAGE"]?>
</div>