function PickpointHandler(result) {
    pp_id = document.getElementById("pp_id");
    pp_id.value = result["id"];
    pp_address = document.getElementById("pp_address");
    pp_address.value = result["address"];
    pp_name = document.getElementById("pp_name");
    pp_name.value = result["name"];
    pp_zone = document.getElementById("pp_zone");
    pp_zone.value = result["zone"];
    pp_coeff = document.getElementById("pp_coeff");
    pp_coeff.value = result["coeff"];
    Span = document.getElementById("sPPDelivery");
    Span.innerHTML = result['address'] + "<br/>" + result['name'];
    document.getElementById('tPP').style.display = 'block';
    if (typeof submitForm === 'function') {
        submitForm();
    } else if (typeof BX.Sale.OrderAjaxComponent.sendRequest === 'function') {
        BX.Sale.OrderAjaxComponent.sendRequest();
    }
    PickPoint.close();
    document.getElementById('pp_sms_phone').focus();
}

function listen(evnt, elem, func) {
    if (elem.addEventListener) { // W3C DOM
        elem.addEventListener(evnt, func, false);
    } else if (elem.attachEvent) { // IE DOM
        return elem.attachEvent("on" + evnt, func);;
    } else {
        return false;
    }
}

function findParentNode(parentName, childObj) {
    var testObj = childObj.parentElement;

    while(testObj) {
        if(testObj.childNodes[1].getAttribute('name') == parentName) {
            return testObj.childNodes[1]
        } else {
            testObj = testObj.parentElement;
        }
    }

    return false;
}

function CheckData() {
    var visibleDeliveryBlock = document.querySelector('#bx-soa-delivery');
    var errorMessage;
    var errorElementIndex;
    if (visibleDeliveryBlock) {
        var errors = BX.Sale.OrderAjaxComponent.result.ERROR.DELIVERY || [];
        if (visibleDeliveryBlock.classList.contains('bx-step-completed')) {
            errorMessage = validatePickpointSelection().replace('\n', '<br>');
            if (BX.Sale.OrderAjaxComponent.result.ERROR.DELIVERY_PICKPOINT !== errorMessage) {
                errorElementIndex = errors.indexOf(BX.Sale.OrderAjaxComponent.result.ERROR.DELIVERY_PICKPOINT);
                if (errorElementIndex !== -1) {
                    errors.splice(errorElementIndex, 1);
                    BX.Sale.OrderAjaxComponent.result.ERROR.DELIVERY = errors;
                    BX.Sale.OrderAjaxComponent.showBlockErrors(BX.Sale.OrderAjaxComponent.deliveryBlockNode);
                }
                BX.Sale.OrderAjaxComponent.result.ERROR.DELIVERY_PICKPOINT = errorMessage;
            }
            if (errorMessage.length > 0) {
                if (errors.indexOf(errorMessage) === -1) {
                    errors.push(errorMessage);
                    BX.Sale.OrderAjaxComponent.result.ERROR.DELIVERY = errors;
                    BX.Sale.OrderAjaxComponent.showBlockErrors(BX.Sale.OrderAjaxComponent.deliveryBlockNode);
                }
                BX.Sale.OrderAjaxComponent.switchOrderSaveButtons(false);
            } else {
                BX.Sale.OrderAjaxComponent.switchOrderSaveButtons(true);
            }
        } else {
            errorElementIndex = errors.indexOf(BX.Sale.OrderAjaxComponent.result.ERROR.DELIVERY_PICKPOINT);
            if (errorElementIndex !== -1) {
                errors.splice(errorElementIndex, 1);
                BX.Sale.OrderAjaxComponent.result.ERROR.DELIVERY = errors;
                BX.Sale.OrderAjaxComponent.showBlockErrors(BX.Sale.OrderAjaxComponent.deliveryBlockNode);
            }
        }
    }
    var Form = Table = document.getElementById('tPP');
    if (Form) {
        while (Form = Form.parentNode) {
            if (Form.tagName == "FORM") {
                break;
            }
        }
        if (Form && Form.tagName == "FORM") {
            arInputs = Form.getElementsByTagName("input");
            for (i = 0; i < arInputs.length; i++) {
                switch (arInputs[i].type) {
                    case "button":
                        if (arInputs[i].getAttribute("onclick")) {
                            str = arInputs[i].getAttribute("onclick").toString();
                            arMatch = (str.match(/submitForm\('(\S+)'\);/));

                            if (arMatch && arMatch[1] == "Y") {
                                sLoad = arMatch[0];
                                arInputs[i].onclick = function () {
                                    return PPFormSubmit(sLoad)
                                };
                            }
                        }
                        break;
                    case "submit":
                        if (arInputs[i].name == "contButton") {
                            arInputs[i].onclick = function () {
                                return PPFormSubmit()
                            };
                        }
                        break;
                }
            }

            arHref = Form.getElementsByTagName("a");
            for (i = 0; i < arHref.length; i++) {
                if (arHref[i].getAttribute("onclick")) {
                    str = arHref[i].getAttribute("onclick").toString();
                    arMatch = (str.match(/submitForm\('(\S+)'\);/));

                    if (arMatch && arMatch[1] == "Y") {
                        sLoad = arMatch[0];
                        arHref[i].onclick = function () {
                            return PPFormSubmit(sLoad)
                        };
                    }
                }
            }
        }
    }

    window.setTimeout(
        function () {
            return CheckData();
        }, 500
    );
}

function validatePickpointSelection() {
    var pp_id = document.getElementById("pp_id");
    var pp_sms_phone = document.getElementById("pp_sms_phone");
    var sMessage = '';
    var iErrNum = 1;

    if (pp_id && pp_sms_phone) {
        if (!pp_id.value) {
            sMessage += iErrNum + ") Не выбрана точка доставки\n";
            iErrNum++;
        }

        if (!pp_sms_phone.value.match(/\+7[0-9]{10}$/)) {
            sMessage += iErrNum + ") Номер телефона должен быть заполнен в виде +79160000000";
        }
    }

    return sMessage;
}

function PPFormSubmit(sLoad) {
    var pp_id = document.getElementById("pp_id");
    if (document.getElementById('tPP') && pp_id) {
        var pickPointDeliveryId = pp_id.getAttribute('data-delivery-id');
        var allInputs = document.getElementsByTagName("input"),
            isPickpointSelected = false;

        for (var x = 0; x < allInputs.length; x++) {
            if(allInputs[x].name === 'DELIVERY_ID' && allInputs[x].value === pickPointDeliveryId
                && allInputs[x].checked === true) {
                isPickpointSelected = true;
            }
        }

        if(isPickpointSelected) {
            sMessage = validatePickpointSelection();
            bSuccess = sMessage.length <= 0;

            if (bSuccess) {
                if (sLoad) {
                    eval(sLoad);
                }
            } else {
                alert(sMessage);
            }

            return bSuccess;
        }
    }

    if (sLoad) {
        eval(sLoad)
    }

    return true;
}

window.onload = function () {
    CheckData();
};
