(function () {
    'use strict';

    var initParent = BX.Sale.OrderAjaxComponent.init,
        getBlockFooterParent = BX.Sale.OrderAjaxComponent.getBlockFooter,
        editOrderParent = BX.Sale.OrderAjaxComponent.editOrder
    ;

    BX.namespace('BX.Sale.OrderAjaxComponentExt');

    BX.Sale.OrderAjaxComponentExt = BX.Sale.OrderAjaxComponent;

    BX.Sale.OrderAjaxComponentExt.init = function (parameters) {
        initParent.apply(this, arguments);

        var editSteps = this.orderBlockNode.querySelectorAll('.bx-soa-editstep'), i;
        for (i in editSteps) {
            if (editSteps.hasOwnProperty(i)) {
                BX.remove(editSteps[i]);
            }
        }

    };

    BX.Sale.OrderAjaxComponentExt.getBlockFooter = function (node) {
        var parentNodeSection = BX.findParent(node, {className: 'bx-soa-section'});

        getBlockFooterParent.apply(this, arguments);

        if (/bx-soa-auth|bx-soa-properties|bx-soa-basket/.test(parentNodeSection.id)) {
            BX.remove(parentNodeSection.querySelector('.pull-left'));
            BX.remove(parentNodeSection.querySelector('.pull-right'));
        }

    };


    BX.Sale.OrderAjaxComponentExt.editOrder = function (section) {

        editOrderParent.apply(this, arguments);

        var sections = this.orderBlockNode.querySelectorAll('.bx-soa-section.bx-active'), i;

        for (i in sections) {
            if (sections.hasOwnProperty(i)) {
                if (!(/bx-soa-auth|bx-soa-properties|bx-soa-basket/.test(sections[i].id))) {
                    sections[i].classList.add('bx-soa-section-hide');
                }
            }
        }

        this.show(BX('bx-soa-properties'));

        this.editActiveBasketBlock(true);

        this.alignBasketColumns();

        if (!this.result.IS_AUTHORIZED) {
            this.switchOrderSaveButtons(true);
        }

    };


    BX.Sale.OrderAjaxComponentExt.initFirstSection = function (parameters) {

    };


})();