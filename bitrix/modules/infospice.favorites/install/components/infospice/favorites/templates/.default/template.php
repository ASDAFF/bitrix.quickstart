<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die(); ?>
<? if ($arResult['ACCESS'] = 'Y'): ?>


    <script type="text/javascript">

        function AddItemToList(idItem, titleItem, urlItem) {

            item = $('<li/>', {
                id: 'item_' + idItem
            });
            $('<span/>', {
                class: 'infospice-favorite-handle',
                html: '&nbsp;'
            }).appendTo(item);
            $('<a/>', {
                href: escape(urlItem),
                text: titleItem
            }).appendTo(item);
            $('<a/>', {
                href: '#',
                class: 'infospice-favorite-remove',
                onclick: 'RemoveItemFavorite(' + idItem + ')',
                text: 'remove'
            }).appendTo(item);
            $(item).appendTo($('.infospice-favorite-menu'));
        }

        //add to favorite
        function AddToFavorite() {
            if ($('#name-item').val() && $('#url-item').val()) {
                $.ajax({
                    data: {
                        TITLE: $('#name-item').val(),
                        IBLOCK_ID: <?=$arResult['IBLOCK_ID']?>,
                        URL: $('#url-item').val()
                    },
                    url: '<?=$componentPath?>/add2favorite.php',
                    success: function (newItem) {
                        if (newItem) {
                            $(newItem).appendTo($('.infospice-favorite-menu'));
                            $('#url-item').val('');
                            $('#name-item').val('')
                        }
                    }
                });
            }
        }

        //add to favorite
        function AddCurrentPageToFavorite() {
            $.ajax({
                contentType: 'charset=<?=SITE_CHARSET?>',
                data: {
                    TITLE: document.title,
                    IBLOCK_ID: <?=$arResult['IBLOCK_ID']?>,
                    URL: location.href,
                    CURRENT_PAGE: 'Y'},
                url: '<?=$componentPath?>/add2favorite.php',
                success: function (newItem) {
                    if (newItem) {
                        $(newItem).appendTo($('.infospice-favorite-menu'));
                        $('.infospice-favorite-btn-add').addClass('already-favorite');
                        $('.infospice-favorite-btn-add').attr({title: '<?=GetMessageJS("INFOSPICE_FAVORITES_UJE_V_IZBANNOM")?>'});
                        $('.infospice-favorite-btn-add').unbind('click');
                    }
                }
            });
        }

        function RemoveItemFavorite(id) {
            $.ajax({
                data: {ID: id},
                url: '<?=$componentPath?>/delete_favorite.php',
                success: function (data) {
                    if (data) {
                        if ($('#item_' + data).attr('class') === 'infospice-favorite-current-page') {
                            $('.already-favorite').removeClass('already-favorite');
                            $('.infospice-favorite-btn-add').unbind('click');
                            $('.infospice-favorite-btn-add').bind('click', function (e) {
                                AddCurrentPageToFavorite();
                                e.preventDefault();
                            });
                        }
                        $('#item_' + data).remove();
                    }
                }
            });
        }

        $(function () {
            $('#form-submit').click(function () {
                AddToFavorite();
            });
            $($('#favorites-model')).prependTo('body');
            $('body').css('position', 'relative');
            $('#favorites-model').css('display', 'block');
        });</script>
    <div id="favorites-model" style="display:none;">
        <div class="fm_controls">
            <span class="infospice-favorite-panel-handle">&nbsp;</span>
            <ul>
                <li>
                    <a href="#" class="infospice-favorite-opener">opener</a>

                    <div class="infospice-favorite-drop">
                        <div class="infospice-favorite-drop-holder">
                            <span class="infospice-favorite-arrow">&nbsp;</span>

                            <div class="infospice-favorite-drop-frame">
                                <div class="infospice-favorite-head">
                                    <a href="#"
                                       class="infospice-favorite-close"><?= GetMessage("INFOSPICE_FAVORITES_ZAKRYTQ") ?></a>
                                    <strong
                                        class="infospice-favorite-title"><?= GetMessage("INFOSPICE_FAVORITES_IZBRANNOE") ?></strong>
                                </div>
                                <ul class="infospice-favorite-menu">
                                    <? foreach ($arResult["ITEMS"] as $arItem): ?>
                                        <?
                                        $this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
                                        $this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
                                        ?>
                                        <li class="<?= $arItem['CURRENT_PAGE_IS_FAVORITE'] ? 'infospice-favorite-current-page' : '' ?>"
                                            id="item_<?= $arItem['ID'] ?>">
                                            <span class="infospice-favorite-handle">&nbsp;</span>
                                            <a href="<?= $arItem['PROPERTY_URL_VALUE'] ?>"><?= $arItem['NAME'] ?></a>
                                            <a href="#" class="infospice-favorite-remove"
                                               onclick="RemoveItemFavorite(<?= $arItem['ID'] ?>)">remove</a>
                                        </li>
                                    <? endforeach ?>
                                </ul>
                                <div class="infospice-favorite-add-new-item">
                                    <a class="infospice-favorite-btn-open">
                                        <em>&nbsp;</em>
                                        <span><?= GetMessage("INFOSPICE_FAVORITES_DOBAVITQ_PUNKT_MENU") ?></span>
                                    </a>

                                    <div class="infospice-favorite-load-holder">
                                        <form action="#" class="infospice-favorite-add-form">
                                            <fieldset>
                                                <div class="infospice-favorite-form-item">
                                                    <label
                                                        for="name-item"><?= GetMessage("INFOSPICE_FAVORITES_NAZVANIE") ?></label>
                                                    <span class="infospice-favorite-field focus"><input type="text"
                                                                                                        id="name-item"
                                                                                                        value=""/></span>
                                                </div>
                                                <div class="infospice-favorite-form-item">
                                                    <label
                                                        for="url-item"><?= GetMessage("INFOSPICE_FAVORITES_SYLKA") ?></label>
                                                    <span class="infospice-favorite-field"><input type="text"
                                                                                                  id="url-item"
                                                                                                  value=""/></span>
                                                </div>
                                                <div class="infospice-favorite-action">
                                                    <input type="button"
                                                           value="<?= GetMessage("INFOSPICE_FAVORITES_DOBAVITQ") ?>"
                                                           id="form-submit"/>
                                                </div>
                                            </fieldset>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </li>
                <li>
                    <a title="<?= $arResult['CURRENT_PAGE_IS_FAVORITE'] ? GetMessage("INFOSPICE_FAVORITES_UJE_V_IZBRANNOM") : GetMessage("INFOSPICE_FAVORITES_DOBAVITQ_TEKUSUU_STR") ?>"
                       class="infospice-favorite-btn-add<?= $arResult['CURRENT_PAGE_IS_FAVORITE'] ? ' already-favorite' : '' ?>">add</a>
                </li>
            </ul>
        </div>
    </div>
<?
endif?>