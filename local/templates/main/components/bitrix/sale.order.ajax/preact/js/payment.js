/* VIEW */
function SaleOrderPayments({ LIST }) {
    return preact.h('div', { class: 'sale_order_payments' }, LIST.map(function (item) {
        return preact.h('div', { class: 'sale_order_payment' }, [
            preact.h('input', {
                type: 'radio',
                id: 'PAY_SYSTEM_ID_' + item.ID,
                name: 'PAY_SYSTEM_ID',
                defaultValue: item.ID,
                checked: item.CHECKED
            }),
            preact.h('label', { for: 'PAY_SYSTEM_ID_' + item.ID }, [
                preact.h('span', { class: 'sale_order_payment_logotip' }, [
                    item.PSA_LOGOTIP && preact.h('span', { class: 'sale_order_payment_logotip_img', style: 'background-image: url("' + item.PSA_LOGOTIP.SRC  + '")'})
                ]),
                preact.h('span', { class: 'sale_order_payment_name' }, item.NAME)
            ])
        ])
    }))
}