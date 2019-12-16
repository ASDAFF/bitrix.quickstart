$(document).ready(function(){

  $('.js_popup_detail').fancybox({
    width : 1170,
    wrapCSS : 'popup_detail',
    fitToView : true,
    autoSize : true,
    openEffect : 'fade',
    closeEffect : 'fade',
    padding : [25,20,25,20],
    helpers : {
      title: null
    },
    ajax : {
      dataType : 'html',
      headers  : { 'popup_detail': 'Y' }
    },
    beforeLoad  : function(){
      this.href = this.href + (0 < this.href.indexOf('?') ? '&' : '?') + 'popup_detail=Y';
    },
    beforeShow  : function(){
      appSLine.setProductItems();
    },
    afterClose  : function(){
      appSLine.setProductItems();
    },
    afterShow  :function(){
      $('.fancybox-inner').css('overflow','visible');
      //RSAL_RefresDetailjJScollPane();
    }
  });
});

  // catalog element -> hover //
$(document).on('mouseenter', '.catalog_item', function(){
  //$('.catalog_item').removeClass('is-hover');
  $(this).addClass('is-hover');
});

$(document).on('mouseleave', '.catalog_item', function(){
  $(this).removeClass('is-hover')
    .find('.div_select.opened').removeClass('opened').addClass('closed');
});

$(document).on('click', '.js-catalog_refresh a', function(e){

  var $link = $(this),
      sUrl = $link.attr('href')
        .replace('+', '%2B')/*.replace(' ', '+')*/, // url fix
      $loadElement = $link.closest('.js-catalog_refresh'),
      ajaxId = $loadElement.data('ajax-id');
      $refreshArea = $('#' + ajaxId);

  var ajaxRequest = {
        type: 'POST',
        url: sUrl,
        success: function(data) {
          var json = BX.parseJSON(data);

          if ($loadElement.data('history-push') != undefined) {
            history.pushState(null, null, sUrl);
          }

          if (json == null) {
            $refreshArea.html(data);
          } else {
            for (var id in json) {
              $('#' + id).html(json[id]);
            }
          }

          $link.parent().addClass('active').siblings().removeClass('active');
          appSLine.setProductItems()
        },
        error: function() {
          console.warn('sorter - change template -> error responsed');
        },
        complete: function() {
          appSLine.ajaxExec = false;
          $refreshArea.rsToggleDark();
        }
      };

  if (
    $loadElement.length > 0 &&
    !appSLine.ajaxExec &&
    ajaxRequest.url != '#' && ajaxRequest.url != undefined
  ) {
    $refreshArea.rsToggleDark({progress: true, progressTop: '100px'});

    appSLine.ajaxExec = true;

    ajaxRequest.url += (
      ajaxRequest.url.indexOf('?') < 0
        ? '?'
        : ajaxRequest.url.slice(-1) != '&'
          ? '&'
          : ''
    ) + 'rs_ajax=Y';

    if (ajaxId != undefined) {
      ajaxRequest.url += '&ajax_id=' + ajaxId;
    }

    $.ajax(ajaxRequest);
  }

  e.preventDefault();
});

$(document).on('click', '.js-ajaxpages a', function(e){
  var $link = $(this),
      ajaxUrl = $link.attr('href'),
      $loadElement = $link.closest('.js-ajaxpages'),
      ajaxRequest = {
        type: 'POST',
        url: ajaxUrl,
        success: function(data) {
          var json = BX.parseJSON(data);

          if (json == null) {
            $loadElement.replaceWith(data);
          } else {
            for (var id in json) {
              $('#' + id).html(json[id]);
            }
          }
        },
        error: function() {
          console.warn('ajaxpages -> error responsed');
        },
        complete: function() {
          appSLine.ajaxExec = false;
          $loadElement.rsToggleDark();
        }
      };

  ajaxRequest.url += (
    ajaxRequest.url.indexOf('?') < 1
      ? '?'
      : ajaxRequest.url.slice(-1) != '&'
        ? '&'
        : ''
  ) + 'rs_ajax=Y&ajax_type=pages';
  
  if ($loadElement.length > 0 && !appSLine.ajaxExec) {

    appSLine.ajaxExec = true;

    $.ajax(ajaxRequest);
  }
  e.preventDefault();
});

$(window).on('scroll', function() {
  $('.js-ajaxpages_auto').each(function() {
    var $ajaxpObj = $(this);
    if (200 > ($ajaxpObj.offset().top - window.pageYOffset - $(window).height()) && !appSLine.ajaxExec) {
      $ajaxpObj.trigger('click');
    }
  });
});