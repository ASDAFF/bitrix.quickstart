$(document).on('click','.js-favorite', function(e){
  e.preventDefault();

  // ADD2FAVORITE
  var $favorite = $(this),
      $product = $(this).closest('.js-product'),
      iProductId = parseInt($product.data('product-id'));
      //$darkArea = $product;

  if (iProductId > 0) {
    // if ($darkArea.length < 1) {
      // $darkArea = $product;
    // }
    //$darkArea.rsToggleDark({progress: true});
    $.ajax({
      type: 'POST',
      dataType: 'json',
      //async: false,
      data: {
        'rs_ajax': 'Y',
        'action': 'add2favorite',
        'element_id': iProductId,
      },
      success: function(data) {
        if (data.STATUS == 'OK') {
          //$('#bh-favorite_num').html(data.HTML);
          if (appSLine.favoriteList[iProductId]) { // remove from favorite
            delete appSLine.favoriteList[iProductId];
          } else { // add to favorite
            appSLine.favoriteList[iProductId] = true;

          }
          $favorite.find('.favorite__cnt').text(data.LIKES_COUNT);
          appSLine.setProductItems();
        } else {
          console.warn('favorite - error responsed');
          e.preventDefault();
        }
      },
      error: function() {
        console.warn('favorite - fail request');
        e.preventDefault();
      },
      complete:function() {
        //$darkArea.rsToggleDark();
      }
    });
  }
});