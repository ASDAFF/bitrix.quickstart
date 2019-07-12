<?php
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) {
    die();
}

use \Bitrix\Main\Localization\Loc;
?>
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
					}
					else
					{
						BX("PAY_CURRENT_ACCOUNT").removeAttribute("checked");
					}
				}
			}
		}

		submitForm();
	}
</script>
<?php uasort($arResult["PAY_SYSTEM"], "cmpBySort"); ?>
<?php //var_dump($arParams["PAY_FROM_ACCOUNT"]); ?>
<table class="table table-order">
    <tbody>
        <?php if($arResult["PAY_FROM_ACCOUNT"] == "Y"): $accountOnly = ($arParams["ONLY_FULL_PAY_FROM_ACCOUNT"] == "Y") ? "Y" : "N"; ?>
        <tr onclick="changePaySystem('account');">
			<td class="gui-box  table-order__radio" style="width: auto">
				<label class="gui-checkbox" for="PAY_CURRENT_ACCOUNT">
					<input type="hidden" name="PAY_CURRENT_ACCOUNT" value="N">
                    <input class="gui-checkbox-input" type="checkbox" name="PAY_CURRENT_ACCOUNT" id="PAY_CURRENT_ACCOUNT" value="Y"<?if($arResult["USER_VALS"]["PAY_CURRENT_ACCOUNT"]=="Y") echo " checked=\"checked\"";?> onChange="submitForm()">
					<span class="gui-checkbox-icon"></span>
				</label>
            </td>
            <td colspan="2">
                <label for="PAY_CURRENT_ACCOUNT" style="font-weight: normal;cursor: pointer;">
                    <div><b><?=Loc::getMessage("SOA_TEMPL_PAY_ACCOUNT")?></b></div>
                    <div><?=Loc::getMessage("SOA_TEMPL_PAY_ACCOUNT1")?>: <b><?=$arResult["CURRENT_BUDGET_FORMATED"]?></b></div>
                    <?php if ($arParams["ONLY_FULL_PAY_FROM_ACCOUNT"] == "Y"): ?>
                        <div><?=Loc::getMessage("SOA_TEMPL_PAY_ACCOUNT3")?></div>
                    <?php else: ?>
                        <div><?=Loc::getMessage("SOA_TEMPL_PAY_ACCOUNT2")?></div>
                    <?php endif; ?>
                </label>
            </td>
        </tr>
        <?php endif; ?>
        <?php foreach($arResult["PAY_SYSTEM"] as $arPaySystem): ?>
        <tr onclick = "BX('ID_PAY_SYSTEM_ID_<?=$arPaySystem["ID"]?>').checked=true;changePaySystem();">
            <td class="gui-box table-order__radio">
                <label class="gui-radiobox" for="ID_PAY_SYSTEM_ID_<?=$arPaySystem["ID"]?>">
                    <input type="radio"
                        id="ID_PAY_SYSTEM_ID_<?=$arPaySystem["ID"]?>"
                        name="PAY_SYSTEM_ID"
                        class="gui-radiobox-item"
                        value="<?=$arPaySystem["ID"]?>"
                        <?php if ($arPaySystem["CHECKED"]=="Y" && !($arParams["ONLY_FULL_PAY_FROM_ACCOUNT"] == "Y" && $arResult["USER_VALS"]["PAY_CURRENT_ACCOUNT"]=="Y")) echo " checked=\"checked\"";?>
                        onclick="changePaySystem();"
                    ><span class="gui-out"><span class="gui-inside"></span></span>
                </label>
            </td>
            <td class="table-order__picture hidden-xs hidden-sm">
                <?php
                if (count($arPaySystem["PSA_LOGOTIP"]) > 0):
                    $imgUrl = $arPaySystem["PSA_LOGOTIP"]["SRC"];
                else:
                    $imgUrl = $arResult['NO_PHOTO'];
                endif;
                ?>
                <span class="table-order__img" style="background-image: url(<?=$imgUrl?>)"></span>
            </td>
            <td class="table-order__desc">
                <?php if ($arParams["SHOW_PAYMENT_SERVICES_NAMES"] != "N"): ?>
                    <b><?=$arPaySystem["PSA_NAME"];?></b>
                <?php endif; ?>
                <?php str_replace("#PAYSYSTEM_PRICE#", SaleFormatCurrency(roundEx($arPaySystem["PRICE"], SALE_VALUE_PRECISION), $arResult["BASE_LANG_CURRENCY"]), Loc::getMessage("SOA_TEMPL_PAYSYSTEM_PRICE")); ?>
                <p><?=$arPaySystem["DESCRIPTION"];?></p>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>