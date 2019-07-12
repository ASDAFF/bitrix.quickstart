if (!window.productUnsuscribe) {
  window.productUnsuscribe = function(event, itemId, listSubscribeId) {

    event.preventDefault();

    if (!itemId || !listSubscribeId.hasOwnProperty(itemId))
      return;

    var ajaxUrl = '/bitrix/components/bitrix/catalog.product.subscribe.list/ajax.php';

    rsFlyaway.darken($(".js-elementid" + itemId));

    BX.ajax({
      method: 'POST',
      dataType: 'json',
      url: ajaxUrl,
      data: {
        sessid: BX.bitrix_sessid(),
        deleteSubscribe: 'Y',
        itemId: itemId,
        listSubscribeId: listSubscribeId[itemId]
      },
      onsuccess: BX.delegate(function(result) {
          if(result.success) {
            location.reload();
          } else {
            rsFlyaway.darken($(".js-elementid" + itemId));
          }
      }, this)
    });

  }
}
