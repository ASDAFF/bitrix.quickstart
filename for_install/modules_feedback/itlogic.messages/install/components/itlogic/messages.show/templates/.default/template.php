<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<div class="mail-show-content">
    <?
    if( $arResult['itemsCount'] > 0 ){
    ?>
    <table class="border1px" width="100%">
        <tr>
            <th><?=GetMessage("ITLOGIC_MESSAGES_KOD_SABLONA")?></th>
            <th class="c1"><?=GetMessage("ITLOGIC_MESSAGES_DATA_USTANOVKI")?><br/><?=GetMessage("ITLOGIC_MESSAGES_V_OCEREDQ_OTPRAVKI")?></th>
            <th><?=GetMessage("ITLOGIC_MESSAGES_OTPRAVLENO")?></th>
            <th class="c3"><?=GetMessage("ITLOGIC_MESSAGES_DATA_OTPRAVKI")?></th>
            <th><?=GetMessage("ITLOGIC_MESSAGES_SODERJANIE_SOOBSENIA")?></th>
        </tr>
        <?foreach($arResult['ITEMS'] as $key => $item):?>
            <tr class="color-<?=$item['COLOR']?>">
                <td><?=$item['EVENT_NAME']?></td>
                <td><?=$item['DATE_INSERT']?></td>
                <td><?=$item['SUCCESS_EXEC']?></td>
                <td><?=$item['DATE_EXEC']?></td>
                <td><div class="overflow"><?=$item['MESS']?></div></td>
            </tr>
        <?endforeach;?>
    </table>
    <?
    }else{
        ?><p><b><?=GetMessage("ITLOGIC_MESSAGES_SOOBSENIY_NE_NAYDENO")?></b></p><?
    }
    ?>
</div>
