/* VIEW */
function SaleOrderErrors({ PROPERTY }) {
    return preact.h('div', { class: 'sale_order_errors' }, [
        PROPERTY.map(function (item) {
            return preact.h('div', { class: 'sale_order_error' }, item)
        })
    ])
}