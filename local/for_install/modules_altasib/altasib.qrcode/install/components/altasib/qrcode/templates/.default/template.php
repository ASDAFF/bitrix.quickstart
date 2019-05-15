<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<script>
function alx_findPos(obj) {
        var curleft = curtop = 0;
        if (obj.offsetParent) {
                curleft = obj.offsetLeft
                curtop = obj.offsetTop
                while (obj = obj.offsetParent) {
                        curleft += obj.offsetLeft
                        curtop += obj.offsetTop
                }
        }
        return [curleft,curtop];
}

function openQr()
{
        var divQr = document.getElementById("alx-qr");
        if (!divQr)
                return;

           QrImgMin = document.getElementById("QrImgMin");
           xy=alx_findPos(QrImgMin);
           divQr.style.left = xy[0]+"px";
           divQr.style.top = xy[1]+"px";
           divQr.style.display = "block";

           var QrImgBig = document.getElementById("QrImgBig");
           leftQr = Math.round(xy[0]-(QrImgBig.height/2  - QrImgMin.height/2))+"px";
           topQr = Math.round(xy[1]-(QrImgBig.width/2  - QrImgMin.width/2))+"px";
           divQr.style.left = leftQr;
           divQr.style.top = topQr;

        return false;
}
function closeQr()
{
        var divQr = document.getElementById("alx-qr");
        if (!divQr)
                return;
           divQr.style.display = "none";
}
</script>
        <?//=GetMessage("ALTASIB_QRCODE_TITLE");?>
        <?
         if($arParams["QR_COPY"] == "Y")
            $copy = "COPY";
         else
            $copy = "";
        ?>
        <?
        if ($arResult["RESULT"] == "Y"):?>
          <?if($arParams["QR_MINI"]>0):?>
                  <div style="height: <?=$arParams["QR_MINI"]?>"><img alt="" src="<?=$arResult["QRCODE"];?>" id="QrImgMin" onClick="openQr()" width="<?=$arParams["QR_MINI"]?>"><?if(strlen($arParams["QR_TEXT"])>0):?><a href="#" class="alx_qr_text" onclick="openQr(); return false;"><?=htmlspecialcharsBack($arParams["QR_TEXT"])?></a><?endif;?></div>
                  <div id="alx-qr"><img alt="" id="QrImgBig" src="<?=$arResult["QRCODE_COPY"]?>" onclick="closeQr()"></div>
          <?else:?>
                  <img src="<?=$arResult["QRCODE"];?>" />
          <?endif;?>
        <?else:?>
                <?=GetMessage("ALTASIB_QRCODE_EMPTY_VAL")?>
        <?endif;?>
