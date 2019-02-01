<?
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/epages.pickpoint/include.php");
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/epages.pickpoint/constants.php");
$iModuleID = "epages.pickpoint";
$ST_RIGHT = $APPLICATION->GetGroupRight($iModuleID);
if ($ST_RIGHT=="D") $APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));
IncludeModuleLangFile(__FILE__);
$message = null;
if(!empty($_REQUEST["EXPORT"])&&($_REQUEST["export"]))
{
	$bError = false;
	$arExportIDs = Array();
	foreach($_REQUEST["EXPORT"] as $iOrderID=>$arFields)
	{

		
		if($arFields["EXPORT"])
		{
			$obOrder = CSaleOrder::GetList(Array(),Array("ID"=>$iOrderID),false,false,Array("ID","PRICE","PAY_SYSTEM_ID","PERSON_TYPE_ID"));
			if($arOrder = $obOrder->Fetch())
			{
				/*if(CPickpoint::CheckPPPaySystem($arOrder["PAY_SYSTEM_ID"],$arOrder["PERSON_TYPE_ID"]))
				{
					if(FloatVal($arFields["PAYED"])<=0 || FloatVal($arFields["PAYED"])>FloatVal($arOrder["PRICE"]))
					{
						$APPLICATION->ThrowException(GetMessage("PP_PRICE_ERROR", Array ("#ORDER_ID#" => $arOrder["ID"],"#PRICE#"=>$arOrder["PRICE"])));
						$bError=true;
						break;
					}			
					
				}*/
				$arExportIDs[] = $arOrder["ID"];
			}
			else 
			{
				$APPLICATION->ThrowException(GetMessage("NO_ORDER", Array ("#ORDER_ID#" => $iOrderID)));
				$bError=true;
				break;
			}
		}
	
	}


	if(!$bError)
	{
		CPickpoint::ExportOrders($arExportIDs);
		//CPickpoint::ExportXML($arExportIDs);
	}


    if($e = $APPLICATION->GetException())
    {
	    $message = new CAdminMessage(GetMessage("rub_save_error"), $e);
    }
    else
    {
      LocalRedirect("/bitrix/admin/pickpoint_export.php?lang=".LANG."&mess=ok");
    }
	///die();

}

elseif ($_REQUEST["save"])
{
	foreach($_REQUEST["EXPORT"] as $iOrderID=>$arFields)
	{
        CPickpoint::SaveOrderOptions($iOrderID);
    }
    LocalRedirect("/bitrix/admin/pickpoint_export.php?lang=".LANG."&mess=save");
}





	$arTabs = Array();
	$arTabs[] = Array(
		"DIV"=>"export",
		"TAB"=>GetMessage("PP_EXPORT"),
		"TITLE"=>GetMessage("PP_EXPORT")
	);
	$tabControl = new CAdminTabControl("tabControl", $arTabs);		
?>
<script>
	function SelectAll(cSelectAll)
	{
		bVal = (cSelectAll.checked);
		Table = document.getElementById("table_export");
		arInputs = Table.getElementsByClassName("cToExport");
		for(i=0;i<arInputs.length;i++)
		{
		    if (!arInputs[i].hasAttribute("disabled"))
			arInputs[i].checked=bVal;
		}

	}
	function CheckFields(cSelectAll)
	{
		Table = document.getElementById("table_export");
		arInputs = Table.getElementsByClassName("cToExport");
		for(i=0;i<arInputs.length;i++)
		{
			if(arInputs[i].checked) return true;
		}
		return false;
	}
    function CheckServiceType(select, orderId)
    {
        price = document.getElementById("export_price_"+orderId);
        payedprice = document.getElementById("export_payed_price_"+orderId);
        if (select.value==1 || select.value==3)
        {
            payedprice.innerHTML = '<input type = "text" size = "8" name="EXPORT['+orderId+'][PAYED]" value="'+price.value+'"/>';
        }
        else
        {
            payedprice.innerHTML = '<?=GetMessage("PP_NO")?>';
        }
    }
</script>

<?if($_REQUEST["mess"] == "ok")
	CAdminMessage::ShowMessage(array("MESSAGE"=>GetMessage("PP_NEW_INVOICE"), "TYPE"=>"OK"));
if($_REQUEST["mess"] == "save")
	CAdminMessage::ShowMessage(array("MESSAGE"=>GetMessage("PP_SAVE_SETTINGS"), "TYPE"=>"OK"));
?>

<form method="post" action="<?=$APPLICATION->GetCurPage()?>" name="find_form">
	<?if($ex = $APPLICATION->GetException()) CAdminMessage::ShowOldStyleError($ex->GetString());?>
	<?if(strlen($_REQUEST["message"])>0) echo CAdminMessage::ShowNote($_REQUEST["message"]);?>

	<?$tabControl->Begin();?>
		<?$tabControl->BeginNextTab();?>
			<?$arItems = CPickpoint::GetOrdersArray();?>
			<?$arServiceTypes = (unserialize(COption::GetOptionString($iModuleID,"pp_service_types_all")));?>
			<?$arAllowedServiceTypes = unserialize(COption::GetOptionString($iModuleID,"pp_service_types_selected"));?>
			<?$arEnclosingTypes = (unserialize(COption::GetOptionString($iModuleID,"pp_enclosing_types_all")));?>
			<?$arAllowedEnclosingTypes = unserialize(COption::GetOptionString($iModuleID,"pp_enclosing_types_selected"));?>
    <tr><td>
			<table width="100%" class = "edit-table" id = "table_export">
				<tr class = "heading">
					<td><input type = "checkbox" id = "cSelectAll" onclick = "SelectAll(this)"/></td>
					<td><?=GetMessage("PP_ORDER_NUMBER")?></td>
                    <td><?=GetMessage("PP_INVOICE_ID")?></td>
					<td><?=GetMessage("PP_SUMM")?></td>
					<td><?=GetMessage("PP_PAYED_BY_PP")?></td>
					<td><?=GetMessage("PP_ADDRESS")?></td>
					<td><?=GetMessage("PP_SERVICE_TYPE")?></td>
					<td><?=GetMessage("PP_RECEPTION_TYPE")?></td>
<?/*					<td><?=GetMessage("PP_SIZE")?></td>               */?>

				</tr>
				<?foreach($arItems as $arItem):?>
					<?$arRequestItem = $_REQUEST["EXPORT"][$arItem["ORDER_ID"]];
                        $bActive = $arItem["INVOICE_ID"]?false:true;
                        /*
                        if ($arRequestItem["PAYED"])
                            $arItem["PAYED"] = $arRequestItem["PAYED"];
                        elseif($arItem["SETTINGS"]["PAYED"])
                            $arItem["PAYED"] = $arItem["SETTINGS"]["PAYED"];
                        else  */
                            $arItem["PAYED"] = $arItem["PRICE"];
                        $arItem["PAYED"] = number_format($arItem["PAYED"],2, ".", "");

                        if (in_array($arItem["SETTINGS"]["SERVICE_TYPE"], Array(1,3)))
                            $arItem["PAYED_PP_SET"] = 1;
                    ?>
					<tr>
						<td><input <?if(!$bActive):?> disabled="disabled"<?endif;?> type = "checkbox" <?=($arRequestItem["EXPORT"])?"checked":""?> class = "cToExport" name = "EXPORT[<?=$arItem["ORDER_ID"]?>][EXPORT]" autocomplete="off"/></td>
						<td><?=GetMessage("PP_N")?><?=$arItem["ORDER_ID"]?> <?=GetMessage("PP_FROM")?><br/><?=$arItem["ORDER_DATE"]?></td>
                        <td align="center"><?=$arItem["INVOICE_ID"]?$arItem["INVOICE_ID"]:""?></td>
						<td><?=CurrencyFormat($arItem["PRICE"],"RUB")?>
                            <?if($bActive):?><input type="hidden" id="export_price_<?=$arItem["ORDER_ID"]?>" value="<?=$arItem["PRICE"]?>" /><?endif;?></td>
						<td align="center" id="export_payed_price_<?=$arItem["ORDER_ID"]?>">
							<?if($arItem["PAYED_BY_PP"]||$arItem["PAYED_PP_SET"]):?>
                                <?/*if($bActive):?>
								    <input type = "text" size = "8" name = "EXPORT[<?=$arItem["ORDER_ID"]?>][PAYED]" value = "<?=$arItem["PAYED"]?>"/>
                                <?else:*/?>
                                    <?=$arItem["PAYED"]?>
                                <?//endif;?>
							<?else:?>
								<?=GetMessage("PP_NO")?>
							<?endif;?>
						</td>
						<td><?=$arItem["PP_ADDRESS"]?></td>
						<td>
                            <?/*
                            $serv_type = null;
                            if ($arRequestItem['SERVICE_TYPE'])
                            {
                                $serv_type = $arRequestItem['SERVICE_TYPE'];
                            }
                            elseif($arItem["SETTINGS"]["SERVICE_TYPE"])
                            {
                                $serv_type = $arItem["SETTINGS"]["SERVICE_TYPE"];
                            }
                            ?>
							<select <?if(!$bActive):?> disabled="disabled"<?endif;?> onchange="CheckServiceType(this, <?=$arItem["ORDER_ID"]?>)" name="EXPORT[<?=$arItem["ORDER_ID"]?>][SERVICE_TYPE]"/>
								<?foreach($arServiceTypes as $iKey=>$sServiceType):?>
									<?if(in_array($iKey,$arAllowedServiceTypes)):?>
										<?if(($arItem["PAYED_BY_PP"] && in_array($iKey,$arPayedServiceTypes)) || !$arItem["PAYED_BY_PP"]):?>
											<option <?=$serv_type==$iKey?"selected":""?> value = "<?=$iKey?>"><?=$sServiceType?></option>
										<?endif;?>
									<?endif;?>
								<?endforeach;?>
							</select>         */?>
                            <?if ($arItem["PAYED_BY_PP"]):?>
                                <?=$arServiceTypes[1]?>
                            <?else:?>
                                <?=$arServiceTypes[0]?>
                            <?endif?>
						</td>
						<td>
                            <?
                            $encl_type = null;
                            if ($arRequestItem['ENCLOSING_TYPE'])
                            {
                                $encl_type = $arRequestItem['ENCLOSING_TYPE'];
                            }
                            elseif($arItem["SETTINGS"]["ENCLOSING_TYPE"])
                            {
                                $encl_type = $arItem["SETTINGS"]["ENCLOSING_TYPE"];
                            }
                            ?>
							<select <?if(!$bActive):?> disabled="disabled"<?endif;?> name = "EXPORT[<?=$arItem["ORDER_ID"]?>][ENCLOSING_TYPE]"/>
								<?foreach($arEnclosingTypes as $iKey=>$sEnclosingType):?>
									<?if(in_array($iKey,$arAllowedEnclosingTypes)):?>
										<option <?=$encl_type==$iKey?"selected":""?> value = "<?=$iKey?>"><?=$sEnclosingType?></option>
									<?endif;?>
								<?endforeach;?>
							</select>
						</td>
<?/*						<td>
                            <?
                            $size = null;
                            if ($arRequestItem['SIZE'])
                            {
                                $size = $arRequestItem['SIZE'];
                            }
                            elseif($arItem["SETTINGS"]["SIZE"])
                            {
                                $size = $arItem["SETTINGS"]["SIZE"];
                            }
                            ?>
							<select <?if(!$bActive):?> disabled="disabled"<?endif;?> name = "EXPORT[<?=$arItem["ORDER_ID"]?>][SIZE]"/>
								<?foreach($arSizes as $iKey=>$arSize):?>
									<option <?if ($size==$iKey):?> selected="selected"<?endif?> value = "<?=$iKey?>"><?=$arSize["NAME"]?>: <?=$arSize["SIZE_X"]?>x<?=$arSize["SIZE_Y"]?>x<?=$arSize["SIZE_Z"]?></option>
								<?endforeach;?>
							</select>
						</td>     */?>

					</tr>
				<?endforeach;?>
			</table>

    </td>
    </tr>
		<?$tabControl->Buttons();?>
		<input type="submit" class="adm-btn-save" name="export" onclick = "return CheckFields();" value="<?echo GetMessage("PP_EXPORT")?>">
<?/*		<input type="submit"  name="save" value="<?echo GetMessage("PP_SAVE")?>">    */?>
	<?$tabControl->End();?>

<?
$tabControl->ShowWarnings("find_form", $message);
?>


<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");?>