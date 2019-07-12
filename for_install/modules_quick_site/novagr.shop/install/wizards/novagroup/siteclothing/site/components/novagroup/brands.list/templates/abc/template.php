<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();?>

<div class="col3-list brand">
    <div id="buttons_all">
        <a href="?abc=0" class="btnall">
            <?= GetMessage("B_BY_LIST") ?>
        </a>
        <a href="?abc=1" class="btnall select">
            <?= GetMessage("B_BY_ALFAVIT") ?>
        </a>
    </div>
    <hr>
    <div class="col3-list stuff-box alfavit">
        <? $i=0;
            foreach ($arResult['BRANDS'] as $ABC=>$items) {$i++;
                ?>
                <div <?if($i>0 && $i%4==1){?>style="clear:both;"<?}?>>
                    <span><?=$ABC?></span>
                    <ul>
                        <?foreach($items as $item){?>
                        <li>
                            <a href="<?=SITE_DIR."brands/?id=".$item['ID']?>">
                                <?= $item['NAME'] ?>
                            </a>
                        </li>
                        <?}?>
                    </ul>
                </div>
            <?
        }?>
    </div>
</div>