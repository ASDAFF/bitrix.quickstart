<?php if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die(); ?>
<pre>
<?php //print_r($arResult['ITEMS']) ?>
</pre>
<?php if ($arParams['SHOW_BIG_IMAGE'] == 'Y'): ?>
    <div id="bigImage-<?=$arParams['GALLERY_ID']?>" class="<?=$arParams['GALLERY_SKIN']?>-big-image">
        <span>
            <div style="display: none;"></div>
            <img src="" alt=""/>
        </span>
    </div>
<?php endif; ?>
<ul id="gallery-<?=$arParams['GALLERY_ID']?>" class="<?=$arParams['GALLERY_SKIN']?>">
    <?php foreach ($arResult['ITEMS'] as &$arItem): ?>
        <?php
        $image = CFile::ResizeImageGet($arItem["~PREVIEW_PICTURE"]["ID"], array('width' => $arParams['SMALL_IMAGE_WIDTH'], 'height' => $arParams['SMALL_IMAGE_HEIGHT']), BX_RESIZE_IMAGE_EXACT, true);

        if ($arParams['SHOW_BIG_IMAGE'] == 'Y') {
            $arItem['bigImage'] = $bigImage = CFile::ResizeImageGet($arItem["~PREVIEW_PICTURE"]["ID"], array('width' => $arParams['BIG_IMAGE_WIDTH'], 'height' => $arParams['BIG_IMAGE_HEIGHT']), BX_RESIZE_IMAGE_PROPORTIONAL, true);
        } else {
            $bigImage = array('src' => '', 'width' => 0, 'height' => 0);
        }
        ?>
        <li>
            <a id="gallery-<?=$arParams['GALLERY_ID']?>-link-<?=$arItem['ID']?>" href="<?=(empty($bigImage['src']) ? $arItem['DETAIL_PAGE_URL'] : $bigImage['src'])?>"<?php if (!empty($bigImage['src'])): ?> data-bigImageHeight=<?=$bigImage['height']?> data-bigImageWidth=<?=$bigImage['width']?><?php endif; ?> data-id="<?=$arItem['ID']?>">
                <img src="<?=$image['src']?>" alt="<?=($arParams['SHOW_IMAGE_CAPTIONS'] == 'Y' ? $arItem['NAME'] : '')?>" width="<?=$image['width']?>" height="<?=$image['height']?>" />
                <span class="border"></span>
            </a>
        </li>
    <?php endforeach; ?>
</ul>


<script language="javascript">
$(document).ready(function () {
    var galleryPrefix = '<?=$arParams['GALLERY_ID']?>';
    var galleryId = '#gallery-<?=$arParams['GALLERY_ID']?>';
    var bigImageId = '#bigImage-<?=$arParams['GALLERY_ID']?>';
    var showBigImage = <?=($arParams['SHOW_BIG_IMAGE'] == 'Y' ? 'true' : 'false')?>;
    var usePreloader = <?=($arParams['USE_PRELOADER'] == 'Y' ? 'true' : 'false')?>;
    var maxHeight = 0;

    $(galleryId).jcarousel();

    if (showBigImage == true) {
        if (usePreloader == true) {
            $(galleryId + ' li a').each(function () {
                $('<img />').attr('src', $(this).attr('href'));
                maxHeight = Math.max(maxHeight, $(this).attr('data-bigImageHeight'));
            });

            $(bigImageId).css('height', maxHeight > 0 ? maxHeight.toString() + 'px' : 'auto');
        }

        $(galleryId + ' li a').click(function (e) {
            e.preventDefault();
            $(bigImageId + ' span > img').attr('src', $(this).attr('href'));
            $(bigImageId + ' span > img').attr('alt', $('>img', this).attr('src'));
            $(bigImageId + ' span > div').html($('>img', this).attr('alt'));

            if ($(bigImageId + ' span > div').html() == '') {
                $(bigImageId + ' span > div').hide();
            } else {
                $(bigImageId + ' span > div').show();
            }

            $(galleryId + ' li a').removeClass('active');
            $(this).addClass('active');
            document.location.hash = galleryPrefix + '-' + $(this).attr('data-id');
        });

        var flag = false;

        if (document.location.hash.indexOf(galleryPrefix) > -1) {
            var result = (new RegExp(galleryPrefix + '-([0-9]+)')).exec(document.location.hash);

            if (typeof(result[1]) != 'undefined') {
                $link = $('#gallery-' + galleryPrefix + '-link-' + result[1]);
                $('#gallery-' + galleryPrefix + '-link-' + result[1]).click();
                $(galleryId).data('jcarousel').scroll($(galleryId + ' li').index($link.parents('li:first')));
                flag = true;
            }
        }

        if (flag === false) {
            $(galleryId).find('li:first a').click();
        }
    }
});
</script>
