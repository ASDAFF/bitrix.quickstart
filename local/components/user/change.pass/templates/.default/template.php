<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */
/** @global CUser $USER */
/** @global CDatabase $DB */
/** @var CBitrixComponentTemplate $this */
/** @var string $templateName */
/** @var string $templateFile */
/** @var string $templateFolder */
/** @var string $componentPath */
/** @var CBitrixComponent $component */
?>

<div class="change_pass">

<?if($arResult['ERROR']){?>
	<span class="error"><?=$arResult['ERROR'];?></span>
<?}?>
<?if($arResult['SUCCESS'] == 'Y'){?>
	<span class="success"><?=GetMessage("SUCCESS");?></span>
<?}?>
	<form action="" method="post">
		<input type="hidden" name="do" value="send" />
		<div class="item">
			<input type="password" value="<?=$_REQUEST['old_password'];?>" name="old_password" placeholder="<?=GetMessage("OLD_PASSWORD");?>" required />
		</div>
		<div class="item">
			<input type="password" value="<?=$_REQUEST['password'];?>" name="password" placeholder="<?=GetMessage("NEW_PASSWORD");?>" required />
		</div>
		<div class="item">
			<input type="password" value="<?=$_REQUEST['confirm_password'];?>" name="confirm_password" placeholder="<?=GetMessage("CONFIRM_NEW_PASSWORD");?>" required />
		</div>
		
		<div class="item">
			<input type="submit" value="<?=GetMessage("SEND");?>" />
		</div>
	</form>
</div>