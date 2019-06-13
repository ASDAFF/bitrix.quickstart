if (!window._ua) {
    var _ua = navigator.userAgent.toLowerCase();
}

if(sitemap.lang.length <= 0)
    console.log("Can not find language file.\n");
else
    var lang = sitemap.lang;

var browser = {
    version: (_ua.match( /.+(?:me|ox|on|rv|it|era|opr|ie)[\/: ]([\d.]+)/ ) || [0,'0'])[1],
    opera: (/opera/i.test(_ua) || /opr/i.test(_ua)),
    msie: (/msie/i.test(_ua) && !/opera/i.test(_ua) || /trident\//i.test(_ua)),
    msie6: (/msie 6/i.test(_ua) && !/opera/i.test(_ua)),
    msie7: (/msie 7/i.test(_ua) && !/opera/i.test(_ua)),
    msie8: (/msie 8/i.test(_ua) && !/opera/i.test(_ua)),
    msie9: (/msie 9/i.test(_ua) && !/opera/i.test(_ua)),
    mozilla: /firefox/i.test(_ua),
    chrome: /chrome/i.test(_ua),
    safari: (!(/chrome/i.test(_ua)) && /webkit|safari|khtml/i.test(_ua)),
    iphone: /iphone/i.test(_ua),
    ipod: /ipod/i.test(_ua),
    iphone4: /iphone.*OS 4/i.test(_ua),
    ipod4: /ipod.*OS 4/i.test(_ua),
    ipad: /ipad/i.test(_ua),
    android: /android/i.test(_ua),
    bada: /bada/i.test(_ua),
    mobile: /iphone|ipod|ipad|opera mini|opera mobi|iemobile|android/i.test(_ua),
    msie_mobile: /iemobile/i.test(_ua),
    safari_mobile: /iphone|ipod|ipad/i.test(_ua),
    opera_mobile: /opera mini|opera mobi/i.test(_ua),
    opera_mini: /opera mini/i.test(_ua),
    mac: /mac/i.test(_ua),
    search_bot: /(yandex|google|stackrambler|aport|slurp|msnbot|bingbot|twitterbot|ia_archiver|facebookexternalhit)/i.test(_ua)
};

function re(elem) { return elem.parentNode ? elem.parentNode.removeChild(elem) : elem; }

function ge(el) { return (typeof el == 'string' || typeof el == 'number') ? document.getElementById(el) : el; }

function geByTag(searchTag, node) {
    node = ge(node) || document;
    return node.getElementsByTagName(searchTag);
}

function geByClass(searchClass, node, tag) {
    node = ge(node) || document;
    tag = tag || '*';
    var classElements = [];

    if (!browser.msie8 && node.querySelectorAll && tag != '*') {
        return node.querySelectorAll(tag + '.' + searchClass);
    }
    if (node.getElementsByClassName) {
        var nodes = node.getElementsByClassName(searchClass);
        if (tag != '*') {
            tag = tag.toUpperCase();
            for (var i = 0, l = nodes.length; i < l; ++i) {
                if (nodes[i].tagName.toUpperCase() == tag) {
                    classElements.push(nodes[i]);
                }
            }
        } else {
            classElements = Array.prototype.slice.call(nodes);
        }
        return classElements;
    }

    var els = geByTag(tag, node);
    var pattern = new RegExp('(^|\\s)' + searchClass + '(\\s|$)');
    for (var i = 0, l = els.length; i < l; ++i) {
        if (pattern.test(els[i].className)) {
            classElements.push(els[i]);
        }
    }
    return classElements;
}

function geByClass1(searchClass, node, tag) {
    node = ge(node) || document;
    tag = tag || '*';
    return !browser.msie8 && node.querySelector && node.querySelector(tag + '.' + searchClass) || geByClass(searchClass, node, tag)[0];
}

function serialize(form) {
    if (!form || form.nodeName !== "FORM") {
        return;
    }
    var i, j, q = [];
    for (i = form.elements.length - 1; i >= 0; i = i - 1) {
        if (form.elements[i].name === "") {
            continue;
        }
        switch (form.elements[i].nodeName) {
            case 'INPUT':
                switch (form.elements[i].type) {
                    case 'text':
                    case 'hidden':
                    case 'password':
                    case 'button':
                    case 'reset':
                    case 'submit':
                        q.unshift(form.elements[i].name + "=" + encodeURIComponent(form.elements[i].value));
                        break;
                    case 'checkbox':
                    case 'radio':
                        if (form.elements[i].checked) {
                            q.unshift(form.elements[i].name + "=" + encodeURIComponent(form.elements[i].value));
                        }
                        break;
                    case 'file':
                        break;
                }
                break;
            case 'TEXTAREA':
                q.unshift(form.elements[i].name + "=" + encodeURIComponent(form.elements[i].value));
                break;
            case 'SELECT':
                switch (form.elements[i].type) {
                    case 'select-one':
                        q.unshift(form.elements[i].name + "=" + encodeURIComponent(form.elements[i].value));
                        break;
                    case 'select-multiple':
                        for (j = form.elements[i].options.length - 1; j >= 0; j = j - 1) {
                            if (form.elements[i].options[j].selected) {
                                q.unshift(form.elements[i].name + "=" + encodeURIComponent(form.elements[i].options[j].value));
                            }
                        }
                        break;
                }
                break;
            case 'BUTTON':
                switch (form.elements[i].type) {
                    case 'reset':
                    case 'submit':
                    case 'button':
                        q.unshift(form.elements[i].name + "=" + encodeURIComponent(form.elements[i].value));
                        break;
                }
                break;
        }
    }
    return q.join("&");
}

function ie (parent, create, before) { parent.insertBefore(create, before); }

popup = {
    value: '',
    allPath: '',
    showStaticFile: function (el) {
        var act = 'generic_static',
            src = 'bitrix/admin/sitemap_ajax',
            el = ge('pop-up-window'),
            l = ge('layer_fixed');

        l.style.display = 'block';
        document.body.style.overflow = 'hidden';
        el.style.display = 'block';

        this.show(src, act);
    },
    close: function () {
        ge('layer_fixed').style.display = 'none';
        ge('pop-up-window').style.display = 'none';
        ge('bx-admin-prefix').style.overflow = '';
    },
    show: function (src, act, el, name, section) {
        var name = name || '',
            el = el || '',
            section = section || '';
        ge('ppw-notification').innerHTML = '';
        ge('ppw-notification').style.display = 'none';

        $.ajax({
            url: "/" + src + ".php",
            type: "POST",
            data: 'name=' + name + '&section=' + section + '&act=' + act,
            beforeSend: function (){
                ge('ppw-text').innerHTML = '<div style="text-align: center"><img src="/bitrix/images/zionec.sitemap/upload.gif"></div><div style="text-align: center"><strong>' + lang.loadMsg + '</strong></div>';
            },
            success: function (result) {
                var objData = JSON.parse(result);
                if(objData.error !== undefined) {
                    ge('ppw-notification').innerHTML = objData.error;
                    ge('ppw-notification').style.display = 'block';
                    ge('ppw-text').innerHTML = objData.text;
                } else {
                    if(objData.path)
                        ge('title-ppw').innerText = objData.path;

                    ge('ppw-text').innerHTML = objData.text;
                }
            }
        });
    },
    showTooltip: function (el, type) {
        /* el - элемент
         * type: 0 - удлаить в статических файлах
         *      1 - сохрнанить статические файлы
         *      2 - дата модификации
         *      3 - приоритет
         *      4 - частота модификаций
         */
        var pw = el.offsetWidth,
            n = document.createElement('div'),
            msg,
            parent,
            wi,
            ml,
            mt,
            c;

        n.id = 'js-tooltip';

        if (type == 0) {
            msg = lang.deleteFolder;
            parent = el.parentNode;
            ne = el.parentNode;
            wi = 160;
            ml = 70;
            mt = - 40;
            c = 'one_tr_d';
        } else if (type == 1) {
            msg = lang.saveChanges;
            parent = el.parentNode;
            ne = el.parentNode;
            wi = 220;
            ml = pw;
            mt = - 40;
            c = 'one_tr_d';
        } else if (type == 2) {
            msg = lang.modDate;
            parent = el.parentNode.parentNode.parentNode.parentNode;
            ne = parent;
            wi = 220;
            ml = -320;
            mt = -60;
            c = 'one_tr_d';
        } else if (type == 3) {
            msg = lang.priority;
            parent = el.parentNode.parentNode.parentNode.parentNode;
            ne = parent;
            wi = 220;
            ml = -440;
            mt = -60;
            c = 'one_tr_d';
        } else if(type == 4) {
            msg = lang.freq;
            parent = el.parentNode.parentNode.parentNode.parentNode;
            ne = parent;
            wi = 220;
            ml = -190;
            mt = -60;
            c = 'one_tr_d';
        }

        n.style.width = wi + 'px';
        n.style.marginLeft = - ml + 'px';
        n.style.marginTop = mt + 'px';
        ie(ne, n, parent.firstChild);
        n.innerHTML = msg + '<div class=' + c + '></div>';
    },
    hideTooltip: function (el) {
        re(ge(el));
    },
    delete: function (el, parent) {
        var e = el.parentNode.parentNode,
            c = geByClass('row-property',ge('ppw-text'), 'div').length / 2;
        re(e);
        /* если с равен 0, значит мы не во всплывающем окне */
        if (c == 0) {
            var c_ = geByClass('row-property',parent, 'div').length / 2;
            if (c_ == 0) {
                re(parent);
            }
        }
        /* мы во всплывающем окне */
        if (c == 1) {
            popup.close();
        }
    },
    send: function (form, src, act, callback, display, param) {
        var form = ge(form) || document.getElementsByName('form')[0],
            data = serialize(form),
            display = (display == '') ? 0 : 1;

        if (display == 1) {
            var el = ge('pop-up-window'),
                l = ge('layer_fixed');

            l.style.display = 'block';
            document.body.style.overflow = 'hidden';
            el.style.display = 'block';
        }

        $.ajax({
            url: "/" + src + ".php",
            type: "POST",
            data: data + '&act=' + act + '&param=' + param,
            beforeSend: function (){
                ge('ppw-text').innerHTML = '<div style="text-align: center"><img src="/bitrix/images/zionec.sitemap/upload.gif"></div><div style="text-align: center"><strong>' + lang.loadMsg + '</strong></div>';
            },
            success: function (result) {
                var objData = JSON.parse(result);
                if(objData.error !== undefined)
                    ge('ppw-notification').innerHTML = objData.error;
                else {
                    if(typeof(callback) == "function")
                        callback(objData);
                    else
                        popup.reloadForm(objData.title);
                }
            }
        });
    },
    reloadForm: function (title) {
        if(title !== undefined)
            ge('title-ppw').innerText = title;
        else
            ge('title-ppw').innerText = lang.successSave;

        ge('ppw-text').innerHTML = lang.successSave;
        setTimeout(
            function() {
                popup.close();
            }, 2000)
    },
    showTooltipDisplay: function (el, width, top) {
        var e = el.nextElementSibling,
            w = el.offsetWidth,
            h = el.offsetHeight,
            nh = top || h,
            nw = width || w;

        e.style.display = 'block';
        e.style.width = nw - 20 + 'px';
        e.style.marginTop =  - (nh*2) - 10 + 'px';
        if (width)
            e.style.marginLeft = -width/2/2 + 'px';
    },
    hideTooltipDisplay: function (el) {
        var e = el.nextElementSibling;
        e.style.display = 'none';
    },
    updateStaticLocalFile: function (type) {
        var parent = geByClass1('adm-cell','','div'),
            act = 'update_static_local_file',
            src = 'bitrix/admin/sitemap_ajax';
        $.ajax({
            url: "/" + src + ".php",
            type: "POST",
            data: 'act=' + act + '&type=' + type,
            success: function (result) {
                popup.close();
                parent.innerHTML = result;
            }
        });
    },
    updateIblockLocal: function (type) {
        var parent = geByClass1('adm-cell-iblock','','div'),
            act = 'update_iblock_local',
            src = 'bitrix/admin/sitemap_ajax';
        $.ajax({
            url: "/" + src + ".php",
            type: "POST",
            data: 'act=' + act + '&type=' + type,
            success: function (result) {
                popup.close();
                parent.innerHTML = result;
            }
        });
    },
    showIblock: function () {
        var act = 'generic_iblock',
            src = 'bitrix/admin/sitemap_ajax',
            el = ge('pop-up-window'),
            l = ge('layer_fixed');

        ge('title-ppw').innerHTML = lang.chooseInfoblock;

        l.style.display = 'block';
        document.body.style.overflow = 'hidden';
        el.style.display = 'block';

        this.show(src, act);
    },
    generation: function () {
        var act = 'generic',
            src = 'bitrix/admin/sitemap_ajax',
            el = ge('pop-up-window'),
            l = ge('layer_fixed');

        ge('title-ppw').innerHTML = lang.generation;

        l.style.display = 'block';
        document.body.style.overflow = 'hidden';
        el.style.display = 'block';

        this.show(src, act);
    },
    showHelp: function () {
        var act = 'help',
            src = 'bitrix/admin/sitemap_ajax',
            el = ge('pop-up-window'),
            l = ge('layer_fixed');

        l.style.display = 'block';
        document.body.style.overflow = 'hidden';
        el.style.display = 'block';

        this.show(src, act);
    }
};