<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<div>
	<script>
		function changePaySystem(param)
		{
			if (BX("account_only") && BX("account_only").value == 'Y') // PAY_CURRENT_ACCOUNT checkbox should act as radio
			{
				if (param == 'account')
				{
					if (BX("PAY_CURRENT_ACCOUNT"))
					{
						BX("PAY_CURRENT_ACCOUNT").checked = true;
						BX("PAY_CURRENT_ACCOUNT").setAttribute("checked", "checked");
						BX.addClass(BX("PAY_CURRENT_ACCOUNT_LABEL"), 'selected');

						// deselect all other
						var el = document.getElementsByName("PAY_SYSTEM_ID");
						for(var i=0; i<el.length; i++)
							el[i].checked = false;
					}
				}
				else
				{
					BX("PAY_CURRENT_ACCOUNT").checked = false;
					BX("PAY_CURRENT_ACCOUNT").removeAttribute("checked");
					BX.removeClass(BX("PAY_CURRENT_ACCOUNT_LABEL"), 'selected');
				}
			}
			else if (BX("account_only") && BX("account_only").value == 'N')
			{
				if (param == 'account')
				{
					if (BX("PAY_CURRENT_ACCOUNT"))
					{
						BX("PAY_CURRENT_ACCOUNT").checked = !BX("PAY_CURRENT_ACCOUNT").checked;

						if (BX("PAY_CURRENT_ACCOUNT").checked)
						{
							BX("PAY_CURRENT_ACCOUNT").setAttribute("checked", "checked");
							BX.addClass(BX("PAY_CURRENT_ACCOUNT_LABEL"), 'selected');
						}
						else
						{
							BX("PAY_CURRENT_ACCOUNT").removeAttribute("checked");
							BX.removeClass(BX("PAY_CURRENT_ACCOUNT_LABEL"), 'selected');
						}
					}
				}
			}

			submitForm();
		}
	</script>
	<div class="info_order_item">
		<?
		if(!empty($arResult["PAY_SYSTEM"]) && is_array($arResult["PAY_SYSTEM"]) || $arResult["PAY_FROM_ACCOUNT"] == "Y")
		{
            ?>
            <div class="main_order_block__top_line">
                <span class="main_order_block__title fonts__main_text"><?=GetMessage("SOA_TEMPL_PAY_SYSTEM")?></span>
            </div>
            <?
		}
		if($arResult["PAY_FROM_ACCOUNT"] == "Y")
		{
			$accountOnly = ($arParams["ONLY_FULL_PAY_FROM_ACCOUNT"] == "Y") ? "Y" : "N";
			?>
			<input type="hidden" id="account_only" value="<?=$accountOnly?>">
            <div class="radio_container">
                <input type="hidden" name="PAY_CURRENT_ACCOUNT" value="N">
                <label for="PAY_CURRENT_ACCOUNT" id="PAY_CURRENT_ACCOUNT_LABEL" onclick="changePaySystem('account');" class="<?if($arResult["USER_VALS"]["PAY_CURRENT_ACCOUNT"]=="Y") echo "selected"?> radio-label fonts__main_comment">
                    <input type="checkbox" name="PAY_CURRENT_ACCOUNT" id="PAY_CURRENT_ACCOUNT" value="Y"<?if($arResult["USER_VALS"]["PAY_CURRENT_ACCOUNT"]=="Y") echo " checked=\"checked\"";?>>
                    <!-- <div class="bx_logotype"><span style="background-image:url(<?=$templateFolder?>/images/logo-default-ps.gif);"></span></div> -->
                    <div>
                        <span class="radio-label_title fonts__main_comment"><?=GetMessage("SOA_TEMPL_PAY_ACCOUNT")?></span>
                        <span class="radio_container_comment fonts__main_comment">
                            <div><?=GetMessage("SOA_TEMPL_PAY_ACCOUNT1")." <b>".$arResult["CURRENT_BUDGET_FORMATED"]?></b></div>
                            <? if ($arParams["ONLY_FULL_PAY_FROM_ACCOUNT"] == "Y"):?>
                                <div><?=GetMessage("SOA_TEMPL_PAY_ACCOUNT3")?></div>
                            <? else:?>
                                <div><?=GetMessage("SOA_TEMPL_PAY_ACCOUNT2")?></div>
                            <? endif;?>
                        </span>
                    </div>
                </label>
            </div>
			<?
		}

        uasort($arResult["PAY_SYSTEM"], "cmpBySort"); // resort arrays according to SORT value
        ?>

        <div class="info_order_item_content">
            <?
            foreach($arResult["PAY_SYSTEM"] as $arPaySystem)
            {
                if(strlen(trim(str_replace("<br />", "", $arPaySystem["DESCRIPTION"]))) > 0 || intval($arPaySystem["PRICE"]) > 0)
                {
                    if(count($arResult["PAY_SYSTEM"]) == 1)
                    {
                        ?>
                        <div class="radio_container">
                            <input type="hidden" name="PAY_SYSTEM_ID" value="<?=$arPaySystem["ID"]?>">
                            <input type="radio"
                                id="ID_PAY_SYSTEM_ID_<?=$arPaySystem["ID"]?>"
                                name="PAY_SYSTEM_ID"
                                value="<?=$arPaySystem["ID"]?>"
                                <?if($arPaySystem["CHECKED"]=="Y" && !($arParams["ONLY_FULL_PAY_FROM_ACCOUNT"] == "Y" && $arResult["USER_VALS"]["PAY_CURRENT_ACCOUNT"]=="Y")) echo " checked=\"checked\"";?>
                                onclick="changePaySystem();"
                            >
                            <label for="ID_PAY_SYSTEM_ID_<?=$arPaySystem["ID"]?>" onclick="BX('ID_PAY_SYSTEM_ID_<?=$arPaySystem['ID']?>').checked=true;changePaySystem();" class="radio-label fonts__main_comment">
                                <?/*
                                if(count($arPaySystem["PSA_LOGOTIP"]) > 0):
                                    $arFileTmp = CFile::ResizeImageGet(
                                        $arPaySystem["PSA_LOGOTIP"]['ID'],
                                        array("width" => "95", "height" =>"55"),
                                        BX_RESIZE_IMAGE_PROPORTIONAL,
                                        true
                                    );
                                    $imgUrl = $arFileTmp["src"];
                                else:
                                    $imgUrl = $templateFolder."/images/logo-default-ps.gif";
                                endif;
                                */?>
                                <!-- <div class="bx_logotype"><span style="background-image:url(<?=$imgUrl?>);"></span></div> -->
                                <div>
                                    <?if($arParams["SHOW_PAYMENT_SERVICES_NAMES"] != "N"):?>
                                        <span class="radio-label_title fonts__main_comment"><?=$arPaySystem["PSA_NAME"];?></span>
                                    <?endif;?>
                                    <span class="radio_container_comment fonts__main_comment">
                                        <?
                                        if(intval($arPaySystem["PRICE"]) > 0)
                                            echo str_replace("#PAYSYSTEM_PRICE#", SaleFormatCurrency(roundEx($arPaySystem["PRICE"], SALE_VALUE_PRECISION), $arResult["BASE_LANG_CURRENCY"]), GetMessage("SOA_TEMPL_PAYSYSTEM_PRICE"));
                                        else
                                            echo $arPaySystem["DESCRIPTION"];
                                        ?>
                                    </span>
                                </div>
                            </label>
                        </div>
                        <?
                    }
                    else // more than one
                    {
                        ?>
                        <div class="radio_container">
                            <input type="radio"
                                id="ID_PAY_SYSTEM_ID_<?=$arPaySystem["ID"]?>"
                                name="PAY_SYSTEM_ID"
                                value="<?=$arPaySystem["ID"]?>"
                                <?if ($arPaySystem["CHECKED"]=="Y" && !($arParams["ONLY_FULL_PAY_FROM_ACCOUNT"] == "Y" && $arResult["USER_VALS"]["PAY_CURRENT_ACCOUNT"]=="Y")) echo " checked=\"checked\"";?>
                                onclick="changePaySystem();"
                            >
                            <label for="ID_PAY_SYSTEM_ID_<?=$arPaySystem["ID"]?>" onclick="BX('ID_PAY_SYSTEM_ID_<?=$arPaySystem['ID']?>').checked=true;changePaySystem();" class="radio-label fonts__main_comment">
                                <?/*
                                if(count($arPaySystem["PSA_LOGOTIP"]) > 0):
                                    $arFileTmp = CFile::ResizeImageGet(
                                        $arPaySystem["PSA_LOGOTIP"]['ID'],
                                        array("width" => "95", "height" =>"55"),
                                        BX_RESIZE_IMAGE_PROPORTIONAL,
                                        true
                                    );
                                    $imgUrl = $arFileTmp["src"];
                                else:
                                    $imgUrl = $templateFolder."/images/logo-default-ps.gif";
                                endif;
                                */?>
                                <!-- <div class="bx_logotype"><span style='background-image:url(<?=$imgUrl?>);'></span></div> -->
                                <div>
                                    <?if($arParams["SHOW_PAYMENT_SERVICES_NAMES"] != "N"):?>
                                        <span class="radio-label_title fonts__main_comment"><?=$arPaySystem["PSA_NAME"];?></span>
                                    <?endif;?>
                                    <span class="radio_container_comment fonts__main_comment">
                                        <?
                                        if(intval($arPaySystem["PRICE"]) > 0)
                                            echo str_replace("#PAYSYSTEM_PRICE#", SaleFormatCurrency(roundEx($arPaySystem["PRICE"], SALE_VALUE_PRECISION), $arResult["BASE_LANG_CURRENCY"]), GetMessage("SOA_TEMPL_PAYSYSTEM_PRICE"));
                                        else
                                            echo $arPaySystem["DESCRIPTION"];
                                        ?>
                                    </span>
                                </div>
                            </label>
                        </div>
                        <?
                    }
                }

                if(strlen(trim(str_replace("<br />", "", $arPaySystem["DESCRIPTION"]))) == 0 && intval($arPaySystem["PRICE"]) == 0)
                {
                    if(count($arResult["PAY_SYSTEM"]) == 1)
                    {
                        ?>
                        <div class="radio_container">
                            <input type="hidden" name="PAY_SYSTEM_ID" value="<?=$arPaySystem["ID"]?>">
                            <input type="radio"
                                id="ID_PAY_SYSTEM_ID_<?=$arPaySystem["ID"]?>"
                                name="PAY_SYSTEM_ID"
                                value="<?=$arPaySystem["ID"]?>"
                                <?if ($arPaySystem["CHECKED"]=="Y" && !($arParams["ONLY_FULL_PAY_FROM_ACCOUNT"] == "Y" && $arResult["USER_VALS"]["PAY_CURRENT_ACCOUNT"]=="Y")) echo " checked=\"checked\"";?>
                                onclick="changePaySystem();"
                            >
                            <label for="ID_PAY_SYSTEM_ID_<?=$arPaySystem["ID"]?>" onclick="BX('ID_PAY_SYSTEM_ID_<?=$arPaySystem['ID']?>').checked=true;changePaySystem();" class="radio-label fonts__main_comment">
                            <?/*
                            if(count($arPaySystem["PSA_LOGOTIP"]) > 0):
                                $arFileTmp = CFile::ResizeImageGet(
                                    $arPaySystem["PSA_LOGOTIP"]['ID'],
                                    array("width" => "95", "height" =>"55"),
                                    BX_RESIZE_IMAGE_PROPORTIONAL,
                                    true
                                );
                                $imgUrl = $arFileTmp["src"];
                            else:
                                $imgUrl = $templateFolder."/images/logo-default-ps.gif";
                            endif;
                            */?>
                            <!-- <div class="bx_logotype"><span style='background-image:url(<?=$imgUrl?>);'></span></div> -->
                            <?if($arParams["SHOW_PAYMENT_SERVICES_NAMES"] != "N"):?>
                                <div>
                                    <span class="radio-label_title fonts__main_comment"><?=$arPaySystem["PSA_NAME"];?></span>
                                </div>
                            <?endif;?>
                            </label>
                        </div>
                        <?
                    }
                    else // more than one
                    {
                        ?>
                        <div class="radio_container">
                            <input type="radio"
                                id="ID_PAY_SYSTEM_ID_<?=$arPaySystem["ID"]?>"
                                name="PAY_SYSTEM_ID"
                                value="<?=$arPaySystem["ID"]?>"
                                <?if ($arPaySystem["CHECKED"]=="Y" && !($arParams["ONLY_FULL_PAY_FROM_ACCOUNT"] == "Y" && $arResult["USER_VALS"]["PAY_CURRENT_ACCOUNT"]=="Y")) echo " checked=\"checked\"";?>
                                onclick="changePaySystem();"
                            >
                            <label for="ID_PAY_SYSTEM_ID_<?=$arPaySystem["ID"]?>" onclick="BX('ID_PAY_SYSTEM_ID_<?=$arPaySystem['ID']?>').checked=true;changePaySystem();" class="radio-label fonts__main_comment">
                                <?/*
                                if(count($arPaySystem["PSA_LOGOTIP"]) > 0):
                                    $arFileTmp = CFile::ResizeImageGet(
                                        $arPaySystem["PSA_LOGOTIP"]['ID'],
                                        array("width" => "95", "height" =>"55"),
                                        BX_RESIZE_IMAGE_PROPORTIONAL,
                                        true
                                    );
                                    $imgUrl = $arFileTmp["src"];
                                else:
                                    $imgUrl = $templateFolder."/images/logo-default-ps.gif";
                                endif;
                                */?>
                                <!-- <div class="bx_logotype"><span style='background-image:url(<?=$imgUrl?>);'></span></div> -->
                                <?if($arParams["SHOW_PAYMENT_SERVICES_NAMES"] != "N"):?>
                                    <div>
                                        <span class="radio-label_title fonts__main_comment">
                                            <?if($arParams["SHOW_PAYMENT_SERVICES_NAMES"] != "N"):?>
                                                <?=$arPaySystem["PSA_NAME"];?>
                                            <?else:?>
                                                <?="&nbsp;"?>
                                            <?endif;?>
                                        </span>
                                    </div>
                                <?endif;?>
                            </label>
                        </div>
                        <?
                    }
                }
            }
            ?>
        </div>
	</div>
</div>
