<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

if($arResult["FILE"] <> '')
{
	?>
	<div class="info">
		<div class="block">
			<?include($arResult["FILE"]);?>
		</div>
	</div>
	<?
}
?>
