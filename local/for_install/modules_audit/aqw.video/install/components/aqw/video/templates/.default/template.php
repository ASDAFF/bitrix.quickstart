<?php if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<?php
    if(empty($arResult["ITEMS"])) return;
    $uniqComponentID = uniqid('aqw_video');     
?>

<style type="text/css">
    <?='#'.$uniqComponentID?>_popup {
        width:200px;
        height:100px;
        position:absolute;
        top:50%;
        left:50%;
        margin:-50px 0 0 -100px; /* [-(height/2)px 0 0 -(width/2)px] */
        display:none;
        border:1px solid #ccc;
        background: white;
        z-index: 9999999;
    }
    <?='#'.$uniqComponentID?>_popup_content {
        padding:10px;
    }
    <?='#'.$uniqComponentID?>_player_div {
        margin: 0 50%;
        background: white;
        position: absolute;
        left:-150px;
        top:500px;
    }
</style>

<div class="aqw_video_table" id="<?=$uniqComponentID?>_table_js_off"><?=GetMessage('JS_OFF')?></div>
<div class="aqw_video_table" id="<?=$uniqComponentID?>_table_js_on" style="display:none">

    <div id="<?=$uniqComponentID?>_popup"></div>

    <script type="text/javascript">
        function <?=$uniqComponentID?>_showPopup(content) {
            var popup = document.getElementById('<?=$uniqComponentID?>_popup');
            popup.innerHTML = content;
            popup.style.width = '<?=$arParams['WIDTH']+20?>px';
            popup.style.height = '<?=$arParams['HEIGHT']+45?>px';
            popup.style.margin = '-<?=$arParams['HEIGHT']/2?>px 0 0 -<?=$arParams['WIDTH']/2?>px';
            popup.style.top = <?=$uniqComponentID?>_getBodyScrollTop() + (<?=$uniqComponentID?>_getBodyHeight() / 2) +'px';
            popup.style.display = 'block';
        }

        function <?=$uniqComponentID?>_hidePopup()
        {
            var popup = document.getElementById('<?=$uniqComponentID?>_popup');
            var popup_content = document.getElementById('<?=$uniqComponentID?>_popup_content');
            popup_content.innerHTML = null;
            popup.style.display = 'none';
        }

        function <?=$uniqComponentID?>_getBodyHeight() {
            var myWidth = 0, myHeight = 0;
            if( typeof( window.innerWidth ) == 'number' ) {
                //Non-IE
                myWidth = window.innerWidth;
                myHeight = window.innerHeight;
            } else if( document.documentElement && ( document.documentElement.clientWidth || document.documentElement.clientHeight ) ) {
                //IE 6+ in 'standards compliant mode'
                myWidth = document.documentElement.clientWidth;
                myHeight = document.documentElement.clientHeight;
            } else if( document.body && ( document.body.clientWidth || document.body.clientHeight ) ) {
                //IE 4 compatible
                myWidth = document.body.clientWidth;
                myHeight = document.body.clientHeight;
            }
            return myHeight;
        }

        function <?=$uniqComponentID?>_getBodyScrollTop()
        {
            var scrOfX = 0, scrOfY = 0;
            if( typeof( window.pageYOffset ) == 'number' ) {
                //Netscape compliant
                scrOfY = window.pageYOffset;
                scrOfX = window.pageXOffset;
            } else if( document.body && ( document.body.scrollLeft || document.body.scrollTop ) ) {
                //DOM compliant
                scrOfY = document.body.scrollTop;
                scrOfX = document.body.scrollLeft;
            } else if( document.documentElement && ( document.documentElement.scrollLeft || document.documentElement.scrollTop ) ) {
                //IE6 standards compliant mode
                scrOfY = document.documentElement.scrollTop;
                scrOfX = document.documentElement.scrollLeft;
            }
            return  scrOfY;
        }
        document.getElementById('<?=$uniqComponentID?>_table_js_off').style.display = 'none';
        document.getElementById('<?=$uniqComponentID?>_table_js_on').style.display = 'block';
    </script>


    <div class="items" style="<?php if((int)$arParams['COUNT_ON_LINE']>0) print 'width:'.((int)$arParams['COUNT_ON_LINE']*((int)$arParams['WIDTH_IMAGE']+10)+10).'px;' ?>">
        <?php $iyzx = 0; foreach($arResult["ITEMS"] as $arKey=>$arItem): $iyzx = $iyzx + 1 ; print ($iyzx % $arParams['COUNT_ON_LINE'] == 1) ? '<div style="clear: both;"></div>' : "" ;  ?>
            <div style="float:left; cursor:pointer; padding:5px; margin:0px;" onclick="<?=$uniqComponentID?>_showPopup(document.getElementById('<?=$uniqComponentID?>_player_<?=$arKey?>').value);return false;">
                <?php
                    $uniqPrevImgID = uniqid('aqw_video_preview_');
                    //echo CFile::ShowImage("", $arParams['WIDTH_IMAGE'], $arParams['HEIGHT_IMAGE'], "border=0 id=".$uniqPrevImgID, "", false, "", $arParams['WIDTH_IMAGE'], $arParams['HEIGHT_IMAGE']);
                ?>
                <div align="center" style="width:<?=$arParams['WIDTH_IMAGE']?>px;height:<?=$arParams['HEIGHT_IMAGE']?>px;display:block;background: black;">
                    <img width="<?=$arParams['WIDTH_IMAGE']?>" height="<?=$arParams['HEIGHT_IMAGE']?>" src="<?=$arItem['preview']?>" border="0" id="<?=$uniqPrevImgID?>" />
                </div>
            </div>
            <textarea style="display: none;" id="<?=$uniqComponentID?>_player_<?=$arKey?>">
                <div id="<?=$uniqComponentID?>_popup_content">
                    <div align="center" style="width:<?=$arParams['WIDTH']?>px;height:<?=$arParams['HEIGHT']?>px;display:block;overflow:hidden;<?=($arItem['type']=='image') ? '' : 'background:black;' ?>">
                        <?php
                            if($arItem['type']=='image')
                            {
                            ?>
                            <img width="<?=$arParams['WIDTH']?>" height="<?=$arParams['HEIGHT']?>" src="<?=$arItem['preview']?>" border="0" id="<?=$arItem['id']?>" />
                            <?php
                            } else {
                                echo $arItem['player'];
                            }
                        ?>
                    </div>
                </div>
                <div style="float:left;width:20%;text-align: left;">
                    <?php
                        if(isset($arResult["ITEMS"][$arKey-1])):
                        ?>
                        <span style="margin-left:10px;cursor:pointer" onclick="<?=$uniqComponentID?>_showPopup(document.getElementById('<?=$uniqComponentID?>_player_<?=$arKey-1?>').value);return false;">&#9668;</span>
                        <?php
                            endif;
                        if(isset($arResult["ITEMS"][$arKey+1])):
                        ?>
                        <span style="margin-left:10px;cursor:pointer" onclick="<?=$uniqComponentID?>_showPopup(document.getElementById('<?=$uniqComponentID?>_player_<?=$arKey+1?>').value);return false;">&#9658;</span>
                        <?php
                            endif;
                    ?>
                    &nbsp;
                </div>
                <div style="float:left;width:40%;text-align: right;">&nbsp;</div>
                <div style="float:left;width:40%;text-align: right;">
                    <span style="margin-right:10px;cursor:pointer" onclick="<?=$uniqComponentID?>_hidePopup();return false;"><?=GetMessage("AQW_VIDEO_ZAKRYTQ")?></span>
                </div>
                <div style="clear: both;"></div>
            </textarea>
            <?php endforeach; ?>
        <div style="clear: both;"></div>
    </div>
</div>