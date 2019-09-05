<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?
if(count($arResult["PERSON_TYPE"]) > 1)
{
	?>
	<div class="section section_person_type">
		<div class="section_title">
			<div class="section_title_in">
				<span><?=GetMessage("MS_SOA_TEMPL_PERSON_TYPE")?></span>
			</div>
		</div>
		<div class="wrap_person_type">	
		<?foreach($arResult["PERSON_TYPE"] as $v):?>
			<div class="label_wrapper">
                <label for="PERSON_TYPE_<?=$v["ID"]?>" <?if ($v["CHECKED"]=="Y") echo "class='label-active'";?>>
                    <input type="radio" id="PERSON_TYPE_<?=$v["ID"]?>" name="PERSON_TYPE" value="<?=$v["ID"]?>"<?if ($v["CHECKED"]=="Y") echo " checked=\"checked\"";?> onClick="submitForm()">
                    <?=$v["NAME"]?>
                </label>
            </div>
			<?endforeach;?>
		  </div>
		<input type="hidden" name="PERSON_TYPE_OLD" value="<?=$arResult["USER_VALS"]["PERSON_TYPE_ID"]?>" />
	</div>
	<?
}
else
{
	if(IntVal($arResult["USER_VALS"]["PERSON_TYPE_ID"]) > 0)
	{
		//for IE 8, problems with input hidden after ajax
		?>
		<span style="display:none;">
		<input type="text" name="PERSON_TYPE" value="<?=IntVal($arResult["USER_VALS"]["PERSON_TYPE_ID"])?>" />
		<input type="text" name="PERSON_TYPE_OLD" value="<?=IntVal($arResult["USER_VALS"]["PERSON_TYPE_ID"])?>" />
		</span>
		<?
	}
	else
	{
		foreach($arResult["PERSON_TYPE"] as $v)
		{
			?>
			<input type="hidden" id="PERSON_TYPE" name="PERSON_TYPE" value="<?=$v["ID"]?>" />
			<input type="hidden" name="PERSON_TYPE_OLD" value="<?=$v["ID"]?>" />
			<?
		}
	}
}
?>