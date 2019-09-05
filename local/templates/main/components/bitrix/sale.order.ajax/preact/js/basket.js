/* VIEW */
function SaleOrderBasket({ ITEMS, COUPONS }) {
    return preact.h('div', { class: 'sale_order_basket' }, [
        preact.h('div', { class: 'sale_order_basket_name' }, 'Товары в заказе'),
        preact.h('div', { class: 'sale_order_basket_items' }, Object.keys(ITEMS).map(function (id) {
            return preact.h(SaleOrderBasketItem, { ITEM: ITEMS[id]['data']} );
        })),
        preact.h(SaleOrderBasketCoupon, { LIST: COUPONS })
    ])
}

function SaleOrderBasketItem({ ITEM }) {
    return preact.h('div', { class: 'sale_order_basket_item' }, [
        preact.h('div', { class: 'sale_order_basket_item_img' }, preact.h('img', { src: ITEM.DETAIL_PICTURE_SRC })),
        preact.h('div',  { class: 'sale_order_basket_item_content' }, [
            preact.h('a', { class: 'sale_order_basket_item_name', href: ITEM.DETAIL_PAGE_URL }, ITEM.NAME),
            (ITEM.PROPS.length) && preact.h(SaleOrderBasketItemProperties, { LIST: ITEM.PROPS })
        ]),
        preact.h('div', { class: 'sale_order_basket_item_pricing' }, [
            preact.h('div', { class: 'sale_order_basket_item_prices' }, [
                preact.h('span', { class: 'sale_order_basket_item_price' }, ITEM.PRICE_FORMATED),
                (ITEM.DISCOUNT_PRICE > 0) && preact.h('span', { class: 'sale_order_basket_item_base_price' }, ITEM.BASE_PRICE_FORMATED),
            ]),
            preact.h(SaleOrderBasketItemQuantity, { 
                ID: ITEM.ID, QUANTITY: ITEM.QUANTITY, MEASURE_TEXT: ITEM.MEASURE_TEXT,
                ACTIONS: { quantity: this.constructor.quantity }
            }),
            preact.h('div', { class: 'sale_order_basket_item_summary' }, [
                preact.h('span', { class: 'sale_order_basket_item_summary_price' }, ITEM.SUM),
                (ITEM.DISCOUNT_PRICE > 0) && preact.h('span', { class: 'sale_order_basket_item_base_summary_price' }, ITEM.SUM_BASE_FORMATED),
            ])
        ]),
        preact.h(SaleOrderBasketItemActions, { ID: ITEM.ID, ACTIONS: { remove: this.constructor.remove } })
    ])
}

function SaleOrderBasketItemProperties({ LIST }) {
    return preact.h('ul', { class: 'sale_order_basket_item_properties' }, LIST.map(function (PROPERTY) {
        return preact.h('li', { class: 'sale_order_basket_item_property' }, [
            preact.h('span', { class: 'sale_order_basket_item_property_name' }, PROPERTY.NAME),
            preact.h('span', { class: 'sale_order_basket_item_property_value' }, PROPERTY.VALUE)
        ])
    }))
}

function SaleOrderBasketItemQuantity({ ID, QUANTITY, MEASURE_TEXT, ACTIONS }) {
    return preact.h('div', { class: 'sale_order_basket_item_quantity' }, [
        preact.h('span', { class: 'sale_order_basket_item_quantity_decrease', onClick: ACTIONS.quantity.bind(this, ID, QUANTITY - 1) }),
        preact.h('input', { class: 'sale_order_basket_item_quantity_value', type: 'text', name: 'basket[' + ID + ']', value: QUANTITY, readonly: true }),
        preact.h('span', { class: 'sale_order_basket_item_quantity_increase', onClick: ACTIONS.quantity.bind(this, ID, QUANTITY + 1) }),
        preact.h('i', { class: 'sale_order_basket_item_quantity_measure' }, MEASURE_TEXT)
    ])
}

function SaleOrderBasketItemActions({ ID, ACTIONS }) {
    return preact.h('div', { class: 'sale_order_basket_item_actions' }, [
        preact.h('span', { class: 'sale_order_basket_item_action_delete', onClick: ACTIONS.remove.bind(this, ID) })
    ])
}

function SaleOrderBasketCoupon({ LIST }) {
    return preact.h('div', { class: 'sale_order_coupon_content' }, [
        (!this.state.SHOW) && preact.h('span', { class: 'sale_order_coupon_input_show', onClick: this.constructor.show.bind(this) }, 'У вас есть промокод или дисконтная карта?'),
        (this.state.SHOW) && preact.h('input', { 
            class: 'sale_order_coupon_input', type: 'text', name: 'COUPON', autocomplete: 'off',
            onChange: this.constructor.onChange.bind(this), placeholder: 'Введите промокод' }),
        (LIST.length > 0) && preact.h('div', { class: 'sale_order_coupons' }, LIST.map(function (COUPON) {
            return preact.h('label', { class: 'sale_order_coupon', 'data-status': COUPON.JS_STATUS }, [
                COUPON.COUPON,
                preact.h('input', { type: 'checkbox', onChange: this.constructor.remove.bind(this), value: COUPON.COUPON })
            ])
        }.bind(this)))
    ])
}

/* ACTIONS */
SaleOrderBasket.update = function (params, form) {
    form.classList.add('sale_order_form_loading');

    BX.ajax.post('/bitrix/components/bitrix/sale.basket.basket/ajax.php', Object.assign(params, {
        basketAction: 'recalculateAjax',
        sessid: BX.bitrix_sessid()
    }), function (response) {
        SaleOrder.update(form);
    })
}

SaleOrderBasketItem.quantity = function (id, quantity) {
    var input = document.getElementsByName('basket[' + id + ']')[0],
        params = {};

    if (quantity <= 0) {
        return false;
    }

    input.value = quantity;
    params['basket[QUANTITY_' + id + ']'] = quantity;

    SaleOrderBasket.update(params, input.form);
}

SaleOrderBasketItem.remove = function (id) {
    var input = document.getElementsByName('basket[' + id + ']')[0],
        params = {};

    params['basket[DELETE_' + id + ']'] = 'Y';

    SaleOrderBasket.update(params, input.form);
}

SaleOrderBasketCoupon.show = function () {
    this.setState(function (state) {
        return { SHOW: true }
    })
}

SaleOrderBasketCoupon.onChange = function (event) {
    var params = {};

    params['basket[coupon]'] = event.target.value;

    SaleOrderBasket.update(params, event.target.form);
}

SaleOrderBasketCoupon.remove = function (event) {
    var params = {};

    params['basket[delete_coupon][' + event.target.value + ']'] = event.target.value;
    event.target.value = '';

    SaleOrderBasket.update(params, event.target.form);
}
