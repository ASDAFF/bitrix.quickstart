
<?
function getFileKrayt($name)
{
    if($name)
    {
        if(SITE_CHARSET == "windows-1251")
        {
            $data = file_get_contents('https://krayt.ru/upload/tp/'.$name);
            return iconv('UTF-8','windows-1251', $data);
        } else{
            return file_get_contents('https://krayt.ru/upload/tp/'.$name);
        }
    }
}
?>
<table class="gadgetholder" cellspacing="0" cellpadding="0" width="100%" id="GDHolder_<?=$arResult["ID"]?>">
    <tbody>
    <tr>
        <td width="50%">
            <div class="bx-gadgets">
                <div class="bx-gadgets-top-wrap">
                    <div class="bx-gadgets-top-center">
                        <div class="bx-gadgets-top-title"><?=GetMessage("K_INFO_DEV")?></div>
                    </div>
                </div>
                <div class="bx-gadgets-content">
                    <?= getFileKrayt('tp_info_company.php')?>
                </div>
            </div>
        </td>
        <td width="20">
            <div style="WIDTH: 20px"></div>
            <br>
        </td>
        <td width="50%">
            <div class="bx-gadgets">
                <div class="bx-gadgets-top-wrap">
                    <div class="bx-gadgets-top-center">
                        <div class="bx-gadgets-top-title"><?=GetMessage("K_INFO_MOD")?></div>
                    </div>
                </div>
                <div class="bx-gadgets-content">
                    <?
                    $k = count($arResult['modules']);
                    foreach($arResult['modules'] as $m):?>
                       <div class="item-m num_<?=$k?>">
                           <div>
                               <strong><?=GetMessage('K_NAME')?>:</strong>
                               <span><?=$m['MODULE_NAME']?> (<?=$m['MODULE_ID']?>) - (<?=$m['MODULE_VERSION']?>)</span>
                           </div>
                           <?if($arResult['newM'][$m['MODULE_ID']]['DATE_TO']):?>
                               <?
                               $date_to = MakeTimeStamp($arResult['newM'][$m['MODULE_ID']]['DATE_TO']);
                               

                               $lgot = false;
                               $all = false;
                               if($date_to > time())
                               {
                                   if(($date_to - time()) <= (60*60*24*30))
                                   {
                                       $lgot = true;
                                   }

                               }
                               if($date_to < time())
                               {
                                   if((time() - $date_to) <= (60*60*24*30))
                                   {
                                       $lgot = true;

                                   }else{
                                       $all = true;
                                   }

                               }


                               ?>
                           <?if((!$lgot) && (!$all)):?>
                               <div class="date-to">
                                   <strong><?=GetMessage('K_UPDATE')?>:</strong>
                                   <span>
                                       <?=GetMessage("K_DATE_TO")?>  - <b><?=$arResult['newM'][$m['MODULE_ID']]['DATE_TO']?>    </b>
                                   </span>
                               </div>
                           <?endif?>
                               <?if($lgot):?>
                                   <div class="date-to">
                                       <strong><?=GetMessage('K_UPDATE')?>:</strong>
                                   <span>
                                       <?=GetMessage("K_DATE_TO_CANCEL")?>  - <b><?=$arResult['newM'][$m['MODULE_ID']]['DATE_TO']?>    </b>
                                   </span>
                                   </div>
                                   <div class="date-to-error">
                                       <?=GetMessage('K_LGOT_UPDATE')?> <?= getFileKrayt('tp_form_btn.php')?>
                                   </div>
                               <?endif?>
                               <?if($all):?>
                                   <div class="date-to">
                                       <strong><?=GetMessage('K_UPDATE')?>:</strong>
                                   <span>
                                       <?=GetMessage("K_DATE_TO_CANCEL")?>  - <b><?=$arResult['newM'][$m['MODULE_ID']]['DATE_TO']?>    </b>
                                   </span>
                                   </div>
                                   <div class="date-to-error">
                                       <?=GetMessage('K_FULL_UPDATE')?> <?= getFileKrayt('tp_form_btn.php')?>
                                   </div>
                               <?endif?>
                           <?endif;?>
                       </div>
                    <?
                        $k--;
                    endforeach?>
                    <div>
                        <?= getFileKrayt('tp_form_market.php')?>
                    </div>
                </div>
            </div>
        </td>
    </tr>
    <tr>
        <td width="50%">
            <div class="bx-gadgets">
                <div class="bx-gadgets-top-wrap">
                    <div class="bx-gadgets-top-center">
                        <div class="bx-gadgets-top-title"><?=GetMessage("K_INFO_SITE")?></div>
                    </div>
                </div>
                <div class="bx-gadgets-content">
            <?
                    $bxProductConfig = array();
                    if(file_exists($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/.config.php"))
                    include($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/.config.php");

                    if(isset($bxProductConfig["admin"]["index"]))
                    $sProduct = $bxProductConfig["admin"]["index"];
                    else
                    $sProduct = GetMessage("GD_INFO_product").' &quot;'.GetMessage("GD_INFO_product_name_".COption::GetOptionString("main", "vendor", "1c_bitrix")).'#VERSION#&quot;';
                    $sVer = ($GLOBALS['USER']->CanDoOperation('view_other_settings')? " ".SM_VERSION : "");
                    $sProduct = str_replace("#VERSION#", $sVer, $sProduct);

                    ?><div class="bx-gadgets-info">
                        <div class="bx-gadgets-content-padding-rl bx-gadgets-content-padding-t" style="font-weight: bold; line-height: 28px;"><?=$sProduct;?></div>
                        <div style="margin: 0 1px 0 1px; border-bottom: 1px solid #D7E0E8;"></div>
                        <div class="bx-gadgets-content-padding-rl">
                            <table class="bx-gadgets-info-site-table">
                                <tr>
                                    <td align="left" valign="top" style="padding-bottom: 20px; line-height: 28px;"><span>
                                            <?
                                            $date_to = MakeTimeStamp($arResult['DATE_TO_SITE']);

                                            $lgot = false;
                                            $all = false;
                                            if($date_to > time())
                                            {
                                                if(($date_to - time()) <= (60*60*24*30))
                                                {
                                                    $lgot = true;
                                                }

                                            }
                                            if($date_to < time())
                                            {
                                                if((time() - $date_to) <= (60*60*24*30))
                                                {
                                                    $lgot = true;

                                                }else{
                                                    $all = true;
                                                }

                                            }
                                            ?>
                                            <?if((!$lgot) && (!$all)):?>
                                                <div class="date-to">
                                                    <strong><?=GetMessage('K_UPDATE')?>:</strong>
                                                   <span>
                                                       <?=GetMessage("K_DATE_TO")?>  - <b><?=$arResult['DATE_TO_SITE']?>    </b>
                                                   </span>
                                                                </div>
                                                <?endif?>
                                                <?if($lgot):?>
                                                <div class="date-to">
                                                    <strong><?=GetMessage('K_UPDATE')?>:</strong>
                                                   <span>
                                                       <?=GetMessage("K_DATE_TO_CANCEL")?>  - <b><?=$arResult['DATE_TO_SITE']?>    </b>
                                                   </span>
                                                        </div>
                                                        <div class="date-to-error">
                                                            <?=GetMessage('K_LGOT_UPDATE')?> <?= getFileKrayt('tp_form_site_btn.php')?>
                                                        </div>
                                                    <?endif?>
                                                            <?if($all):?>
                                                                <div class="date-to">
                                                                    <strong><?=GetMessage('K_UPDATE')?>:</strong>
                                                   <span>
                                                       <?=GetMessage("K_DATE_TO_CANCEL")?>  - <b><?=$arResult['DATE_TO_SITE']?>    </b>
                                                   </span>
                                                </div>
                                                <div class="date-to-error">
                                                    <?=GetMessage('K_FULL_UPDATE')?> <?= getFileKrayt('tp_form_site_btn.php')?>
                                                </div>
                                            <?endif?>
                                            <div>
                                                <?= getFileKrayt('tp_form_site_sale.php')?>
                                            </div>
                                            <div>
                                               <strong><?=GetMessage('K_LICENSE_SITE')?></strong> <?=$arResult['LICENSE_SITE']?>
                                            </div>
                                            <?
                                            if ($GLOBALS["USER"]->CanDoOperation('view_all_users'))
                                            {
                                                ?><div><?=str_replace("#VALUE#", CUser::GetCount(), GetMessage("GD_INFO_USERS"));?></div><?
                                            }
                                            ?></span></td>
                                    <td align="right" valign="bottom"><span style="display: inline-block; vertical-align: bottom; align: right;"><img src="/bitrix/gadgets/bitrix/admin_info/images/<?=(in_array(LANGUAGE_ID, array("ru", "en", "de"))?LANGUAGE_ID:"en")?>/logo.gif"></span></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </td>
        <td width="20">
            <div style="WIDTH: 20px"></div>
            <br>
        </td>
        <td width="50%">
            <div class="bx-gadgets">
                <div class="bx-gadgets-top-wrap">
                    <div class="bx-gadgets-top-center">
                        <div class="bx-gadgets-top-title"><?=GetMessage('K_ADD_TP_TITLE')?></div>
                    </div>
                </div>
                <div class="bx-gadgets-content">
                    <?= file_get_contents('https://krayt.ru/upload/tp/tp_form_tp.php')?>
                </div>
            </div>
        </td>
    </tbody>
</table>