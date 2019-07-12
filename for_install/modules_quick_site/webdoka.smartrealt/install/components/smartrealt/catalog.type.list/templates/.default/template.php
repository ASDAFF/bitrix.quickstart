<?php
/**
* ###################################
* # Copyright (c) 2012 SmartRealt   #
* # http://www.smartrealt.com       #
* # mailto:info@smartrealt.com      #
* ###################################
*/

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
?>
    <?php $i = 0; ?>
        <?php foreach ($arResult['arRubrics'] as $arRubric) { ?>
        <?php if ($i%2==0) { ?>
        <div class="col">
            <?php } ?>
            <b><?php echo $arRubric['Name'];?></b>
            <?php foreach ($arRubric['arElements'] as $arElement) { ?>
                <?php if ($arElement['Count'] > 0) { ?>
                    <a href="<?php echo $arElement['ListUrl'];?>"><?php echo $arElement['Name'];?></a><br>
                <?php } else { ?>
                    <?php echo $arElement['Name'];?><br>
                <?php } ?>
            <?php } ?>
            <?php if (($i+1)%2==0 || $i+1 == count($arResult['arRubrics'])) { ?>
        </div>
        <?php } ?>
    <?php $i++; } ?>         
