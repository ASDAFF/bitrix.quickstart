/* VIEW */
function SaleOrderBlock({ NAME, HIDDEN, CONTENT, ACTIONS }) {
    return preact.h('div', { class: 'sale_order_block', 'aria-hidden': HIDDEN }, [
        preact.h('div', { class: 'sale_order_block_name' }, NAME),
        preact.h('div', { class: 'sale_order_block_content' }, CONTENT),
        ACTIONS && preact.h('div', { class: 'sale_order_block_actions', onClick: this.constructor.step.bind(this) }, ACTIONS)
    ])
}

/* ACTIONS */
SaleOrderBlock.step = function (event) {
    if (!event.target.classList.contains('sale_order_block_action')) {
        return false
    }

    if (event.target.hasAttribute('data-validate') && !SaleOrderBlock.validate.call(this, event.target)) {
        return false;
    }

    if (event.target.hasAttribute('data-step')) {
        var step = parseInt(event.target.getAttribute('data-step')) - 1,
            steps = event.target.parentElement.parentElement.parentElement;

        [].forEach.call(steps.children, function (item) {
            item.setAttribute('aria-hidden', 'true');
        });

        steps.children[step].setAttribute('aria-hidden', 'false');
    }
}

SaleOrderBlock.validate = function (element) {
    var block = element.parentElement.parentElement,
        elements = block.querySelectorAll('input[type="text"], input[type="email"], select, textarea'),
        valid = true;

    elements = [].filter.call(elements, function(element) {
        return element.name
    })

    var errors = elements.filter(function(element) {
        return !element.validity.valid
    })

    elements.forEach(function(element) {
        element.setAttribute('aria-invalid', false)
    })

    if (errors.length) {
        valid = false;

        errors.forEach(function(element) {
            element.setAttribute('aria-invalid', true)
        })

        block.scrollIntoView();
    }

    return valid;
}