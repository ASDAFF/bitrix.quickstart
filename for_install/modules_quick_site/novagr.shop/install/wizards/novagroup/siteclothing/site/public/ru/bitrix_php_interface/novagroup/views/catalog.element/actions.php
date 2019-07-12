<div class="label-card">
    <?$APPLICATION->IncludeComponent(
        "novagroup:catalog.timetobuy",
        "label",
        Array(
            "IBLOCK_ID"=>$val['IBLOCK_ID'],
            "ID"=>$val['ID']
        )
    );?>
    <?
    if( !empty($val['PROPERTIES']['SPECIALOFFER']['VALUE']) )
        echo'<div class="card-spec-min"></div>';
    if( !empty($val['PROPERTIES']['NEWPRODUCT']['VALUE']) )
        echo'<div class="card-new-min"></div>';
    if( !empty($val['PROPERTIES']['SALELEADER']['VALUE']) )
        echo'<div class="card-lider-min"></div>';
    ?>
</div>