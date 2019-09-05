/* VIEW */
function SaleOrder(props) {
    return preact.h('form', {
        class: 'sale_order_form',
        novalidate: true,
        onChange: this.constructor.onChange.bind(this),
        onSubmit: this.constructor.onSubmit.bind(this)
    }, [
        preact.h(SaleOrderBasket, { ITEMS: props.GRID.ROWS, COUPONS: props.COUPON_LIST }),
        preact.h('div', { class: 'sale_order_steps' }, [
            preact.h(SaleOrderBlock, {
                NAME: 'Шаг 1: ' + props.ORDER_PROP.groups[1].NAME,
                HIDDEN: 'false',
                CONTENT: [
                    preact.h(SaleOrderProperties, { LIST: SaleOrderProperties.getByGroup(1, props.ORDER_PROP.properties) }),
                    preact.h(SaleOrderPersons, { LIST: props.PERSON_TYPE })
                ],
                ACTIONS: [
                    preact.h('span', { class: 'sale_order_block_action', 'data-step': 2, 'data-validate': true }, 'Далее')
                ]
            }),
            preact.h(SaleOrderBlock, {
                NAME: 'Шаг 2: ' + props.ORDER_PROP.groups[2].NAME,
                HIDDEN: 'true',
                CONTENT: [
                    preact.h(SaleOrderProperties, { LIST: SaleOrderProperties.getByGroup(2, props.ORDER_PROP.properties) }),
                    preact.h(SaleOrderShipments, { LIST: props.DELIVERY }),
                    preact.h(SaleOrderShipmentServices, {
                        SHIPMENTS: props.DELIVERY,
                        CHECKED: Object.keys(props.DELIVERY).filter(function (id) { return props.DELIVERY[id].CHECKED })
                    }),
                    preact.h(SaleOrderShipmentStores, {
                        SHIPMENTS: props.DELIVERY,
                        CHECKED: Object.keys(props.DELIVERY).filter(function (id) { return props.DELIVERY[id].CHECKED }), 
                        LIST: props.STORE_LIST
                    })
                ],
                ACTIONS: [
                    preact.h('span', { class: 'sale_order_block_action', 'data-step': 1 }, 'Назад'),
                    preact.h('span', { class: 'sale_order_block_action', 'data-step': 3, 'data-validate': true }, 'Далее')
                ]
            }),
            preact.h(SaleOrderBlock, {
                NAME: 'Шаг 3: Оплата',
                HIDDEN: 'true',
                CONTENT: [
                    (props.PAY_FROM_ACCOUNT === 'Y') && preact.h(SaleOrderInnerPayment, { 
                        PAY: (props.PAY_CURRENT_ACCOUNT === 'Y') ? true : false,
                        BUDGET: props.CURRENT_BUDGET_FORMATED,
                        VALUE: props.TOTAL.ORDER_TOTAL_PRICE - props.TOTAL.ORDER_TOTAL_LEFT_TO_PAY
                    }),
                    preact.h(SaleOrderPayments, { LIST: props.PAY_SYSTEM })
                ],
                ACTIONS: [
                    preact.h('span', { class: 'sale_order_block_action', 'data-step': 2 }, 'Назад'),
                    preact.h('button', { class: 'sale_order_block_action', type: 'submit', onSubmit: this.constructor.add.bind(this) }, 'Оформить')
                ]
            })
        ]),
        preact.h('div', { class: 'sale_order_total' }, [
            preact.h(SaleOrderTotal, { TOTAL: props.TOTAL }),
            !(Array.isArray(props.ERROR) && (props.ERROR.length === 0)) && preact.h(SaleOrderErrors, props.ERROR)
        ])
    ])
}

/* ACTIONS */
SaleOrder.getFormData = function (form) {
	var entity = BX.ajax.prepareForm(form);

	return entity.data;
}

SaleOrder.onChange = function (event) {
	event.preventDefault();

	if (~event.target.name.search('DELIVERY_EXTRA_SERVICES')) {
		SaleOrder.update(event.target.form);
		return true;
	}

	switch (event.target.name) {
		case 'DELIVERY_ID':
		case 'PAY_SYSTEM_ID':
		case 'PERSON_TYPE':
        case 'PAY_CURRENT_ACCOUNT':
			SaleOrder.update(event.target.form);
			break;
	}
}

SaleOrder.onSubmit = function (event) {
	event.preventDefault();
}

SaleOrder.update = function (form) {
	var data = SaleOrder.getFormData(form);

	data = { order: data };
	data['order']['location_type'] = 'code';

	data['sessid'] = BX.bitrix_sessid();
	data['via_ajax'] = 'Y';
	data['is_ajax_post'] = 'Y';
	data['soa-action'] = 'refreshOrderAjax';

	form.classList.add('sale_order_form_loading');

	BX.ajax.post('/personal/order/make/', data, function (response) {
		response = JSON.parse(response);

		if (response.success === 'N') {
			window.location.href = response.redirect;

			return false;
		}

		preactProvider.view['SaleOrder'][0].setState(response.order);
		form.classList.remove('sale_order_form_loading');
	})
}

SaleOrder.add = function (form) {
	var data = SaleOrder.getFormData(form);

	data['location_type'] = 'code';
	data['sessid'] = BX.bitrix_sessid();
	data['is_ajax_post'] = 'Y';
	data['soa-action'] = 'saveOrderAjax';

	form.classList.add('sale_order_form_loading');

	BX.ajax.post('/personal/order/make/', data, function (response) {
		response = JSON.parse(response);

		if (response.order.REDIRECT_URL) {
			window.location.href = response.order.REDIRECT_URL;

			return true;
		}

		if (response.order.ERROR) {
			preactProvider.view['SaleOrder'][0].setState(function (state) {
				return { ERROR: response.order.ERROR }
			});
		}

		form.classList.remove('sale_order_form_loading');
	})
}