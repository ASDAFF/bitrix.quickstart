<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
    <tr>
        <td width="25%" align="right" valign="top"><span class="sof-req">*</span><?=GetMessage("SOA_PAYSYSTEM")?>:</td>
        <td>
            <select name="PAYSYSTEM_ID" id="PAYSYSTEM_ID" onChange="submitForm();">
                <?
                foreach($arResult["PAYSYSTEM"] as $val)
                {
                    ?>
                    <option value="<?=$val["ID"]?>"<?if ($val["CHECKED"]=="Y") echo " selected";?>><?=$val["NAME"]?></option>
                    <?
                }
                ?>
            </select>
            <div class="desc"><?=$arResult["PAYSYSTEM_CHECKED_DESC"]?></div>
        </td>
    </tr>