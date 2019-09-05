<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<div class="section section_person_type">
	<div class="section_title">
		<div class="section_title_in">
			<span><?=GetMessage("MS_SOA_TEMPL_PERSON_TYPE")?></span>
		</div>
	</div>
	<div class="wrap_person_type">	
		<?php 
		if($arResult['BUYERS'])
		{
			foreach($arResult['BUYERS'] as $idBuyer => $buyer)
			{
				?>
				<div class="label_wrapper" onClick="SetContact('<?=$idBuyer?>')">
					<label for="PROFILE_ID_<?=$idBuyer?>" <?if ($buyer["CHECKED"]=="Y") echo "class='label-active'";?>>
						<input type="radio" data-person-type="<?=$buyer['PERSON_TYPE_ID']?>" id="PROFILE_ID_<?=$idBuyer?>" name="PROFILE_ID" value="<?=$idBuyer?>"<?if ($buyer["CHECKED"]=="Y") echo " checked=\"checked\"";?> >
						<?=$buyer['ORG'].' ('.$buyer['INN'].')'?>
					</label>
				</div>
				<?php
			}
		}
		else
		{
			?>
			<span><?=GetMessage("MS_SOA_TEMPL_PERSON_TYPE_NO")?></span>
			<?php
		}
		?>
	</div>
</div>
<?
if(count($arResult["PERSON_TYPE"]) > 1)
{
	foreach($arResult["PERSON_TYPE"] as $v)
	{
		if($v["CHECKED"]=="Y")
		{
			?>
			<input type="hidden" name="PERSON_TYPE" value="<?=$v['ID']?>" />
			<?php
		}
	}
	?>
	<input type="hidden" name="PERSON_TYPE_OLD" value="<?=$arResult["USER_VALS"]["PERSON_TYPE_ID"]?>" />
	<?php
}
else 
{
	if(IntVal($arResult["USER_VALS"]["PERSON_TYPE_ID"]) > 0)
	{
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