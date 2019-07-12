<?php if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
/**
 * @var CBitrixComponentTemplate $this
 */
$this->setFrameMode(true);
IncludeTemplateLangFile(__FILE__);
/*<a data-toggle="modal" href="#feedBackModal">обратная связь <i class="icon-envelope feedb"></i></a>*/
?>
<div class="arrow-contact">
    <a href="#" class="hide-contact"><span><?=GetMessage("NEWS_CONTACT_LABEL")?></span><span class="arrow-as">&#9660;</span></a>
    <div class="contact-sh">
        <?echo $arResult["DETAIL_TEXT"];?>
    </div>
</div>
<a href="<?=(isMobile() ? "tel:".str_replace(array(" ", "(", ")", "-"), array("", "", "", ""), $arResult["PREVIEW_TEXT"]) : 'javascript:void(0);')?>" class="tel"><?=$arResult["PREVIEW_TEXT"]?></a>
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


<??>