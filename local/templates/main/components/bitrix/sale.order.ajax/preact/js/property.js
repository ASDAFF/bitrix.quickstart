/* VIEW */
function SaleOrderProperties({ LIST }) {
    return preact.h('div', { class: 'sale_order_properties' }, LIST.map(function (item) {
        return preact.h('div', { class: 'sale_order_property' }, [
            preact.h('label', { for: 'ORDER_PROP_' + item.ID, class: 'sale_order_property_name' }, item.NAME),
            preact.h(window['SaleOrderProperty' + item.TYPE], item)
        ])
    }))
}

function SaleOrderPropertySTRING(props) {
    if (props.MULTILINE === 'Y') {
        return preact.h('textarea', {
            id: 'ORDER_PROP_' + props.ID,
            name: 'ORDER_PROP_' + props.ID,
            class: 'sale_order_property_value',
            placeholder: props.DESCRIPTION,
            required: props.REQUIRED,
        }, props.VALUE)
    }

    return preact.h('input', {
        type: 'text',
        id: 'ORDER_PROP_' + props.ID,
        name: 'ORDER_PROP_' + props.ID,
        class: 'sale_order_property_value',
        placeholder: props.DESCRIPTION,
        defaultValue: props.VALUE,
        required: props.REQUIRED,
    })
}

function SaleOrderPropertyLOCATION(props) {
    return preact.h(SaleOrderLocation, props);
}

/* ACTIONS */
SaleOrderProperties.getByGroup = function (id, properties) {
    return properties.filter(function (item) {
        return (parseInt(item.PROPS_GROUP_ID) === id)
    })
}
