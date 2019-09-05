/* VIEW */
function SaleOrderInnerPayment({ PAY, BUDGET, VALUE }) {
    return preact.h('div', null, [
        preact.h('input', {
            type: 'checkbox',
            name: 'PAY_CURRENT_ACCOUNT',
            value: 'Y',
            checked: PAY
        }),
        preact.h('input', {
            type: 'text',
            name: 'PAY_CURRENT_ACCOUNT_VALUE',
            value: VALUE
        }),
        preact.h('button', {
            type: 'button',
        }, 'Пересчитать'),
        preact.h('div', null, 'Средств на балансе: ' + BUDGET)
    ])
}