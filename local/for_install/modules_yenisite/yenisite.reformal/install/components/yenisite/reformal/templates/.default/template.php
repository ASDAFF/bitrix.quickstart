<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

if(!isset($arParams['TYPE_OF_INTEGRATION']))  
  {
    $arParams['TYPE_OF_INTEGRATION'] =  'widget'; 
    $arParams['TYPE'] =  'standart'; 
    $arParams['TAB_INDENT'] =  '50'; 
    $arParams['UNITS']=  '%'; 
    $arParams['TAB_BG_COLOR']=  '#F05A00'; 
    $arParams['TAB_BORDER_COLOR']=  '#FFFFFF';  
    $arParams['TAB_BORDER_WIDTH'] =  '2'; 
    $arParams['FROM_NEW_WINDOW']=  'Y'; 
    $arParams["ADD_LOGO"]= 'Y';
}
?>

<? 
    if($arParams["TYPE_OF_INTEGRATION"] == 'integral')
    { ?> <iframe style="width: 100%; height: 100%; border: 0;" frameborder="0" 
                src="http://reformal.ru/widget/<?=$arParams["PROJECT_ID"]; ?> "></iframe> <?                
    }
    
    if($arParams["TYPE_OF_INTEGRATION"] != 'integral')
    {
        switch ($arParams["TYPE"])
        {
            case link:      
             
                if($arParams["FROM_NEW_WINDOW"] == 'Y') { ?>
        
                    <a href="http://<?=$arParams["PROJECT_HOST"]; ?>" onclick="window.open('http://<?=$arParams["PROJECT_HOST"]; ?>');
                    return false;"><?=$arParams["HEADER_TEXT"]; ?></a><script type="text/javascript">
    
                    var reformalOptions = {
                        project_id: <?=$arParams["PROJECT_ID"]; ?>,
                        show_tab: false,
                        project_host: "<?=$arParams["PROJECT_HOST"]; ?>",
                        force_new_window: true
                     };
    
                    (function() {
                     var script = document.createElement('script');
                    script.type = 'text/javascript'; script.async = true;
                    script.src = ('https:' == document.location.protocol ? 'https://' : 'http://') + 'media.reformal.ru/widgets/v3/reformal.js';
                    document.getElementsByTagName('head')[0].appendChild(script);
                    })();
                    
                    </script><noscript><a href="http://reformal.ru"><img src="http://media.reformal.ru/reformal.png" /></a>
                    <a href="http://<?=$arParams["PROJECT_HOST"]; ?>"><?=$arParams["HEADER_TEXT"]; ?></a></noscript>
        
            <?  } 
                
                if($arParams["FROM_NEW_WINDOW"] == 'N') { ?>

                <a href="http://<?=$arParams["PROJECT_HOST"]; ?>" onclick="Reformal.widgetOpen();return false;" 
                onmouseover="Reformal.widgetPreload();"><?=$arParams["HEADER_TEXT"]; ?></a><script type="text/javascript">
                    
                var reformalOptions = {
                    project_id: <?=$arParams["PROJECT_ID"]; ?>,
                    show_tab: false,
                    project_host: "<?=$arParams["PROJECT_HOST"]; ?>"
                };
    
                (function() {
                var script = document.createElement('script');
                script.type = 'text/javascript'; script.async = true;
                script.src = ('https:' == document.location.protocol ? 'https://' : 'http://') + 'media.reformal.ru/widgets/v3/reformal.js';
                document.getElementsByTagName('head')[0].appendChild(script);
                })();
                
                </script><noscript><a href="http://reformal.ru"><img src="http://media.reformal.ru/reformal.png" /></a>
                <a href="http://<?=$arParams["PROJECT_HOST"]; ?>"><?=$arParams["HEADER_TEXT"]; ?></a></noscript>

             <? }
                break;
      
            case customizable: ?>
                
                 <script type="text/javascript">
                    var reformalOptions = {
                    project_id: <?=$arParams["PROJECT_ID"]; ?>,
                    project_host: "<?=$arParams["PROJECT_HOST"]; ?>",
                    tab_orientation: "<?=$arParams["TAB_ORIENTATION"]; ?>",
                    tab_indent: "<?=$arParams["TAB_INDENT"].$arParams["UNITS"];?>",
                    tab_image_url:"/bitrix/components/yenisite/reformal/images/<?=$arParams["TAB_IMAGE_URL"]; ?>",
                    tab_is_custom: true
                    };
                    
                   <? if($arParams["FROM_NEW_WINDOW"] == 'Y') { ?> reformalOptions.force_new_window = true; <? } ?>
                
                    (function() {
                    var script = document.createElement('script');
                    script.type = 'text/javascript'; script.async = true;
                    script.src = ('https:' == document.location.protocol ? 'https://' : 'http://') + 'media.reformal.ru/widgets/v3/reformal.js';
                    document.getElementsByTagName('head')[0].appendChild(script);
                    })();
                </script><noscript><a href="http://reformal.ru"><img src="http://media.reformal.ru/reformal.png" /></a>
                <a href="http://<?=$arParams["PROJECT_HOST"]; ?>">O<?=GetMessage("YENISITE_REFORMAL_TZYVY_I_PREDLOJENIA")?></a></noscript>
 
              <?  break;
        
            case standart: ?>
                
                <script type="text/javascript">
                    var reformalOptions = {
                    project_id: <?=$arParams["PROJECT_ID"]; ?>,
                    project_host: "<?=$arParams["PROJECT_HOST"]; ?>",
                    tab_orientation: "<?=$arParams["TAB_ORIENTATION"]; ?>",
                    tab_indent: "<?=$arParams["TAB_INDENT"].$arParams["UNITS"];?>",
                    tab_bg_color: "<?=$arParams["TAB_BG_COLOR"]; ?>",
                    tab_border_color: "<?=$arParams["TAB_BORDER_COLOR"]; ?>",
                    tab_border_width: <?=$arParams["TAB_BORDER_WIDTH"]; ?>
                    };
                    
                    <? if($arParams["ADD_LOGO"] == 'Y'){ 
                            if($arParams["TAB_ORIENTATION"] == 'left' || $arParams["TAB_ORIENTATION"] == 'right'){
                                ?> reformalOptions.tab_image_url = "/bitrix/components/yenisite/reformal/images/left-right1.png"; <?
                            }
                            if($arParams["TAB_ORIENTATION"] == 'top-left' || $arParams["TAB_ORIENTATION"] == 'top-right'
                               || $arParams["TAB_ORIENTATION"] == 'bottom-left' || $arParams["TAB_ORIENTATION"] == 'bottom-right'){
                                ?> reformalOptions.tab_image_url = "/bitrix/components/yenisite/reformal/images/top-bottom1.png"; <?
                            }
                    }
                    ?>
                        
                    <? if($arParams["ADD_LOGO"] == 'N'){
                            if($arParams["TAB_ORIENTATION"] == 'left' || $arParams["TAB_ORIENTATION"] == 'right'){
                                ?> reformalOptions.tab_image_url = "/bitrix/components/yenisite/reformal/images/left-right0.png"; <?
                            }
                            if($arParams["TAB_ORIENTATION"] == 'top-left' || $arParams["TAB_ORIENTATION"] == 'top-right'
                               || $arParams["TAB_ORIENTATION"] == 'bottom-left' || $arParams["TAB_ORIENTATION"] == 'bottom-right'){
                                ?> reformalOptions.tab_image_url = "/bitrix/components/yenisite/reformal/images/top-bottom0.png"; <?
                            }
                    } 
                    ?>
                    
                    <? if($arParams["FROM_NEW_WINDOW"] == 'Y') { ?> reformalOptions.force_new_window = true; <? } ?>
    
                    (function() {
                    var script = document.createElement('script');
                    script.type = 'text/javascript'; script.async = true;
                    script.src = ('https:' == document.location.protocol ? 'https://' : 'http://') + 'media.reformal.ru/widgets/v3/reformal.js';
                    document.getElementsByTagName('head')[0].appendChild(script);
                     })();
                     
                </script><noscript><a href="http://reformal.ru"><img src="http://media.reformal.ru/reformal.png" /></a>
                <a href="http://<?=$arParams["PROJECT_HOST"]; ?>"><?=$arParams["HEADER_TEXT"]; ?></a></noscript>
                
              <?  break;
        }
    }
    
?>


