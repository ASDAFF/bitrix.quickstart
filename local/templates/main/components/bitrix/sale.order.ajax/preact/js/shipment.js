/* VIEW */
function SaleOrderShipments({ LIST, STORES }) {
    return preact.h('div', { class: 'sale_order_shipments' }, Object.keys(LIST).map(function (id) {
        return preact.h('div', { class: 'sale_order_shipment' }, [
            preact.h('input', {
                type: 'radio',
                id: 'DELIVERY_ID_' + LIST[id].ID,
                name: 'DELIVERY_ID',
                defaultValue: LIST[id].ID,
                checked: LIST[id].CHECKED
            }),
            preact.h('label', { for: 'DELIVERY_ID_' + LIST[id].ID }, [
                preact.h('span', { class: 'sale_order_shipment_logotip' }, [
                    LIST[id].LOGOTIP && preact.h('span', { class: 'sale_order_shipment_logotip_img', style: 'background-image: url("' + LIST[id].LOGOTIP.SRC  + '")'}),
                    LIST[id].PRICE_FORMATED && preact.h('span', { class: 'sale_order_shipment_price' }, 
                        (LIST[id].DELIVERY_DISCOUNT_PRICE_FORMATED) ? LIST[id].DELIVERY_DISCOUNT_PRICE_FORMATED : LIST[id].PRICE_FORMATED)
                ]),
                preact.h('span', { class: 'sale_order_shipment_name' }, LIST[id].NAME)
            ])
        ])
    }))
}

function SaleOrderShipmentServices({ SHIPMENTS, CHECKED }) {
    if (CHECKED.length && SHIPMENTS[CHECKED[0]].EXTRA_SERVICES.length) {
        return preact.h('div', { class: 'sale_order_shipment_services' }, [
            preact.h('div', { class: 'sale_order_shipment_services_name' }, 'Выберите дополнительные услуги'),
            SHIPMENTS[CHECKED[0]].EXTRA_SERVICES.map(function (item) {
                return preact.h('div', { class: 'sale_order_shipment_service' }, [
                    preact.h('input', {
                        type: 'checkbox',
                        id: 'DELIVERY_EXTRA_SERVICES[' + SHIPMENTS[CHECKED[0]].ID + '][' + item.id + ']',
                        name: 'DELIVERY_EXTRA_SERVICES[' + SHIPMENTS[CHECKED[0]].ID + '][' + item.id + ']',
                        defaultValue: 'Y'
                    }),
                    preact.h('label', { for: 'DELIVERY_EXTRA_SERVICES[' + SHIPMENTS[CHECKED[0]].ID + '][' + item.id + ']', }, item.name),
                    preact.h('p', null, item.priceFormatted)
                ])
            }.bind(this))
        ])
    }
}

function SaleOrderShipmentStores({ SHIPMENTS, CHECKED, LIST }) {
    if (CHECKED.length && SHIPMENTS[CHECKED[0]].STORE.length) {
        return preact.h('div', { class: 'sale_order_stores' }, [
            preact.h('div', { class: 'sale_order_stores_name' }, 'Выберите пункт самовывоза'),
            SHIPMENTS[CHECKED[0]].STORE.map(function (id) {
                return preact.h('div', { class: 'sale_order_store' }, [
                    preact.h('input', {
                        type: 'radio',
                        id: 'BUYER_STORE_' + LIST[id].ID,
                        name: 'BUYER_STORE',
                        onChange: function (event) { this.setState({ CHECKED: event.target.value }) }.bind(this),
                        defaultValue: LIST[id].ID,
                        checked: (this.state.CHECKED && this.state.CHECKED === LIST[id].ID) || (LIST[id].ID == 1)
                    }),
                    preact.h('label', { for: 'BUYER_STORE_' + LIST[id].ID }, LIST[id].TITLE),
                    preact.h('p', null, LIST[id].ADDRESS)
                ])
            }.bind(this))
        ])
    }
}