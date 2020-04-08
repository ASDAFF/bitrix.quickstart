<?
global $APPLICATION;
?>
<div class="modal fade bs-example-modal-md change-table" tabindex="-1" role="dialog" id="change-columns-modal-<?=$result['ProductListConfig']['PAGE_ELEMENT_NAME']?>">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <button aria-label="Close" data-dismiss="modal" class="icon icon-close-bold close" type="button"></button>
                <h3 class="modal-title">Изменить столбцы</h3>
            </div>
            <div class="modal-body">
                <form action="<?=$APPLICATION->GetCurPageParam("", array("COUNT"))?>" method="GET" class="js-submit-to-set-cookie" data-submit="N" data-reload="Y" data-name="<?=$result['ProductListConfig']['PAGE_ELEMENT_NAME']?>">
                    <div class="form-group">
                        <?
                        foreach($result['ProductListConfig']['PAGE_ELEMENT_COL_OPTION_FULL'] as $key=>$val) {
                            ?>
                            <div class="checkbox">
                                <label><input type="checkbox" name="<?=$key?>"<?=$result['ProductListConfig']["PAGE_ELEMENT_COL_OPTION"][$key] ? ' checked="checked"' : ''?> value="1"><span><?=$val['NAME']?></span></label>
                            </div>
                            <?
                        }
                        ?>
                    </div>
                    <div class="form-footer">
                        <button class="btn" type="submit">Сохранить</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>