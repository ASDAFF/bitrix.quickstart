<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?><?
CModule::IncludeModule('iblock');
CModule::IncludeModule('novagr.jwshop');
if($_REQUEST["filter_canceled"] == "Y" && $_REQUEST["filter_history"] == "Y")
	$page = "canceled";
elseif($_REQUEST["filter_status"] == "F" && $_REQUEST["filter_history"] == "Y")
	$page = "completed";
elseif($_REQUEST["filter_history"] == "Y")
	$page = "all";
else
	$page = "active";

$getVisibleProperties = NovagroupJewelryCart::getVisiblePropertyBySection('orders');

$baseUrl = $APPLICATION->GetCurPage();

/*
<ul id="myTab-i02" class="nav nav-tabs">
<li class="active"><a class="btn invested" href="#id-07" data-toggle="tab"><?=GetMessage("STPOL_F_ACTIVE")?></a></li>
<li><a class="btn invested" href="#id-08" data-toggle="tab"><?=GetMessage("STPOL_F_ALL")?></a></li>
<li><a class="btn invested" href="#id-09" data-toggle="tab"><?=GetMessage("STPOL_F_COMPLETED")?></a></li>
<li><a class="btn invested" href="#id-10" data-toggle="tab"><?=GetMessage("STPOL_F_CANCELED")?></a></li>
</ul>
*/
//deb( $_REQUEST["subtab"]);

if ($arParams["AJAX"] != 1) {
	
?>

<div class="invest-ed" id="ordersContainer">
<?php 
} // end $arParams["AJAX"] 
?>

	<ul id="myTab-i02" class="nav nav-tabs">
	<li <?=( $_REQUEST["subtab"] == 'id-07' || !$_REQUEST["subtab"]  ? ' class="active"' : '')?>><a id="link-id-07" class="btn invested" href="<?=$baseUrl."?filter_history=N&subtab=id-07"?>" data-params='{"filter_history":"N", "subtab":"id-07"}' ><?=GetMessage("STPOL_F_ACTIVE")?></a></li>
	<li <?=( $_REQUEST["subtab"] == 'id-08'   ? ' class="active"' : '')?>><a id="link-id-08" class="btn invested" href="<?=$baseUrl."?filter_history=Y&subtab=id-08"?>" data-params='{"filter_history":"Y", "subtab":"id-08"}' ><?=GetMessage("STPOL_F_ALL")?></a></li>
	<li <?=( $_REQUEST["subtab"] == 'id-09'   ? ' class="active"' : '')?>><a id="link-id-09" class="btn invested" href="<?=$baseUrl."?filter_status=F&filter_history=Y&subtab=id-09"?>" data-params='{"filter_status" : "F", "filter_history":"Y", "subtab":"id-09"}' ><?=GetMessage("STPOL_F_COMPLETED")?></a></li>
	<li <?=( $_REQUEST["subtab"] == 'id-10'   ? ' class="active"' : '')?>><a id="link-id-10" class="btn invested" href="<?=$baseUrl."?filter_canceled=Y&filter_history=Y&subtab=id-10"?>" data-params='{"filter_canceled" : "Y", "filter_history":"Y", "subtab":"id-10"}' ><?=GetMessage("STPOL_F_CANCELED")?></a></li>
	</ul>

	<div id="myTabContent-i02" class="tab-content">
		<div class="tab-pane fade active in" id="id-07">
       	<?php 
		$bNoOrder = true;
		foreach($arResult["ORDERS"] as $key => $val) {
			$bNoOrder = false;
			//deb($val);

			if ($val["ORDER"]["CANCELED"] == "Y") {
				// заказ отменен
				$tableClass = ' cancel';
			
			} elseif ($val["ORDER"]["STATUS_ID"] == "P") {
				// Оплачен, формируется к отправке
				$tableClass = ' formed';
				
			} elseif ($val["ORDER"]["STATUS_ID"] == "N") {
				// Принят, ожидается оплата
				$tableClass = ' expected';
				
			} elseif ($val["ORDER"]["STATUS_ID"] == "F") {
				// выполнен
				$tableClass = ' cancel';
				
			} 
			$orderId = $val["ORDER"]["ID"]; 
			
			?>
       		<table class="table table-bordered table-striped equipment<?=$tableClass?>">

				<thead>
					<tr>
					<td class="date-f">
						<span><?=GetMessage("STPOL_ORDER_NO")?><?=$val["ORDER"]["ID"] ?>&nbsp;<?=GetMessage("STPOL_FROM")?>&nbsp;<?=$val["ORDER"]["DATE_INSERT"]; ?></span>
					</td>
					<td class="tar fwn">
						<?php /*
						<a title="<?echo GetMessage("STPOL_DETAIL")?>" href="<?=$val["ORDER"]["URL_TO_DETAIL"] ?>"><?echo GetMessage("STPOL_DETAIL")?></a>*/?>
					</td>
					</tr>
				</thead>					
				<tbody>
				<tr>
				<td><strong><?echo GetMessage("STPOL_SUM")?></strong> <?=$val["ORDER"]["FORMATED_PRICE"]?></td>
				<td>
					<?if ($val["ORDER"]["CANCELED"] == "Y"):?>
						<?=$val["ORDER"]["DATE_CANCEL"]?>
					<?else:?>
						<?=$val["ORDER"]["DATE_STATUS"]?>
					<?endif;?>
				</td>
				</tr>
				<tr>
				<td>
					<strong><?=GetMessage("STPOL_PAYED")?></strong> <?echo (($val["ORDER"]["PAYED"]=="Y") ? GetMessage("STPOL_Y") : GetMessage("STPOL_N"));?>
				</td>
				<td class="order_status">
					<?if ($val["ORDER"]["CANCELED"] == "Y"):?>
						<strong><?=GetMessage("STPOL_CANCELED");?></strong>
					<?else:?>
						<strong><?=$arResult["INFO"]["STATUS"][$val["ORDER"]["STATUS_ID"]]["NAME"]?></strong>
					<?endif;?>
				</td>
				
				</tr>
				<tr>
				<td  class="compositionorder">
				<?if(IntVal($val["ORDER"]["PAY_SYSTEM_ID"])>0)
					echo "<strong>".GetMessage("P_PAY_SYS")."</strong> ".$arResult["INFO"]["PAY_SYSTEM"][$val["ORDER"]["PAY_SYSTEM_ID"]]["NAME"]."<br><br>"?>

				<?if(IntVal($val["ORDER"]["DELIVERY_ID"])>0)
				{
					echo "<strong>".GetMessage("P_DELIVERY")."</strong> ".$arResult["INFO"]["DELIVERY"][$val["ORDER"]["DELIVERY_ID"]]["NAME"]."<br><br>";
				}
				elseif (strpos($val["ORDER"]["DELIVERY_ID"], ":") !== false)
				{
					echo "<strong>".GetMessage("P_DELIVERY")."</strong> ";
					$arId = explode(":", $val["ORDER"]["DELIVERY_ID"]);
					echo $arResult["INFO"]["DELIVERY_HANDLERS"][$arId[0]]["NAME"]." (".$arResult["INFO"]["DELIVERY_HANDLERS"][$arId[0]]["PROFILES"][$arId[1]]["TITLE"].")"."<br><br>";
				}
				?>
					<strong><?echo GetMessage("STPOL_CONTENT")?></strong>
					<ul class="lsnn">
						<?
						foreach($val["BASKET_ITEMS"] as $vvval)
						{
							$vvval["DETAIL_PAGE_URL"] .= '?showoptions=all';
							?>
							<li>
								<?
								if (strlen($vvval["DETAIL_PAGE_URL"]) > 0)
									echo "<a href=\"".$vvval["DETAIL_PAGE_URL"]."\">";
								echo $vvval["NAME"];
								if (strlen($vvval["DETAIL_PAGE_URL"]) > 0)
									echo "</a>";
                                $OFFER_PROPERTIES = array();
								if($vvval["QUANTITY"] > 0)
									$OFFER_PROPERTIES[] = $vvval["QUANTITY"].GetMessage("STPOL_SHT");
                                    
                                $OFFER = GetIBlockElement($vvval['PRODUCT_ID']);
                                if(is_array($getVisibleProperties))
                                {
                                    foreach($getVisibleProperties as $property)
                                    {
                                        if(($PROPERTY_ID = $OFFER['PROPERTIES'][$property]['VALUE'])>0)
                                        {
                                            $OFFER_PROPERTY = GetIBlockElement($PROPERTY_ID);
                                            $OFFER_PROPERTIES[] = $OFFER['PROPERTIES'][$property]['NAME']." ".$OFFER_PROPERTY['NAME'];
                                        }
                                    }
                                }
                                if(count($OFFER_PROPERTIES)>0)echo " &mdash; <span style='text-transform:lowercase'>".implode(", ",$OFFER_PROPERTIES).'</span>';
								?>
							</li>
							<?
						}
						?>
					</ul>
				</td>
				<td >
					<?if ($val["ORDER"]["CAN_CANCEL"] == "Y"):
					// $val["ORDER"]["URL_TO_CANCEL"]
					?>
					
					<a class="btn bt2 db" title="<?= GetMessage("STPOL_CANCEL") ?>" href="<?=htmlspecialcharsbx($val['ORDER']['URL_TO_CANCEL'])?>"><?= GetMessage("STPOL_CANCEL") ?></a>
					<?endif;
					//var_dump($val);
					?><a class="btn bt2 db" title="<?= GetMessage("STPOL_REORDER") ?>" href="<?=htmlspecialcharsbx($val['ORDER']['URL_TO_COPY'])?>"><?= GetMessage("STPOL_REORDER1") ?></a>
                    <div class="individ">
                        <?
                        // врезка для кнопки оплаты через обработчик
                        $arFilter = Array(
                            "USER_ID" 	=> $USER -> GetID(),
                            "ID" 		=> $orderId,
                        );

                        $dbOrder = CSaleOrder::GetList(Array("ID" => "ASC"), $arFilter);
                        if($arOrder = $dbOrder -> Fetch())
                        {
                            if (IntVal($arOrder["PAY_SYSTEM_ID"]) > 0 and $val['ORDER']['PAYED']=='N' and $val['ORDER']['CANCELED']!=="Y")
                            {
                                $dbPaySysAction = CSalePaySystemAction::GetList(
                                    array(),
                                    array(
                                        "PAY_SYSTEM_ID" => $arOrder["PAY_SYSTEM_ID"],
                                        "PERSON_TYPE_ID" => $arOrder["PERSON_TYPE_ID"]
                                    ),
                                    false,
                                    false,
                                    array("NAME", "ACTION_FILE", "NEW_WINDOW", "PARAMS", "ENCODING")
                                );
                                if ($arPaySysAction = $dbPaySysAction->Fetch())
                                {
                                    if (strlen($arPaySysAction["ACTION_FILE"]) > 0)
                                    {
                                        $arResult["CAN_REPAY"] = "Y";
                                        if ($arPaySysAction["NEW_WINDOW"] == "Y")
                                        {
                                            ?>
                                            <a href="<?=htmlspecialcharsbx($arParams["PATH_TO_PAYMENT"]).'?ORDER_ID='.$orderId?>" target="_blank"><?=GetMessage("SALE_REPEAT_PAY")?></a>
                                        <?
                                        }else{
                                            CSalePaySystemAction::InitParamArrays($arOrder, $orderId, $arPaySysAction["PARAMS"]);

                                            $pathToAction = $_SERVER["DOCUMENT_ROOT"].$arPaySysAction["ACTION_FILE"];
                                            $pathToAction = str_replace("\\", "/", $pathToAction);
                                            while (substr($pathToAction, strlen($pathToAction) - 1, 1) == "/")
                                                $pathToAction = substr($pathToAction, 0, strlen($pathToAction) - 1);
                                            if (file_exists($pathToAction))
                                            {
                                                if (is_dir($pathToAction) && file_exists($pathToAction."/payment.php"))
                                                {
                                                    $ORDER_ID = $orderId;
                                                    $pathToAction .= "/payment.php";
                                                    include($pathToAction);
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                        ?>
                    </div>
				</td>
			</tr>
			
		</tbody>
		</table>
		<?php 
		}
		
		?>


    	</div>
	</div>              

<?php 

if ($bNoOrder)
{
	echo ShowNote(GetMessage("STPOL_NO_ORDERS_NEW"));
}

if(strlen($arResult["NAV_STRING"]) > 0):?>
	<div class="navigation"><?=$arResult["NAV_STRING"]?></div>
<?
endif;
	
if ($arParams["AJAX"] != 1) {	
	?>
	</div>
	<?php 
}
