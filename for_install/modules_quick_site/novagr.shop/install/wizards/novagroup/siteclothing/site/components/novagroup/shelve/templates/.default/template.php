<?
if(in_array((int)$arParams['SHELVED_ITEM'],$arResult["SHELVED_ITEMS"]))
{
    $ADDED_SHELVE_PRODUCT = "display:none";
    $DELETED_SHELVE_PRODUCT = "display:block";
} else {
    $ADDED_SHELVE_PRODUCT = "display:block";
    $DELETED_SHELVE_PRODUCT = "display:none";
}
?>

<div id="SHELVE_PRODUCT" data-elem-id="<?=(int)$arParams['SHELVED_ITEM']?>">
    <div class="set">
        <div id="box-shelve" style="display: none;">
            <div class="message-demo added-success set-tool"><?= GetMessage("ADDED_TO_SHELVES") ?></div>
            <div class="message-demo added-error set-tool"><?= GetMessage("ERROR_ADDED_TO_SHELVES") ?></div>
            <div class="message-demo deleted-success set-tool"><?= GetMessage("DELETED_FROM_SHELVES") ?></div>
            <div class="message-demo deleted-error set-tool"><?= GetMessage("ERROR_DELETED_TO_SHELVES") ?></div>
        </div>
        <a href="#" style="<?=$ADDED_SHELVE_PRODUCT?>" class="DIV_SHELVE_PRODUCT ADDED_SHELVE_PRODUCT" data-action="addToShelve"
           data-elem-id=""><?= GetMessage("ADD_TO_SHELVES") ?></a>
        <a href="#" style="<?=$DELETED_SHELVE_PRODUCT?>" class="DIV_SHELVE_PRODUCT DELETED_SHELVE_PRODUCT" data-action="addToShelve"
           data-elem-id=""><?= GetMessage("DEL_FROM_SHELVES") ?></a>
    </div>
</div>
