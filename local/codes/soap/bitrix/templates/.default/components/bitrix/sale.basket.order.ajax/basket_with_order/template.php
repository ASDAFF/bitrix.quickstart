<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?
    if(!empty($arResult["ERROR"]))
    {
        foreach($arResult["ERROR"] as $v)
            echo ShowError($v);
    }
    elseif(!empty($arResult["OK_MESSAGE"]))
    {
        foreach($arResult["OK_MESSAGE"] as $v)
            echo ShowNote($v);
    }
?>
<div class="b-step-wrapper">
    <a href="#" class="b-step__link m-step__one active">Корзина</a>
    <span class="b-step__sep">&mdash;</span>
    <a href="#" class="b-step__link m-step__two">Информация  о покупателе</a>
    <span class="b-step__sep">&mdash;</span>
    <a href="#" class="b-step__link m-step__three">Доставка</a>
    <span class="b-step__sep">&mdash;</span>
    <a href="#" class="b-step__link m-step__four">Оплата</a>
</div>
<div class="b-cart__list">
    <NOSCRIPT>
        <div class="errortext"><?=GetMessage("SOA_NO_JS")?></div>
    </NOSCRIPT>
    <?
        if (strlen($arResult["ERROR_MESSAGE"]) > 0)
        {	
            ShowNote($arResult["ERROR_MESSAGE"]);
        }

        if (isset($arResult["ORDER_BUSKET"]["CONFIRM_ORDER"]) && $arResult["ORDER_BUSKET"]["CONFIRM_ORDER"] == "Y")
        {
            include($_SERVER["DOCUMENT_ROOT"].$templateFolder."/basket_confirm.php");
        }
        else
        {
            $arUrlTempl = Array(
                "delete" => $APPLICATION->GetCurPage()."?action=delete&id=#ID#",
                "shelve" => $APPLICATION->GetCurPage()."?action=shelve&id=#ID#",
                "add" => $APPLICATION->GetCurPage()."?action=add&id=#ID#",
            );
        ?>
        <div style="display:none;">
            <div id="order_form_id">
                <input type="hidden" name="form" value="Y" />
                <?=bitrix_sessid_post()?>
                <? include($_SERVER["DOCUMENT_ROOT"].$templateFolder."/basket_items.php");?>
                <? //include($_SERVER["DOCUMENT_ROOT"].$templateFolder."/basket_items_delay.php"); ?>
                <? include($_SERVER["DOCUMENT_ROOT"].$templateFolder."/basket_items_notavail.php"); ?>
                <? //include($_SERVER["DOCUMENT_ROOT"].$templateFolder."/basket_items_subscribe.php"); ?>
                <?
                    if(count($arResult["ITEMS"]["AnDelCanBuy"]) > 0):

                        $display = "none";
                        if ($arParams['SHOW_BUSKET_ORDER'] == 'Y')
                            $display = "block";
                        if (isset($_POST["display_props"]) && strlen($_POST["display_props"]) > 0)
                            $display = htmlspecialcharsbx($_POST["display_props"]);
                    ?>
                    <div id="delay_none" style="display:block">
                        <input type="hidden" name="display_props" id="display_props" value="<?=$display?>" />
                        <input type="hidden" name="display_user" id="display_user" value="<?=$display?>" />

                        <div id="order_user" style="display:<?=$display?>">
                            <? include($_SERVER["DOCUMENT_ROOT"].$templateFolder."/basket_user.php");?>
                            <? include($_SERVER["DOCUMENT_ROOT"].$templateFolder."/basket_person_type.php");?>
                        </div>

                        <div id="order_props" style="display:none;">
                            <?// include($_SERVER["DOCUMENT_ROOT"].$templateFolder."/basket_props.php");?>
                            <? include($_SERVER["DOCUMENT_ROOT"].$templateFolder."/basket_delivery.php");?>

                            <div class="order_submit"><input type="submit" value="<?=GetMessage("SALE_ORDER")?>" name="BasketOrder" id="basketOrderButton2" ></div>
                        </div>
                    </div>
                    <?endif;?>
            </div>
        </div>

        <div id="form_new"></div>

        <script>
            <!--
            function ShowBasketItems(val)
            {
                if(val == 4)
                    {
                    if(document.getElementById("id-cart-list"))
                        document.getElementById("id-cart-list").style.display = 'none';
                    if(document.getElementById("id-shelve-list"))
                        document.getElementById("id-shelve-list").style.display = 'none';
                    if(document.getElementById("id-sub-list"))
                        document.getElementById("id-sub-list").style.display = 'none';
                    if(document.getElementById("id-noactive-list"))
                        document.getElementById("id-noactive-list").style.display = 'block';

                    if (document.getElementById("delay_none"))
                        document.getElementById("delay_none").style.display = 'none';
                }
                if(val == 3)
                    {
                    if(document.getElementById("id-cart-list"))
                        document.getElementById("id-cart-list").style.display = 'none';
                    if(document.getElementById("id-shelve-list"))
                        document.getElementById("id-shelve-list").style.display = 'none';
                    if(document.getElementById("id-noactive-list"))
                        document.getElementById("id-noactive-list").style.display = 'none';
                    if(document.getElementById("id-sub-list"))
                        document.getElementById("id-sub-list").style.display = 'block';
                    if (document.getElementById("delay_none"))
                        document.getElementById("delay_none").style.display = 'none';
                }
                if(val == 2)
                    {
                    if(document.getElementById("id-cart-list"))
                        document.getElementById("id-cart-list").style.display = 'none';
                    if(document.getElementById("id-sub-list"))
                        document.getElementById("id-sub-list").style.display = 'none';
                    if(document.getElementById("id-noactive-list"))
                        document.getElementById("id-noactive-list").style.display = 'none';
                    if(document.getElementById("id-shelve-list"))
                        document.getElementById("id-shelve-list").style.display = 'block';
                    if (document.getElementById("delay_none"))
                        document.getElementById("delay_none").style.display = 'none';
                }
                else if(val == 1)
                    {
                    if(document.getElementById("id-cart-list"))
                        document.getElementById("id-cart-list").style.display = 'block';
                    if(document.getElementById("id-sub-list"))
                        document.getElementById("id-sub-list").style.display = 'none';
                    if(document.getElementById("id-noactive-list"))
                        document.getElementById("id-noactive-list").style.display = 'none';
                    if(document.getElementById("id-shelve-list"))
                        document.getElementById("id-shelve-list").style.display = 'none';
                    if (document.getElementById("delay_none"))	
                        document.getElementById("delay_none").style.display = 'block';
                }
            }
            function submitForm()
            {
                var orderForm = document.getElementById('ORDER_FORM_ID_NEW');

                jsAjaxUtil.InsertFormDataToNode(orderForm, 'order_form_div', true);
                orderForm.submit();

                return true;
            }
            function ShowUser()
            {
                if (BX('order_user').style.display == "block")
                    BX('order_user').style.display = "none";
                else
                    BX('order_user').style.display = "block";

                BX('display_user').value = BX('order_user').style.display;

                return false;
            }
                        function ShowOrder()
            {
                if (BX('order_props').style.display == "block")
                    BX('order_user').style.display = "none";
                else
                    BX('order_props').style.display = "block";
                    BX('order_user').style.display = "none";

                BX('display_props').value = BX('order_props').style.display;

                return false;
            }

            var newform = document.createElement("FORM");
            newform.method = "POST";
            newform.action = "";
            newform.name = "<?=$FORM_NAME?>";
            newform.id = "ORDER_FORM_ID_NEW";
            var im = document.getElementById('order_form_id');
            document.getElementById("form_new").appendChild(newform);
            newform.appendChild(im);

            //-->
        </script>
        <?
        }
    ?>
</div>