<?php if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

IncludeTemplateLangFile(__FILE__);

?>
<div class="span">
    <div class="arrow-contact">
        <a class="tel" href="<?=(isMobile() ? "tel:".str_replace(array(" ", "(", ")", "-"), array("", "", "", ""), $arResult["PREVIEW_TEXT"]) : 'javascript:void(0);')?>"> <span class="ico-tel"></span> <span class="cont-y"><?=$arResult["PREVIEW_TEXT"]?></span></a> <a class="hide-contact" href="#"><span class="ico-contact"></span> <span class="cont-y"><span><?=GetMessage("STORE_CONTACT_LABEL")?></span><span class="arrow-as">&#9660;</span></span></a>
        <div class="contact-sh" style="">
            <?echo $arResult["DETAIL_TEXT"];?></div>
    </div>

    <script>
        $(document).ready(function() {

            $(document).click(function(event) {
                if ($(event.target).closest(".contact-sh").length)
                    return;
                $(".contact-sh").slideUp("slow");
                event.stopPropagation();
            });
            $('.hide-contact').click(function() {
                $(this).siblings(".contact-sh").slideToggle("slow");
                return false;
            });
        });
    </script>

</div>