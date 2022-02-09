<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
?>

<?
$path = explode('/',$_SERVER['REQUEST_URI']);

?>

<div class="b-sidebar-filter m-sidebar">
	<div class="b-sidebar__text m-wishlist">Мои виш листы:</div>
<div id='wishlist_sidebar'>
<?foreach($arResult as $cat):?>
	<div class="b-sidebar-wishlist__section">
		<div class="b-sidebar-wishlist__title">
			<h2 class="b-sidebar-wishlist__h2"><?if($path[2]<>$cat['CODE']):?><a href='<?=$cat['SECTION_PAGE_URL']?>'><?endif;?><?=$cat['NAME'];?><?if($path[2]<>$cat['CODE']):?></a><?endif;?><span class="b-sidebar-wishlist__count"><?=$cat['ELEMENT_CNT'];?></span></h2>
		</div>
		<button cat='<?=$cat['ID']?>' class="b-wishlist_list_delete b-button__delete m-cart__delete m-wishlist__delete"></button>
	</div>
<?endforeach;?>

	<div class="b-sidebar-wishlist__section">
		<div class="b-sidebar-wishlist__title">
			<input id='wishlist_name' type="text" class="b-cart-field__input m-wishlist__field" placeholder="Новый виш лист" />
		</div>
		<button id='wishlist_add' class="b-button__delete m-cart__add m-wishlist__delete"></button>
	</div>
	<div class="b-sidebar-socail">
		<div class="b-sidebar__text m-wishlist">Поделиться:</div>
		<div class="b-sidebar-socail__link">
			<?$APPLICATION->IncludeComponent("bitrix:main.include", ".default", array(
				"AREA_FILE_SHOW" => "file",
				"PATH" => "/includes/social.php",
				"EDIT_TEMPLATE" => ""
				),
				false
			);?>
					</div>
	</div>
</div>
</div>