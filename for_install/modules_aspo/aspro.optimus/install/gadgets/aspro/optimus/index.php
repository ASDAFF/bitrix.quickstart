<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

$moduleID = strtolower($arGadget['GADGET_ID']);
$jsFunctionName = 'AsproGadget_GetDateSupport_'.str_replace('@', '', $arGadget['ID']);
$GLOBALS['APPLICATION']->SetAdditionalCSS('/bitrix/gadgets/aspro/'.$moduleID.'/styles.css');
?>
<div class="aspro-gadgets">
    <div class="aspro-gadgets-waiter"></div>
    <div class="<?=$moduleID;?>-aspro-gadgets-shield"></div>
    <div class="aspro-gadgets-title" title="<?=$arGadget['NAME']?>"><?=$arGadget['NAME']?></div>
    <div class="aspro-gadgets-content-layout"></div>
    <script type="text/javascript">
    if(typeof <?=$jsFunctionName?> !== 'function'){
        function <?=$jsFunctionName?>() {
            var obGadget = BX('t<?=$arGadget['ID']?>');
            if(obGadget){
                var obAsproGadget = BX.findChildren(obGadget, {'className':'aspro-gadgets'}, true);
                var obAsproGadgetLayout = BX.findChildren(obGadget, {'className':'aspro-gadgets-content-layout'}, true);
            }
            BX.ajax({
                url: '<?=substr(__DIR__, strpos(__DIR__, '/bitrix/gadgets/')).'/ajax.php';?>',
                method: 'POST',
                data: {'mid': '<?=$moduleID?>'},
                dataType: 'html',
                timeout: 30,
                async: true,
                processData: true,
                scriptsRunFirst: true,
                emulateOnload: true,
                start: true,
                cache: false,
                onsuccess: function(data){
                    if(obGadget){
                        if(obAsproGadget.length){
                            BX.addClass(obAsproGadget[0], 'aspro-gadgets-ready');
                        }
                        if(obAsproGadgetLayout.length){
                            BX.adjust(obAsproGadgetLayout[0], {html: data});
                        }
                    }
                },
                onfailure: function(){
                    if(obGadget){
                        if(obAsproGadget.length){
                            BX.addClass(obAsproGadget[0], 'aspro-gadgets-ready');
                        }
                        if(obAsproGadgetLayout.length){
                            BX.adjust(obAsproGadgetLayout[0], {html: '<div class="aspro-gadgets-title2 pink"><?=GetMessage('GD_ASPRO_ERROR')?></div>'});
                        }
                    }
                }
            });
        }
    }

    <?=$jsFunctionName?>();
    </script>
</div>