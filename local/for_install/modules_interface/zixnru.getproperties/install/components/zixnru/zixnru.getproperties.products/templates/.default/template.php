<?php if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die(); ?>
<?php
/*
 * Code is distributed as-is
 * the Developer may change the code at its discretion without prior notice
 * Developers: Djo 
 * Website: http://zixn.ru
 * Twitter: https://twitter.com/Zixnru
 * Email: izm@zixn.ru
 */
//echo '<pre>';
//print_r($arResult);
//echo '</pre>';
?>
<!-- Свойства -->
<div class="zixnPropertyList">
    <div class="harakt">
        <table class="haraktAll">
            <?php if (!empty($arResult)) { ?>
                <? foreach ($arResult as $propert): ?>
                    <? if ($propert['PROPERTY_TYPE'] == 'L') { ?>
                        <tr>

                            <td class="name"><? echo $propert["NAME"]; ?></td>
                            <td class="value"><? echo $propert["VALUE_ENUM"]; ?></td>

                        </tr>
                    <? } ?>

                <? endforeach; ?>
            <?php } else { ?>
                <tr>
                    <td class="name"><? echo GetMessage("NOT_PROPERTY"); ?></td>
                </tr>
            <?php } ?>
        </table>
    </div>
</div>
