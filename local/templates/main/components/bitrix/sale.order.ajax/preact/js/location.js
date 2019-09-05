/* VIEW */
function SaleOrderLocation(props) {
    if (props.DISPLAY_VALUE && !this.state.ITEM) {
        return this.setState({ ITEM: props.DISPLAY_VALUE })
    }

    return preact.h('div', { class: 'sale_order_property_location' }, [
        preact.h('input', {
            type: 'hidden',
            name: 'ORDER_PROP_' + props.ID,
            defaultValue: this.state.CODE || props.VALUE,
            required: props.REQUIRED
        }),
        preact.h('input', {
            onInput: this.constructor.input.bind(this),
            onFocus: this.constructor.onFocus.bind(this),
            onBlur: this.constructor.onBlur.bind(this),
            class: 'sale_order_property_value',
            type: 'text',
            name: 'LOCATION',
            autocomplete: 'off',
            defaultValue: this.state.ITEM || props.DISPLAY_VALUE,
            required: props.REQUIRED
        }),
        this.state.ITEMS && preact.h(SaleOrderLocationItems, { LIST: this.state.ITEMS, ACTIONS: { set: this.constructor.set } }, this)
    ])
}

function SaleOrderLocationItems({ LIST, ACTIONS }) {
    return preact.h('div', {
        class: 'sale_order_locations',
        onMouseDown: ACTIONS.set.bind(this.props.children[0])
    }, LIST.map(function (item) {
        return preact.h('div', { class: 'sale_order_location', 'data-code': item.CODE }, [item.DISPLAY].concat(item.PATH).join(', '))
    }))
}

/* ACTIONS */
SaleOrderLocation.set = function (event) {
    this.setState(function (state) {
        var item = state.ITEMS.filter(function (item) {
            return item.CODE === event.target.getAttribute('data-code')
        });

        if (item.length) {
            return { CODE: item[0].CODE, ITEM: [item[0].DISPLAY].concat(item[0].PATH).join(', '), ITEMS: false, UPDATE: true }
        }
    });
}

SaleOrderLocation.getItems = function (data) {
    return data.ITEMS.map(function (item) {
        return {
            CODE: item.CODE,
            DISPLAY: item.DISPLAY,
            PATH: SaleOrderLocation.getItemPath(item.PATH, data.ETC.PATH_ITEMS)
        };
    })
}

SaleOrderLocation.getItemPath = function (path, data) {
    return path.map(function (id) {
        return data[id].DISPLAY
    })
}

SaleOrderLocation.input = function (event) {
    SaleOrderLocation.load.call(this, event.target)
},
SaleOrderLocation.onFocus = function (event) {
    if (event.target.defaultValue) { 
        event.target.value = '';
    }
}

SaleOrderLocation.onBlur = function (event) {
    if (event.target.defaultValue) {
        event.target.value = event.target.defaultValue;
    }

    if (this.state.UPDATE) { 
        this.setState(function (state) { 
            return { UPDATE: false }
        }); 

        SaleOrder.update(event.target.form) 
    }
}

SaleOrderLocation.load = BX.debounce(function (element) {
    element.parentElement.classList.add('sale_order_property_location_loading');

    BX.ajax({
        url: '/bitrix/components/bitrix/sale.location.selector.search/get.php',
        method: 'POST',
        data: {
            select: { 1: 'CODE', 2: 'TYPE_ID', 'VALUE': 'ID', 'DISPLAY': 'NAME.NAME' },
            filter: { '=PHRASE': element.value, '=NAME.LANGUAGE_ID': 'ru', '=SITE_ID': 's1' },
            additionals: { 1: 'PATH' },
            version: 2,
            PAGE_SIZE: 10,
            PAGE: 0
        },
        dataType: 'json',
        onsuccess: function (response) {
            element.parentElement.classList.remove('sale_order_property_location_loading');

            if (response.result) {
                this.setState(function (state) {
                    return { 
                        ITEMS: SaleOrderLocation.getItems(response.data)
                     };
                });

                return true;
            }
        }.bind(this)
    });
}, 300)
