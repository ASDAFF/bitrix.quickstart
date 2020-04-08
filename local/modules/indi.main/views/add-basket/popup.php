<?
$strCount = $result['strCount'];
$artNum = $result['artNum'];
$img = $result['img'];
$element['ID'] = $result['id'];
$countItem = $result['countItem'];
$priceFormat = $result['priceFormat'];
$name = $result['name'];
?>
<div class="modal-header">
    <div class="row">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span class="sr-only">Закрыть</span></button>
        <div class="col-12">
            <h2 class="modal-title" id="form-result-new-label-2">Товар добавлен в корзину</h2>
        </div>
        <div class="col-12">
            <h4><?=$strCount?><a href="/personal/cart/">Посмотреть</a></h4>
        </div>
    </div>
</div>
<div class="modal-body">
    <div class="row product-row pb-xl-4">
        <div class="col-6 col-md-3 product-row__img justify-content-center"><img src="<?=$img?>" alt="" class="img-fluid"></div>
        <?=$artNum?>
        <div class="col-6 col-md-3 product-row__count d-flex justify-content-center">
            <div class="quantity js-popup-basket-quantity" data-id="<?=$element['ID']?>">
                <input type="button" value="-" class="minus js-popup-basket-dec">
                <input type="number" id="quantity_5cffcb1fbb8e1" class="col-6 input-text qty text js-popup-basket-input" data-step="1" min="1" max=""
                       name="quantity" value="<?=$countItem?>" title="Qty" size="4" pattern="[0-9]*" inputmode="numeric"
                       aria-labelledby="iPhone Dock quantity">
                <input type="button" value="+" class="plus js-popup-basket-inc">
            </div>
        </div>
        <div class="col-6 col-md-3 product-row__price text-center"><span><?=$priceFormat?></span></div>
    </div>
    <div class="row product-row product-row-btns py-5">
        <div class="offset-md-4 col-md-4 col-12 py-3"><a class="js-popup-close products__btn products__btn-continue p-3 p-md-4" href="javascript:void(0);">Продолжить</a></div>
        <div class="col-md-4 col-12 py-3"><a class="products__btn products__btn-offer p-3 p-md-4" href="/personal/cart/">Оформить заказ</a></div>
    </div>
</div>

<script>
    $('.js-popup-close').on('click', function (e) {
        $(this).parent().parent().parent().parent().parent().parent().modal('hide');
    });
</script>