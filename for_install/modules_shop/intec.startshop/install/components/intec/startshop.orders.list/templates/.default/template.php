<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();?>
<?$this->setFrameMode(true);?>
<div class="startshop-orders-list default">
    <table width="100%" cellspacing="0" cellpadding="0" border="0" class="startshop-orders-list-table">
        <tr class="startshop-orders-list-table-row startshop-orders-list-table-row-filled">
            <td class="startshop-orders-list-table-row-column">
                <div class="startshop-orders-list-table-row-column-indents">
                    <?=GetMessage('SOL_DEFAULT_COLUMN_ID');?>
                </div>
            </td>
            <td class="startshop-orders-list-table-row-column">
                <div class="startshop-orders-list-table-row-column-indents">
                    <?=GetMessage('SOL_DEFAULT_COLUMN_DATE');?>
                </div>
            </td>
            <td class="startshop-orders-list-table-row-column">
                <div class="startshop-orders-list-table-row-column-indents">
                    <?=GetMessage('SOL_DEFAULT_COLUMN_STATUS');?>
                </div>
            </td>
            <td class="startshop-orders-list-table-row-column">
                <div class="startshop-orders-list-table-row-column-indents">
                    <?=GetMessage('SOL_DEFAULT_COLUMN_AMOUNT');?>
                </div>
            </td>
        </tr>
        <?$frame = $this->createFrame()->begin();?>
            <?if (!empty($arResult['ORDERS'])):?>
                <?foreach ($arResult['ORDERS'] as $iKey => $arOrder):?>
                    <tr class="startshop-orders-list-table-row<?=$iKey & 1 != 0 ? ' startshop-orders-table-row-filled' : ''?>">
                        <td class="startshop-orders-list-table-row-column">
                            <div class="startshop-orders-list-table-row-column-indents">
                                <?if (!empty($arOrder['ACTIONS']['VIEW'])):?>
                                    <a class="startshop-link startshop-link-standart" style="text-decoration: none;" href="<?=$arOrder['ACTIONS']['VIEW']?>"><?=$arOrder['ID']?></a>
                                <?else:?>
                                    <?=$arOrder['ID']?>
                                <?endif;?>
                            </div>
                        </td>
                        <td class="startshop-orders-list-table-row-column">
                            <div class="startshop-orders-list-table-row-column-indents">
                                <?=date('d.m.Y', strtotime($arOrder['DATE_CREATE']))?>
                            </div>
                        </td>
                        <td class="startshop-orders-list-table-row-column">
                            <div class="startshop-orders-list-table-row-column-indents">
                                <?if (!empty($arOrder['STATUS'])):?>
                                    <?=$arOrder['STATUS']['LANG'][LANGUAGE_ID]['NAME']?>
                                <?endif;?>
                            </div>
                        </td>
                        <td class="startshop-orders-list-table-row-column">
                            <div class="startshop-orders-list-table-row-column-indents">
                                <?=$arOrder['AMOUNT']['PRINT_VALUE']?>
                            </div>
                        </td>
                    </tr>
                <?endforeach;?>
            <?else:?>
                <tr class="startshop-orders-list-table-row">
                   <td colspan="4" class="startshop-orders-list-table-row-column">
                       <div class="startshop-orders-list-table-row-column-indents" style="text-align: center; font-weight: bold; margin: 10px 0px;">
                            <?=GetMessage('SOL_DEFAULT_NOTIFY_EMPTY')?>
                       </div>
                   </td>
                </tr>
            <?endif;?>
        <?$frame->end();?>
    </table>
</div>