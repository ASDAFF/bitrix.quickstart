<?if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true) die();?>

<div class="bx-hdr-profile">
	<?if ($arParams['SHOW_AUTHOR'] == 'Y'):?>
		<div class="bx-basket-block">
			<i class="fa fa-user"></i>
			<?if ($USER->IsAuthorized()):
				$name = trim($USER->GetFullName());
				if (! $name)
					$name = trim($USER->GetLogin());
				if (strlen($name) > 15)
					$name = substr($name, 0, 12).'...';
				?>
				<a href="<?=$arParams['PATH_TO_PROFILE']?>"><?=htmlspecialcharsbx($name)?></a>
				&nbsp;
				<a href="?logout=yes"><?=GetMessage('TSB1_LOGOUT')?></a>
			<?else:?>
				<a href="<?=$arParams['PATH_TO_REGISTER']?>?login=yes"><?=GetMessage('TSB1_LOGIN')?></a>
				&nbsp;
				<a href="<?=$arParams['PATH_TO_REGISTER']?>?register=yes"><?=GetMessage('TSB1_REGISTER')?></a>
			<?endif?>
		</div>
	<?endif?>

	<div class="bx-basket-block">
		<i class="fa fa-shopping-cart"></i>
		<a href="<?=$arParams['PATH_TO_BASKET']?>"><?=GetMessage('TSB1_CART')?></a>
		<?if ($arParams['SHOW_NUM_PRODUCTS'] == 'Y' && ($arResult['NUM_PRODUCTS'] > 0 || $arParams['SHOW_EMPTY_VALUES'] == 'Y')):?>
			<?=$arResult['NUM_PRODUCTS'].' '.$arResult['PRODUCT(S)']?>
		<?endif?>
		<?if ($arParams['SHOW_TOTAL_PRICE'] == 'Y'):?>
			<br <?if ($arParams['POSITION_FIXED'] == 'Y'):?>class="hidden-xs"<?endif?>/>
			<span>
				<?=GetMessage('TSB1_TOTAL_PRICE')?>
				<?if ($arResult['NUM_PRODUCTS'] > 0 || $arParams['SHOW_EMPTY_VALUES'] == 'Y'):?>
					<strong><?=$arResult['TOTAL_PRICE']?></strong>
				<?endif?>
			</span>
		<?endif?>
		<?if ($arParams['SHOW_PERSONAL_LINK'] == 'Y'):?>
			<br>
			<span class="icon_info"></span>
			<a href="<?=$arParams['PATH_TO_PERSONAL']?>"><?=GetMessage('TSB1_PERSONAL')?></a>
		<?endif?>
	</div>
</div>
