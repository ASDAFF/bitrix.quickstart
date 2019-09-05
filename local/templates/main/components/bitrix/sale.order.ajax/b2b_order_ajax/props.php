<?
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Config\Option;
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
include($_SERVER["DOCUMENT_ROOT"].$templateFolder."/props_format.php");
//echo 2;
//printr($arResult);
$arPersonalProps = array();
$arOrderProps = array();
foreach($arResult['ORDER_PROP'] as $orderPropType => $orderProps)
{
	foreach($orderProps as $key=>$orderProp)
	{
		if(in_array($orderProp['CODE'],array('LAST_NAME','NAME','SECOND_NAME','PHONE','EMAIL')))
		{
			$arPersonalProps[] = $orderProp['CODE'];
		}
		else
		{
			$arOrderProps[] = $orderProp['CODE'];
		}
	}
}
if(sizeof($arPersonalProps) == 2)//fiz
{
	$arOrderProps = array_merge($arOrderProps, $arPersonalProps);
	$arPersonalProps = array();
}
?>
<div class="col-sm-12 sm-padding-left-no">
	<div class="section block_buyer">
		<div class="section_title">
			<div class="section_title_in">
				<span><?=($arPersonalProps)?Loc::getMessage("MS_ORDER_INFO_ABOUT_PAY"):Loc::getMessage("MS_ORDER_INFO_ABOUT_BUYER")?></span>
			</div>
		</div>
		<?php
		if(
				is_array($arParams['BUYER_PERSONAL_TYPE']) &&
				in_array($arResult['ORDER_DATA']['PERSON_TYPE_ID'],$arParams['BUYER_PERSONAL_TYPE']) &&
				is_array($arResult["ORDER_PROP"]["USER_PROFILES"]) &&
				sizeof($arResult["ORDER_PROP"]["USER_PROFILES"]) > 0
		)
		{
			?>
			<div class="wrap-buyer">
				<div class="row">
					<div class="col-sm-9 col-md-8 col-lg-8 sm-padding-right-no buyer-title">
						<?php echo Loc::getMessage("SOA_BUYER_PROFILE");?>
					</div>
					<div class="col-sm-15 col-md-16 col-lg-16 buyer-select">
						<select name="PROFILE_ID" id="ID_PROFILE_ID" onChange="SetContact(this.value)">
							<option value="0"><?=GetMessage("SOA_TEMPL_PROP_NEW_PROFILE")?></option>
							<?
							foreach($arResult["ORDER_PROP"]["USER_PROFILES"] as $arUserProfiles)
							{
								?>
								<option value="<?=$arUserProfiles["ID"] ?>"<?if ($arUserProfiles["CHECKED"]=="Y") echo " selected";?>>
									<?=$arUserProfiles["NAME"]?>
								</option>
								<?
							}
							?>
						</select>
					</div>
				</div>
			</div>
			<?
		}
		?>
		<div class="sale_order_props">
			<?
			PrintPropsForm($arResult["ORDER_PROP"]["USER_PROPS_N"], $arParams["TEMPLATE_LOCATION"], $arOrderProps);
			PrintPropsForm($arResult["ORDER_PROP"]["USER_PROPS_Y"], $arParams["TEMPLATE_LOCATION"], $arOrderProps);
			?>
		</div>
	</div>
</div>
<div class="col-sm-12 sm-padding-right-no">
	<div class="section order_comment <?php if($arPersonalProps) echo 'block_buyer';?>">
		<?php if($arPersonalProps)
		{
			?>
			<div class="section_title">
				<div class="section_title_in">
					<span><?=GetMessage("MS_ORDER_INFO_ABOUT_BUYER")?></span>
				</div>
			</div>
			<div class="sale_order_props">
				<?
				PrintPropsForm($arResult["ORDER_PROP"]["USER_PROPS_N"], $arParams["TEMPLATE_LOCATION"], $arPersonalProps);
				PrintPropsForm($arResult["ORDER_PROP"]["USER_PROPS_Y"], $arParams["TEMPLATE_LOCATION"], $arPersonalProps);
				?>
			</div>
			<?php
		}?>
		<div class="section_title">
			<div class="section_title_in">
				<span><?=GetMessage("MS_ORDER_COMMENTS")?></span>
			</div>
		</div>
		<textarea id="ORDER_DESCRIPTION" name="ORDER_DESCRIPTION"><?=(isset($_POST['ORDER_DESCRIPTION']) && !empty($_POST['ORDER_DESCRIPTION']))?$_POST['ORDER_DESCRIPTION']:"" ?></textarea>
	<?php
	foreach($arResult["ORDER_PROP"]["USER_PROPS_N"] as $arProperties)
	{
		if($arProperties['CODE'] == 'CONFIDENTIAL')
		{
			if(Option::get('sotbit.b2bshop', 'SHOW_CONFIDENTIAL_ORDER','Y') == 'Y')
			{
				?>
				<div class="confidential-field sm-padding-no">
					<input type="hidden" name="<?=$arProperties["FIELD_NAME"]?>" value="1">
					<input type="checkbox" <?php echo ($arProperties["CHECKED"] == 'Y')?'checked="checked"':''?> class="checkbox" name="<?=$arProperties["FIELD_NAME"]?>" id="<?=$arProperties["FIELD_NAME"]?>" value="Y">
					<label for="<?=$arProperties["FIELD_NAME"]?>"><?=Loc::getMessage('SALE_CONFIDENTIAL')?></label>
				</div>
				<?php
			}
			else
			{
				?>
				<input type="hidden" name="<?=$arProperties["FIELD_NAME"]?>" value="Y">
				<?php
			}
		}
	}
	foreach($arResult["ORDER_PROP"]["USER_PROPS_Y"] as $arProperties)
	{
		if($arProperties['CODE'] == 'CONFIDENTIAL')
		{
			?>
			<div class="confidential-field sm-padding-no">
				<input type="hidden" name="<?=$arProperties["FIELD_NAME"]?>" value="1">
				<input type="checkbox" class="checkbox" name="<?=$arProperties["FIELD_NAME"]?>" id="<?=$arProperties["FIELD_NAME"]?>" value="Y">
				<label for="<?=$arProperties["FIELD_NAME"]?>"><?=Loc::getMessage('SALE_CONFIDENTIAL')?></label>
			</div>
			<?php
		}
	}
	?>
	</div>
</div>