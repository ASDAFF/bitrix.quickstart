<?
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
	die();
}
?>

<div class="catalog-filter catalog-filter-default">
	<?/*<h2><?=GetMessage("IBLOCK_FILTER_TITLE")?></h2>*/?>
	<form class="form form-filter" action="<?=$arResult["FORM_ACTION"]?>" method="get" role="filter">
		<div class="row">
			<?foreach($arResult["ITEMS"] as $arItem) {
				if (array_key_exists("HIDDEN", $arItem)) {
					print $arItem["INPUT"];
					continue;
				}
				
				$domId = 'id-' . md5($arItem['INPUT_NAME']);
				$arItem["INPUT"] = str_replace(
					array(
						'<input',
						'<select',
					),
					array(
						'<input class="form-control" id="' . $domId . '"',
						'<select class="form-control" id="' . $domId . '"',
					),
					$arItem["INPUT"]
				);
				?>
				<div class="col-sm-6 col-md-4 col-lg-3">
					<div class="form-group">
						<label class="control-label" for="<?=$domId?>"><?=$arItem["NAME"]?>:</label>
						<?=$arItem["INPUT"]?>
					</div>
				</div>
				<?
			}?>
		</div>
		
		<div class="form-group form-toolbar">
			<input class="btn btn-default" type="submit" value="<?=GetMessage("IBLOCK_SET_FILTER")?>"/>
			<?/*<input class="btn btn-default" type="submit" name="del_filter" value="<?=GetMessage("IBLOCK_DEL_FILTER")?>"/>*/?>
		</div>
		<input type="hidden" name="set_filter" value="Y"/>
	</form>
</div>