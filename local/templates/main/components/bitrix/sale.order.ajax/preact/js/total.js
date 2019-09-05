/* VIEW */
function SaleOrderTotal({ TOTAL }) {
    return preact.h('div', { class: 'sale_order_summary' }, [
        preact.h('div', { class: 'sale_order_summary_basket' }, [
            preact.h('b', null, 'Стоимость товаров'),
            preact.h('span', { class: 'sale_order_summary_basket_price' }, TOTAL.ORDER_PRICE_FORMATED),
            ((TOTAL.DISCOUNT_PRICE > 0) && (TOTAL.ORDER_PRICE !== TOTAL.PRICE_WITHOUT_DISCOUNT_VALUE)) && preact.h('span', { class: 'sale_order_summary_basket_price_old' }, TOTAL.PRICE_WITHOUT_DISCOUNT)
        ]),
        preact.h('div', { class: 'sale_order_summary_delivery' }, [
            preact.h('span', null, 'Доставка'),
            preact.h('span', null, [
                (TOTAL.DELIVERY_PRICE) ? TOTAL.DELIVERY_PRICE_FORMATED : 'Бесплатно'
            ])
        ]),
        (TOTAL.ORDER_TOTAL_LEFT_TO_PAY && TOTAL.ORDER_TOTAL_LEFT_TO_PAY < TOTAL.ORDER_TOTAL_PRICE) && preact.h('div', { class: 'sale_order_summary_delivery' }, [
            preact.h('span', null, 'Оплачено'),
            preact.h('span', null, TOTAL.PAYED_FROM_ACCOUNT_FORMATED)
        ]),
        (TOTAL.DISCOUNT_PRICE > 0) && preact.h('div', { class: 'sale_order_summary_discount' }, [
            preact.h('span', null, 'Экономия'),
            preact.h('span', null, TOTAL.DISCOUNT_PRICE_FORMATED)
        ]),
        preact.h('div', { class: 'sale_order_summary_total' }, [
            preact.h('b', null, 'Итого'),
            preact.h('span', null, (TOTAL.ORDER_TOTAL_LEFT_TO_PAY && TOTAL.ORDER_TOTAL_LEFT_TO_PAY < TOTAL.ORDER_TOTAL_PRICE) ? TOTAL.ORDER_TOTAL_LEFT_TO_PAY_FORMATED : TOTAL.ORDER_TOTAL_PRICE_FORMATED)
        ])
    ])
}