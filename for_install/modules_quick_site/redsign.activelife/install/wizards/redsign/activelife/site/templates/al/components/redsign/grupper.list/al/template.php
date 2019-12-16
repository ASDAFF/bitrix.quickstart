<?php

use \Bitrix\Main\Localization\Loc;


if(!defined('B_PROLOG_INCLUDED')||B_PROLOG_INCLUDED!==true)die();

$this->setFrameMode(true);
?>

<?php if(is_array($arResult['GROUPED_ITEMS']) && count($arResult['GROUPED_ITEMS']) > 0): ?>
    <?php foreach($arResult['GROUPED_ITEMS'] as $arrValue): ?>
        <?php if (is_array($arrValue['PROPERTIES']) && count($arrValue['PROPERTIES']) > 0): ?>
            <div class="props_group">
                <div class="props_group__name"><?=$arrValue['GROUP']['NAME']?></div>
                <table class="props_group__props">
                    <tbody>
                    <?php foreach($arrValue['PROPERTIES'] as $key1 => $property): ?>
                        <tr>
                            <th><?=$property['NAME']?></th>
                            <td><?=(is_array($property['DISPLAY_VALUE']) ? implode(' / ', $property['DISPLAY_VALUE']) : $property['DISPLAY_VALUE'])?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    <?php endforeach; ?>
<?php endif; ?>
<?php if (is_array($arResult['NOT_GROUPED_ITEMS']) && count($arResult['NOT_GROUPED_ITEMS']) > 0): ?>
    <div class="props_group">
        <div class="props_group__name"><?=Loc::getMessage('RS_SLINE.RGL_AL.OTHER_PROPS')?></div>
        <table class="props_group__props">
            <tbody>
            <?php foreach($arResult['NOT_GROUPED_ITEMS'] as $property): ?>
                <tr>
                    <th><?=$property['NAME']?></th>
                    <td><?=(is_array($property['DISPLAY_VALUE']) ? implode(' / ', $property['DISPLAY_VALUE']) : $property['DISPLAY_VALUE'])?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>