window.JCCatalogBigdataProducts = function(arParams) {};

window.JCCatalogBigdataProducts.prototype.RememberRecommendation = function(obj, productId) {
  var rcmContainer = BX.findParent(obj, {
    'className': 'bigdata_recommended_products_items'
  });
  var rcmId = BX.findChild(rcmContainer, {
    'attr': {
      'name': 'bigdata_recommendation_id'
    }
  }, true).value;

  // save to RCM_PRODUCT_LOG
  var plCookieName = BX.cookie_prefix + '_RCM_PRODUCT_LOG';
  var plCookie = getCookie(plCookieName);
  var itemFound = false;

  var cItems = [],
    cItem;

  if (plCookie) {
    cItems = plCookie.split('.');
  }

  var i = cItems.length;

  while (i--) {
    cItem = cItems[i].split('-');

    if (cItem[0] == productId) {
      // it's already in recommendations, update the date
      cItem = cItems[i].split('-');

      // update rcmId and date
      cItem[1] = rcmId;
      cItem[2] = BX.current_server_time;

      cItems[i] = cItem.join('-');
      itemFound = true;
    } else {
      if ((BX.current_server_time - cItem[2]) > 3600 * 24 * 30) {
        cItems.splice(i, 1);
      }
    }
  }

  if (!itemFound) {
    // add recommendation
    cItems.push([productId, rcmId, BX.current_server_time].join('-'));
  }

  // serialize
  var plNewCookie = cItems.join('.');

  var cookieDate = new Date(new Date().getTime() + 1000 * 3600 * 24 * 365 * 10);
  document.cookie = plCookieName + "=" + plNewCookie + "; path=/; expires=" + cookieDate.toUTCString() + "; domain=" + BX.cookie_domain;
};

function getCookie(name) {
  var matches = document.cookie.match(new RegExp(
    "(?:^|; )" + name.replace(/([\.$?*|{}\(\)\[\]\\\/\+^])/g, '\\$1') + "=([^;]*)"
  ));
  return matches ? decodeURIComponent(matches[1]) : undefined;
}

function bx_rcm_recommendation_event_attaching(rcm_items_cont) {

  var detailLinks = BX.findChildren(rcm_items_cont, {
    'className': 'bx_rcm_view_link'
  }, true);

  if (detailLinks) {
    for (i in detailLinks) {
      BX.bind(detailLinks[i], 'click', function(e) {
        window.JCCatalogBigdataProducts.prototype.RememberRecommendation(
          BX(this),
          BX(this).getAttribute('data-product-id')
        );
      });
    }
  }
}

function bx_rcm_adaptive_recommendation_event_attaching(items, uniqId) {
  // onclick handler
  var callback = function(e) {

    var link = BX(this),
      j;

    for (j in items) {
      if (items[j].productUrl == link.getAttribute('href')) {
        window.JCCatalogBigdataProducts.prototype.RememberProductRecommendation(
          items[j].recommendationId, items[j].productId
        );

        break;
      }
    }
  };

  // check if a container was defined is the template
  var itemsContainer = BX(uniqId);

  if (!itemsContainer) {
    // then get all the links
    itemsContainer = document.body;
  }

  var links = BX.findChildren(itemsContainer, {
    tag: 'a'
  }, true);

  // bind
  if (links) {
    var i;
    for (i in links) {
      BX.bind(links[i], 'click', callback);
    }
  }
}

function bx_rcm_get_from_cloud(injectId, rcmParameters, localAjaxData) {
  var url = 'https://analytics.bitrix.info/crecoms/v1_0/recoms.php';
  var data = BX.ajax.prepareData(rcmParameters);

  if (data) {
    url += (url.indexOf('?') !== -1 ? "&" : "?") + data;
  }

  var onready = function(response) {

    if (!response.items) {
      response.items = [];
    }
    BX.ajax({
      url: '/bitrix/components/bitrix/catalog.bigdata.products/ajax.php?' + BX.ajax.prepareData({
        'AJAX_ITEMS': response.items,
        'RID': response.id
      }),
      method: 'POST',
      data: localAjaxData,
      dataType: 'html',
      processData: false,
      start: true,
      onsuccess: function(html) {
        var ob = BX.processHTML(html);

        // inject
        BX(injectId).innerHTML = ob.HTML;
        owlInit($(".owlslider.owlbigdata"), {
          margin: 24,
          responsive: {
            "0": {
              "items": "2"
            },
            "768": {
              "items": "3"
            },
            "956": {
              "items": "4"
            },
            1440: {
              "items": "5"
            }
          }
        });
        BX.ajax.processScripts(ob.SCRIPT);
      }
    });
  };

  BX.ajax({
    'method': 'GET',
    'dataType': 'json',
    'url': url,
    'timeout': 3,
    'onsuccess': onready,
    'onfailure': onready
  });
}
