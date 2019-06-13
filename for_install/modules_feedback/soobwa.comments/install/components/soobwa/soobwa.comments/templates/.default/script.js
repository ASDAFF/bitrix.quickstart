/*
 * библиотека - jquery.autoresize.js
 * */
(function($){$.fn.autoResize=function(options){var settings=$.extend({onResize:function(){},animate:true,animateDuration:150,animateCallback:function(){},extraSpace:20,limit:1000},options);this.filter('textarea').each(function(){var textarea=$(this).css({resize:'none','overflow-y':'hidden'}),origHeight=textarea.height(),clone=(function(){var props=['height','width','lineHeight','textDecoration','letterSpacing'],propOb={};$.each(props,function(i,prop){propOb[prop]=textarea.css(prop)});return textarea.clone().removeAttr('id').removeAttr('name').css({position:'absolute',top:0,left:-9999}).css(propOb).attr('tabIndex','-1').insertBefore(textarea)})(),lastScrollTop=null,updateSize=function(){clone.height(0).val($(this).val()).scrollTop(10000);var scrollTop=Math.max(clone.scrollTop(),origHeight)+settings.extraSpace,toChange=$(this).add(clone);if(lastScrollTop===scrollTop){return}lastScrollTop=scrollTop;if(scrollTop>=settings.limit){$(this).css('overflow-y','');return}settings.onResize.call(this);settings.animate&&textarea.css('display')==='block'?toChange.stop().animate({height:scrollTop},settings.animateDuration,settings.animateCallback):toChange.height(scrollTop)};textarea.unbind('.dynSiz').bind('keyup.dynSiz',updateSize).bind('keydown.dynSiz',updateSize).bind('change.dynSiz',updateSize)});return this}})(jQuery);
/*
 * End
 * */
$(function(){
    $(document).on('click','.comments_body_show_tree span', function(e){
        e.preventDefault();
        var parent = $(this).closest('.comments_item');
        if(!$('.comments_tree_box',parent).is(':visible')) {
            $('.comments_tree_box',parent).slideDown();
        } else {
            $('.comments_tree_box',parent).slideUp();
        }
    });

    $(document).on('click','.btn_comment', function(e){
        e.preventDefault();
        var parent =  $(this).closest('.form_box_wrap');
        var parentHeight = parent.height();
        var form = $(this).closest('form',parent);

        if($('.js-comments-form [name=TEXT]').val().length > 0){
            // отправка сообщения
            $.ajax({
                type: "GET",
                url: location.pathname + "?" + $('.js-comments-form').serialize(),
                dataType: 'json',
                success: function(date){
                    if(date.SEND == 'Y'){
                        parent.height(parentHeight);
                        form.fadeOut(function () {
                            $('.comments_thanks', parent).fadeIn();
                        });
                    }else{
                        $('.js-comments-form textarea.auto_resize').css('border-color', '#F00');
                        console.log(date);
                    }
                }
            });
        }else{
            $('.js-comments-form textarea.auto_resize').css('border-color', '#F00');
        }
    });

    $(document).on('click','.admin_panel_button', function(e){
        e.preventDefault();
        var parent = $(this).closest('.comments_header');
        $('.admin_panel_menu').stop().fadeOut(100);
        $('.admin_panel_menu',parent).stop().fadeToggle(100);
    });

    $("body").click(function (event) {
        if ($(event.target).closest(".admin_panel_menu:visible").length === 0) {
            $('.admin_panel_menu').stop().fadeOut(100);
        }
    });

    $('.auto_resize').autoResize();

    /*
    * Загрузка следующей страницы
    * */
    $(document).on('click','.js-read-more', function(e){
        e.preventDefault();
        // элемент
        var elm = $('.js-read-more');
        // колличество странниц
        var pages = $(elm).attr('data-pages');
        // на какой странице нажодимся
        var pagen = $(elm).attr('data-pagen');
        // id комментариев
        var id_comments = $(elm).attr('data-comments');

        pagen++;

        // запрос данных
        $.ajax({
            type: "GET",
            url: location.pathname + "?GET_COMMENT=Y&PAGEN=" + pagen,
            dataType: 'html',
            success: function(date){
                $('.js-get-comment-' + id_comments).append(date);
            }
        });

        // получаем комментарии
        if(pagen == pages){
            $(elm).hide();
        }else{
            $(elm).attr('data-pagen', pagen);
        }
    });

    /*
    * Удалить запись
    * */
    $(document).on('click','.js-del-comment', function(e){
        e.preventDefault();
        var element = e.target;
        $('.admin_panel_menu').stop().fadeOut(100);
        $.ajax({
            type: "GET",
            url: location.pathname + "?DELETE_COMMENT=Y&DELETE_COMMENT_ID=" + $(element).attr('data-id'),
            dataType: 'json',
            success: function(date){
                $('.js-del-item-' + $(element).attr('data-id')).fadeOut(function (){ $(this).remove();});
                console.log(date);
            }
        });
    });
    /*
    * Опубликовать запись
    * */
    $(document).on('click','.js-posted-comment', function(e){
        e.preventDefault();
        var element = e.target;
        $('.admin_panel_menu').stop().fadeOut(100);

        $.ajax({
            type: "GET",
            url: location.pathname + "?ACTIVE_COMMENT=Y&ACTIVE_COMMENT_ID=" + $(element).attr('data-id'),
            dataType: 'json',
            success: function(date){
                $('.js-del-item-' + $(element).attr('data-id')).removeClass('no_posted');
            }
        });
        element.closest('li').remove();
    });

});