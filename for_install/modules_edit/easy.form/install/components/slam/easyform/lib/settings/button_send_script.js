function initCreateSendMail(arParams) {
    var oBut = arParams.oCont.appendChild(BX.create("INPUT", {props: {'type': 'button', value: arParams.data}}));
    oBut.onclick = function()
    {
        BX.ajax({
            url: '/bitrix/components/slam/easyform/send_ajax.php',
            data: {action: 'add', type : 'event'},
            method: 'POST',
            dataType: 'json',
            onsuccess: function(data){
                if(data.value) {
                    var s = document.querySelector('select[name="EVENT_MESSAGE_ID[]"]');
                    var o = document.createElement('option');
                    o.value = data.value;
                    o.selected = true;
                    if (typeof o.textContent === 'undefined')
                        o.innerText = data.text;
                    else
                        o.textContent = data.text;
                    s.appendChild(o);
                } else {
                    console.log('error');
                }
            }
        });
    }
}
function initCreateSendIblock(
    arParams) {
    var oBut = arParams.oCont.appendChild(BX.create("INPUT", {props: {'type': 'button', value: arParams.data}}));
    oBut.onclick = function()
    {
        BX.ajax({
            url: '/bitrix/components/slam/easyform/send_ajax.php',
            data: {action: 'add', type : 'iblock'},
            method: 'POST',
            dataType: 'json',
            onsuccess: function(data){
                if(data.value) {
                    var s = document.querySelector('select[name="IBLOCK_TYPE"] option[value="'+ data.type_value +'"]');
                    if(s.length > 0){

                        s.forEach(function(el) {
                            if(el.value == data.type_value)
                                el.setAttribute('selected', true);
                            else
                                el.removeAttribute('selected');

                        });

                    } else {

                        var s = document.querySelector('select[name="IBLOCK_TYPE"]');
                        s.querySelectorAll('option').forEach(function(el) {
                            el.removeAttribute('selected');
                        });
                        var o = document.createElement('option');
                        o.value = data.type_value;
                        o.selected = true;
                        if (typeof o.textContent === 'undefined')
                            o.innerText = data.type_text;
                        else
                            o.textContent = data.type_text;
                        o.setAttribute('selected', 'selected');
                        s.appendChild(o);

                    }

                    var s = document.querySelector('select[name="IBLOCK_ID"]');

                    s.querySelectorAll('option').forEach(function(el) {
                        el.removeAttribute('selected');
                    });

                    var o = document.createElement('option');
                    o.value = data.value;
                    o.selected = true;
                    if (typeof o.textContent === 'undefined')
                        o.innerText = data.text;
                    else
                        o.textContent = data.text;
                    o.setAttribute('selected', 'selected');
                    s.appendChild(o);
                    s.parentNode.querySelector('input[type="button"]').click();

                } else {
                    console.log('error');
                }
            }
        });
    }
}