<?
    ######################################################
    # Name: energosoft.grouping                          #
    # File: template.php                                 #
    # (c) 2005-2012 Energosoft, Maksimov M.A.            #
    # Dual licensed under the MIT and GPL                #
    # http://energo-soft.ru/                             #
    # mailto:support@energo-soft.ru                      #
    ######################################################
?>

<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();?>
<script type="text/javascript">  
    $(function() {
            $(".baloon").click(function() {
                $(this).parent('.baloon_form').submit();
                  //  alert($(this).text());
            });
            return false;
    });
</script>   
<table cellspacing="0" cellpadding="0" border="0">

    <?foreach($arResult as $arElement):
            //echo "<pre>", print_r($arElement,1), "</pre>";?>
        <?if(count($arElement["PROPERTIES"]) > 0 || $arParams["ES_SHOW_EMPTY"] == "Y"):?>
            <tr style="background-color: lightgray;">
                <td><b><?=$arElement["NAME"]?></b></td>
                <td></td>
            </tr>
            <?foreach($arElement["PROPERTIES"] as $arProp):?>
                <?if($arProp["DISPLAY_VALUE"] != "" || $arParams["ES_SHOW_EMPTY_PROPERTY"] == "Y"):?>
                    <tr>
                        <td><?=$arProp["NAME"]?></td>
                        <td>
                            <?if ($arProp["PROPERTY_TYPE"] == "L"):?>
                                <form class="baloon_form" action="/catalogue/" method="post">
                                    <input type="hidden" name="PROPERTY_<?=$arProp["CODE"]?>" value="<?=$arProp["DISPLAY_VALUE"]?>">
                                    <a href="#" class="baloon"><?=$arProp["VALUE_ENUM"]==""?$arProp["DISPLAY_VALUE"]:$arProp["VALUE_ENUM"]?></a>
                                </form>
                                <?else:?>
                                <?=$arProp["VALUE_ENUM"]==""?$arProp["DISPLAY_VALUE"]:$arProp["VALUE_ENUM"]?>
                                <?endif;?>
                        </td>
                    </tr>
                    <?endif;?>
                <?endforeach;?>
            <?endif;?> 
        <?endforeach;?>

</table>