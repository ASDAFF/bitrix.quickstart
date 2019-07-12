<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<?if (!empty($arResult)):?>
		<nav class="b-top-menu">
			<a href="#index" class="b-top-menu__link b-top-menu__link__type_index"></a>
			<?foreach($arResult as $it):?>
			
				<a href="<?=$it['LINK']?>" class="b-top-menu__link"> <?=$it['TEXT']?> </a>
			<?endforeach;?>
		</nav>
<?endif?>
