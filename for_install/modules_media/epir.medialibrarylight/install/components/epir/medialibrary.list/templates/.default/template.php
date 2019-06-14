<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();?>


<script type="text/javascript">

    $(document).ready(function() {
        $("a[rel=group]").fancybox({
            helpers: {
                          title : {
                              type : 'float'
                          }
                      }
        });

        $("img.media_lib").lazyload({
//            effect : "fadeIn",
            failure_limit : 5
        });
    });
    
</script>


<ul>
    <?foreach($arResult['COLLECTIONS'] as $arCol):?>
        <li><a href="<?=$arCol['URL']?>"><?=$arCol['NAME']?></a></li>
    <?endforeach?>    
</ul>

<ul class="item_coll">
<?foreach($arResult['COLLECTION']['ITEMS'] as $arItem):?>

    <li>
        <?if($arParams['INCLUDE_LAZY'] == 'Y'):?>
            <?
            $img_info = getimagesize($_SERVER['DOCUMENT_ROOT'].$arItem['THUMB_PATH']);
            ?>
            <a href="<?=$arItem['PATH']?>" rel="group" <?if($arParams['SHOW_TITLE'] == 'Y'):?> title="<?=$arItem['NAME']?>"<?endif?>><img class="media_lib" <?=$img_info[3]?> data-original="<?=$arItem['THUMB_PATH']?>" src="<?=$templateFolder?>/images/grey.gif" alt=""></a>
        <?else:?>
            <a href="<?=$arItem['PATH']?>" rel="group" <?if($arParams['SHOW_TITLE'] == 'Y'):?> title="<?=$arItem['NAME']?>"<?endif?>><img class="media_lib" src="<?=$arItem['THUMB_PATH']?>" alt=""></a>
        <?endif?>
    </li>
<?endforeach?>
</ul>
<br>
<br>

<?if($arResult['COLLECTION']['BACK_URL']):?>
    <a href="<?=$arResult['COLLECTION']['BACK_URL']?>"><?=GetMessage('EPIR_TAMPLATE_NAZAD')?></a>
<?endif?>

