<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<form method="GET" action="<?= $arResult["CURRENT_PAGE"] ?>" name="bfilter">
<table class="sale-personal-order-list-filter data-table">
	<tr>
		<th colspan="2"><?echo GetMessage("SPOL_T_F_FILTER")?></th>
	</tr>
	<tr>
		<td><?=GetMessage("SPOL_T_F_ID");?>:</td>
		<td><input type="text" name="filter_id" value="<?=htmlspecialchars($_REQUEST["filter_id"])?>" size="10"></td>
	</tr>
	<tr>
		<td><?=GetMessage("SPOL_T_F_DATE");?>:</td>
		<td><?$APPLICATION->IncludeComponent(
	"bitrix:main.calendar",
	"",
	Array(
		"SHOW_INPUT" => "Y", 
		"FORM_NAME" => "bfilter", 
		"INPUT_NAME" => "filter_date_from", 
		"INPUT_NAME_FINISH" => "filter_date_to", 
		"INPUT_VALUE" => $_REQUEST["filter_date_from"], 
		"INPUT_VALUE_FINISH" => $_REQUEST["filter_date_to"], 
		"SHOW_TIME" => "N" 
	)
);?></td>
	</tr>
	<tr>
		<td><?=GetMessage("SPOL_T_F_STATUS")?>:</td>
		<td><select name="filter_status">
				<option value=""><?=GetMessage("SPOL_T_F_ALL")?></option>
				<?
				foreach($arResult["INFO"]["STATUS"] as $val)
				{
					if ($val["ID"]!="F")
					{
						?><option value="<?echo $val["ID"]?>"<?if($_REQUEST["filter_status"]==$val["ID"]) echo " selected"?>>[<?=$val["ID"]?>] <?=$val["NAME"]?></option><?
					}
				}
				?>
		</select></td>
	</tr>
	<tr>
		<td><?=GetMessage("SPOL_T_F_PAYED")?>:</td>
		<td><select name="filter_payed">
				<option value=""><?echo GetMessage("SPOL_T_F_ALL")?></option>
				<option value="Y"<?if ($_REQUEST["filter_payed"]=="Y") echo " selected"?>><?=GetMessage("SPOL_T_YES")?></option>
				<option value="N"<?if ($_REQUEST["filter_payed"]=="N") echo " selected"?>><?=GetMessage("SPOL_T_NO")?></option>
		</select></td>
	</tr>
	<tr>
		<td><?=GetMessage("SPOL_T_F_CANCELED")?>:</td>
		<td>
			<select name="filter_canceled">
				<option value=""><?=GetMessage("SPOL_T_F_ALL")?></option>
				<option value="Y"<?if ($_REQUEST["filter_canceled"]=="Y") echo " selected"?>><?=GetMessage("SPOL_T_YES")?></option>
				<option value="N"<?if ($_REQUEST["filter_canceled"]=="N") echo " selected"?>><?=GetMessage("SPOL_T_NO")?></option>
			</select>
		</td>
	</tr>
	<tr>
		<td><?=GetMessage("SPOL_T_F_HISTORY")?>:</td>
		<td>
			<select name="filter_history">
				<option value="N"<?if($_REQUEST["filter_history"]=="N") echo " selected"?>><?=GetMessage("SPOL_T_NO")?></option>
				<option value="Y"<?if($_REQUEST["filter_history"]=="Y") echo " selected"?>><?=GetMessage("SPOL_T_YES")?></option>
			</select>
		</td>
	</tr>
	<tr>
		<th colspan="2">
			<input type="submit" name="filter" value="<?=GetMessage("SPOL_T_F_SUBMIT")?>">&nbsp;&nbsp;
			<input type="submit" name="del_filter" value="<?=GetMessage("SPOL_T_F_DEL")?>">
		</th>
	</tr>
</table>
</form>
<br />
<?if(strlen($arResult["NAV_STRING"]) > 0):?>
	<p><?=$arResult["NAV_STRING"]?></p>
<?endif?>
<table class="sale-personal-order-list data-table">
	<tr>
		<th><?=GetMessage("SPOL_T_ID")?><br /><?=SortingEx("ID")?></th>
		<th><?=GetMessage("SPOL_T_PRICE")?><br /><?=SortingEx("PRICE")?></th>
		<th><?=GetMessage("SPOL_T_STATUS")?><br /><?=SortingEx("STATUS_ID")?></th>
		<th><?=GetMessage("SPOL_T_BASKET")?><br /></th>
		<th><?=GetMessage("SPOL_T_PAYED")?><br /><?=SortingEx("PAYED")?></th>
		<th><?=GetMessage("SPOL_T_CANCELED")?><br /><?=SortingEx("CANCELED")?></th>
		<th><?=GetMessage("SPOL_T_PAY_SYS")?><br /></th>
		<th><?=GetMessage("SPOL_T_ACTION")?></th>
	</tr>
	<?foreach($arResult["ORDERS"] as $val):?>
		<tr>
			<td><b><?=$val["ORDER"]["ID"]?></b><br /><?=GetMessage("SPOL_T_FROM")?> <?=$val["ORDER"]["DATE_INSERT_FORMAT"]?></td>
			<td><?=$val["ORDER"]["FORMATED_PRICE"]?></td>
			<td><?=$arResult["INFO"]["STATUS"][$val["ORDER"]["STATUS_ID"]]["NAME"]?><br /><?=$val["ORDER"]["DATE_STATUS"]?></td>
			<td><?
				$bNeedComa = False;
				foreach($val["BASKET_ITEMS"] as $vval)
				{
					?><li><?
					if (strlen($vval["DETAIL_PAGE_URL"])>0) 
						echo '<a href="'.$vval["DETAIL_PAGE_URL"].'">';
					echo $vval["NAME"];
					if (strlen($vval["DETAIL_PAGE_URL"])>0) 
						echo '</a>';
						echo ' - '.$vval["QUANTITY"].' '.GetMessage("STPOL_SHT");
					?></li><?
				}
			?></td>
			<td><?=(($val["ORDER"]["PAYED"]=="Y") ? GetMessage("SPOL_T_YES") : GetMessage("SPOL_T_NO"))?></td>
			<td><?=(($val["ORDER"]["CANCELED"]=="Y") ? GetMessage("SPOL_T_YES") : GetMessage("SPOL_T_NO"))?></td>
			<td>
				<?=$arResult["INFO"]["PAY_SYSTEM"][$val["ORDER"]["PAY_SYSTEM_ID"]]["NAME"]?> / 
				<?if (strpos($val["ORDER"]["DELIVERY_ID"], ":") === false):?>
					<?=$arResult["INFO"]["DELIVERY"][$val["ORDER"]["DELIVERY_ID"]]["NAME"]?>
				<?else:
					$arId = explode(":", $val["ORDER"]["DELIVERY_ID"]);
				?>
					<?=$arResult["INFO"]["DELIVERY_HANDLERS"][$arId[0]]["NAME"]?> (<?=$arResult["INFO"]["DELIVERY_HANDLERS"][$arId[0]]["PROFILES"][$arId[1]]["TITLE"]?>)
				<?endif?>
			</td>
			<td><a title="<?=GetMessage("SPOL_T_DETAIL_DESCR")?>" href="<?=$val["ORDER"]["URL_TO_DETAIL"]?>"><?=GetMessage("SPOL_T_DETAIL")?></a><br />
				<a title="<?=GetMessage("SPOL_T_COPY_ORDER_DESCR")?>" href="<?=$val["ORDER"]["URL_TO_COPY"]?>"><?=GetMessage("SPOL_T_COPY_ORDER")?></a><br />
<!-- UnitellerPlugin add -->
				<?if($val['ORDER']['NEED_CHECK'] == 'Y'):?>
					<a title="<?= GetMessage('SPOL_T_CHECK_DESCR'); ?>" href="<?= $val['ORDER']['URL_TO_CHECK']; ?>"><?= GetMessage('SPOL_T_CHECK'); ?></a><br />
				<?endif;?>
<!-- /UnitellerPlugin add -->
				<?if($val["ORDER"]["CAN_CANCEL"] == "Y"):?>
					<a title="<?=GetMessage("SPOL_T_DELETE_DESCR")?>" href="<?=$val["ORDER"]["URL_TO_CANCEL"]?>"><?=GetMessage("SPOL_T_DELETE")?></a>
				<?endif;?>
			</td>
		</tr>
	<?endforeach;?>
</table>
<?if(strlen($arResult["NAV_STRING"]) > 0):?>
	<p><?=$arResult["NAV_STRING"]?></p>
<?endif?>