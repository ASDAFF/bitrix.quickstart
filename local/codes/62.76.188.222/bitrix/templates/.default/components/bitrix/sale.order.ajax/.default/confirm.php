<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
if (!empty($arResult["ORDER"])) {
    ?>


    <section class="b-detail">
        <div class="b-detail-content">
            <h2 class="b-h2 m-checkout__h2">Заказ сформирован</h2>
            <p>Ваш заказ <b>№<?= $arResult["ORDER_ID"] ?></b> от <?= $arResult["ORDER"]["DATE_INSERT"]; ?> успешно создан.</p>
            <p>Вы можете следить за выполнением своего заказа в <a href="<?= $arParams["PATH_TO_PERSONAL"] ?>">Персональном разделе сайта</a>.<br>Обратите внимание, что для входа в этот раздел вам необходимо будет ввести логин и пароль пользователя сайта.</p>
            <p>Оплата заказа: <?= $arResult["PAY_SYSTEM"]["NAME"] ?></p>


            <?
            if (strlen($arResult["PAY_SYSTEM"]["ACTION_FILE"]) > 0) {
                ?>
                <tr>
                    <td>
        <?
        if ($arResult["PAY_SYSTEM"]["NEW_WINDOW"] == "Y") {
            ?>
                            <script language="JavaScript">
                                window.open('<?= $arParams["PATH_TO_PAYMENT"] ?>?ORDER_ID=<?= $arResult["ORDER_ID"] ?>');
                            </script>
            <?= GetMessage("SOA_TEMPL_PAY_LINK", Array("#LINK#" => $arParams["PATH_TO_PAYMENT"] . "?ORDER_ID=" . $arResult["ORDER_ID"])) ?>
            <?
        } else {
            if (strlen($arResult["PAY_SYSTEM"]["PATH_TO_ACTION"]) > 0) {
                include($arResult["PAY_SYSTEM"]["PATH_TO_ACTION"]);
            }
        }
        ?>
                    </td>
                </tr>
                        <?
                    }
                    ?>

    </div>
</section>
<?}?>