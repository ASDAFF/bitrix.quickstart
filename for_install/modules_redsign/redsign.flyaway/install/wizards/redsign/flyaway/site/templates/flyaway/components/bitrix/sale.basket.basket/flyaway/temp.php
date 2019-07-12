
<div class="panel panel-default personal-panel">
            <div class="panel-body">
                
                <div class="row">
                    <div class="col col-xs-6 visible-xs visible-sm">
                        <a href="">Очистить корзину</a>
                    </div>
                    <div class="col col-xs-6 col-md-offset-9">
                        <?=Loc::getMessage("SALE_COUNT_ITEMS");?>
                        <span id = ""><?=count($arResult["ITEMS"]["AnDelCanBuy"]);?></span>
                        <?php if(!empty($arResult['allWeight'])): ?>
                            <br>
                            <?=Loc::getMessage("SALE_TOTAL_WEIGHT");?>
                             <span id = "allWeight_FORMATED"><?=$arResult['allWeight_FORMATED']?></span>
                        <?php endif; ?>
                             
                        <br><br>
                        <?=Loc::getMessage("SALE_TOTAL")?>
                        <span id = "allSum_FORMATED"><strong> <?=$arResult['allSum_FORMATED']?> </strong></span>
                        <br>
                        <?=Loc::getMessage('SALE_VAT_INCLUDED'); ?>
                        <span id = "allVATSum_FORMATED"><?=$arResult["allVATSum_FORMATED"]?></span>
                    </div>
                    <div class="col col-md-xs-12">
                        
                    </div>
                    <div class="col col-md-xs-12">

                    </div>
                </div>
                    
            </div>
        </div>