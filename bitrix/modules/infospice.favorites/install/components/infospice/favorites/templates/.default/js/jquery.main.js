// page init
jQuery(function () {
    initSortable();
    initDragAndDrop();
    initControlBtns();
    initOpenForm();
});

function initControlBtns() {
    var activeClass = 'opened';
    var panel = jQuery('#favorites-model');
    panel.find('.fm_controls > ul > li').each(function () {
        var item = jQuery(this);
        var opener = item.find('a.infospice-favorite-opener');
        var btnAdd = item.find('a.infospice-favorite-btn-add');
        var drop = item.find('.infospice-favorite-drop');
        if (opener.length && drop.length) {
            item.bind('mouseenter', function () {
                if (panel.data('state') === 'stop') {
                    jQuery('body').bind('mousemove.cBtns', function (e) {
                        var offsetL = panel.offset().left, offsetT = panel.offset().top;
                        if (panel.hasClass(activeClass)) {
                            var panelW = drop.offset().left === offsetL ? drop.width() : panel.width() + drop.width();
                            var panelH = drop.offset().top === offsetT ? drop.height() : panel.height() + drop.height();
                            if (drop.offset().top < offsetT) {
                                offsetT = drop.offset().top;
                            }
                            if (drop.offset().left < offsetL) {
                                offsetL = drop.offset().left;
                            }
                            if (offsetL - 15 > e.pageX || offsetL + panelW + 15 < e.pageX || offsetT + panelH + 15 < e.pageY || offsetT - 15 > e.pageY) {
                                panel.removeClass(activeClass);
                            }
                        }
                    });
                    panel.addClass(activeClass);
                }

            });

            drop.find('.infospice-favorite-close').bind('click', function (e) {
                panel.removeClass(activeClass);
                jQuery('body').unbind('mousemove.cBtns');
                e.preventDefault();
            });
        }

        if (btnAdd.length) {
            btnAdd.bind('click', function (e) {
                AddCurrentPageToFavorite();
                e.preventDefault();
            });
        }
    });
}


function initOpenForm() {
    var animSpeed = 500, loadedClass = 'loaded';
    jQuery('div.infospice-favorite-add-new-item').each(function () {
        var holder = jQuery(this);
        var opener = holder.find('.infospice-favorite-btn-open');
        var loadHolder = holder.find('.infospice-favorite-load-holder');
        var addForm = loadHolder.find('.infospice-favorite-add-form').hide();
        opener.bind('click', function () {
            if (!holder.hasClass(loadedClass)) {
                holder.addClass(loadedClass);
                addForm.slideDown(animSpeed);
            } else {
                holder.removeClass(loadedClass);
                addForm.slideUp(animSpeed);
            }
            return false;
        });
    });
}

function initSortable() {
    jQuery('#favorites-model .infospice-favorite-menu').each(function () {
        var holder = jQuery(this);
        var items = holder.find('> li');
        var countItems = items.length;
        var itemsPosition = [];
        items.each(function (n) {
            items.eq(n).attr('data-index', n);
        });
        var itemsPos = getCookie('itemsPosition');
        // console.log(itemsPos);
        if (itemsPos !== null) {
            itemsPosition = itemsPos.split(',');
            var cnt = itemsPosition.length;
            for (var i = cnt - 1; i >= 0; i--) {
                var item = items.eq(itemsPosition[i]);
                if (item.length) {
                    holder.prepend(item);
                }
            }
        }

        holder.sortable({
            handle: '.infospice-favorite-handle',
            stop: function (event, ui) {
                itemsPosition = [];
                items = holder.find('> li');
                items.each(function (n) {
                    itemsPosition.push(items.eq(n).attr('data-index'));
                });
                setCookie('itemsPosition', itemsPosition.join(), 30);
            }
        });
    });
}


function getCookie(name) {
    var start = document.cookie.indexOf(name + "=");
    var len = start + name.length + 1;
    if ((!start) && (name != document.cookie.substring(0, name.length))) {
        return null;
    }
    if (start == -1)
        return null;
    var end = document.cookie.indexOf(';', len);
    if (end == -1)
        end = document.cookie.length;
    return unescape(document.cookie.substring(len, end));
}
function setCookie(name, value, expires, path, domain, secure) {
    var today = new Date();
    today.setTime(today.getTime());
    if (expires) {
        expires = expires * 1000 * 60 * 60 * 24;
    }
    var expires_date = new Date(today.getTime() + (expires));
    document.cookie = name + '=' + escape(value) +
        ((expires) ? ';expires=' + expires_date.toGMTString() : '') + //expires.toGMTString()
        ((path) ? ';path=' + path : '') +
        ((domain) ? ';domain=' + domain : '') +
        ((secure) ? ';secure' : '');
}
function deleteCookie(name, path, domain) {
    if (getCookie(name))
        document.cookie = name + '=' +
            ((path) ? ';path=' + path : '') +
            ((domain) ? ';domain=' + domain : '') +
            ';expires=Thu, 01-Jan-1970 00:00:01 GMT';
}

function initDragAndDrop() {
    var win = jQuery(window);
    jQuery('#favorites-model').dragAndDrop({
        handle: '.infospice-favorite-panel-handle',
        onInit: function (self) {
            //var position = getCookie('panelPosition');
            self.controls = self.dragBlock.find('.fm_controls');
            self.drop = self.controls.find('> ul > li .infospice-favorite-drop');

            if (typeof window.itemsPos !== "undefined") {
                var position = window.itemsPos;
            }
            if (position !== null) {
                // position = jQuery.parseJSON(position);
                var winW = win.width() < jQuery('body').width() ? win.width() - 30 : win.width();
                var winH = win.height() < jQuery('body').height() ? win.height() - 30 : win.height();

                if (position.xPos < 0 || position.xPos > $(window).width()) {
                    position.xPos = 100;
                }
                //console.log(allowPositionY);

                if (position.yPos < 0 || position.yPos > $(window).height()) {
                    position.yPos = 100;
                }

                self.dragBlock.css({
                    top: parseInt((position.yPos * winH) / position.pageHeight),
                    left: parseInt((position.xPos * winW) / position.pageWidth)
                });

                if (position.controlsClasses != '') {
                    self.controls.attr('class', position.controlsClasses);
                    self.drop.attr('class', position.dropClasses);
                }
            } else {
                self.dragBlock.css({
                    top: 50,
                    left: win.width() - self.dragBlock.width()
                });
                self.controls.addClass('right-side');
            }
            if (self.drop.length) {
                self.dragBlock.addClass('opened');
                self.drop.data('width', self.drop.width());
                self.dragBlock.removeClass('opened');
            }
        },
        onMove: function (self, e) {
            var winWidth = win.width(),
                winHeight = win.height(),
                dragWidth = self.dragBlock.width(),
                dragHeight = self.dragBlock.height(),
                dropHeight = self.drop.height();

            if (winHeight - 100 < e.clientY && (winWidth - dragWidth > e.clientX)) {
                self.drop.removeClass('drop-bottom');
                self.controls.removeClass('top-side').removeClass('right-side').addClass('bottom-side');
                if (winWidth - self.drop.data('width') - dragWidth < e.clientX) {
                    self.drop.addClass('drop-right');
                } else {
                    self.drop.removeClass('drop-right');
                }
            } else if (20 >= e.clientY && (winWidth - dragWidth > e.clientX)) {
                self.drop.removeClass('drop-bottom');
                if (winWidth - self.drop.data('width') - dragWidth < e.clientX) {
                    self.drop.addClass('drop-right');
                } else {
                    self.drop.removeClass('drop-right');
                }
                self.controls.removeClass('bottom-side').removeClass('right-side').addClass('top-side');
            } else if (winWidth - dragWidth > e.clientX && winWidth - dragWidth - self.drop.data('width') < e.clientX && winHeight - dragHeight > e.clientY) {
                self.drop.removeClass('drop-right');
                self.controls.removeClass('top-side').removeClass('bottom-side');
                self.controls.addClass('right-side');
                if (winHeight - dropHeight < e.clientY) {
                    self.drop.addClass('drop-bottom');
                } else {
                    self.drop.removeClass('drop-bottom');
                }
            } else {
                self.drop.removeClass('drop-right');
                self.controls.removeClass('right-side').removeClass('top-side').removeClass('bottom-side');
                if (winHeight - dropHeight < e.clientY) {
                    self.drop.addClass('drop-bottom');
                } else {
                    self.drop.removeClass('drop-bottom');
                }
            }
            self.dragBlock.removeClass('opened');
        },
        onEnd: function (self, e) {
            var winHeight = win.height();
            //var allowPositionX = self.dragBlock.offset().left;
            var allowPositionX = self.dragBlock.css('left');
            var allowPositionY = self.dragBlock.css('top');
            //var allowPositionY = self.dragBlock.offset().top > winHeight ? winHeight - self.dragBlock.height() : self.dragBlock.offset().top - self.dragBlock.height();
            if (allowPositionX < 0 || allowPositionX > $(window).width()) {
                allowPositionX = 100;
            }
            //console.log(allowPositionY);

            if (allowPositionY < 0 || allowPositionX > $(window).height()) {
                allowPositionY = 100;
            }

            var position = {
                xPos: allowPositionX,
                yPos: allowPositionY,
                pageWidth: win.width(),
                pageHeight: win.height(),
                dropClasses: self.drop.attr('class'),
                controlsClasses: self.controls.attr('class')
            }
            //setCookie('panelPosition', jQuery.toJSON(position), 30);

            $.ajax({
                data: position,
                url: sComponentPath + '/save_position.php',
                success: function (data) {
                }
            });
        }
    });
}

// Drag and drop
function DragAndDrop(options) {
    this.settings = jQuery.extend({
        handle: false,
        zIndex: 9999,
        constrains: false,
        onInit: false,
        onStart: false,
        onMove: false,
        onEnd: false
    }, options);
    this.init();
}

DragAndDrop.prototype = {
    init: function () {
        if (this.settings.holder) {
            this.findElenents();
            this.attachEvents();
            if (typeof this.settings.onInit === "function") {
                this.settings.onInit(this);
            }
        }
    },
    findElenents: function () {
        this.dragBlock = jQuery(this.settings.holder);
        this.dragBlock.data('state', 'stop');
        this.page = jQuery('body')

        if (this.settings.handle) {
            this.dragElement = this.dragBlock.find(this.settings.handle);
        } else {
            this.dragElement = this.dragBlock;
        }
    },
    attachEvents: function () {
        var self = this;
        var t;
        self.dragElement.bind('mousedown', this.bindScope(this.startDrag));
        jQuery(window).bind('resize', function () {
            if (typeof self.settings.onInit === "function") {
                self.settings.onInit(self);
            }
        });
    },
    startDrag: function (e) {
        var self = this;
        self.currentTime = new Date().getTime();
        this.page.addClass('no_selection').bind('mousemove.dd', this.bindScope(this.moveDrag)).bind('mouseup.dd', this.bindScope(this.endDrag));
        $(document).bind('mouseup.dd',function (e) {
            self.endDrag(e);
        }).bind('mousemove.dd', function (e) {
                e.preventDefault();
            });
        document.ondragstart = function () {
            return false
        }
        document.body.onselectstart = function () {
            return false;
        }
        self.page.css({cursor: 'move'});
        return false;
    },
    moveDrag: function (e) {

        var self = this;
        var winWidth = jQuery(window).width();
        var winHeight = jQuery(window).height();
        if ((e.clientY < (winHeight - self.dragBlock.height()) && e.clientX < (winWidth - self.dragBlock.width()))) {
            self.dragBlock.css({
                left: e.clientX,
                top: e.clientY
            });
            if (typeof self.settings.onMove === "function") {
                self.settings.onMove(self, e);
            }
            self.dragBlock.data('state', 'move');
        }
        return false;
    },
    endDrag: function (e) {
        var self = this;
        document.body.onselectstart = null;
        document.ondragstart = null;
        self.page.removeClass('no_selection').css({cursor: 'auto'});
        self.page.unbind('mousemove.dd');
        self.page.unbind('mouseup.dd');
        $(document).unbind('mouseup.dd').unbind('mousemove.dd');
        ;
        if (typeof self.settings.onEnd === "function") {
            self.settings.onEnd(self, e);
        }
        self.lastTime = new Date().getTime();
        this.dragBlock.data('state', 'stop');
        return false;
    },
    bindScope: function (func, scope) {
        return $.proxy(func, scope || this);
    }
}

// jquery plugin
jQuery.fn.dragAndDrop = function (opt) {
    return this.each(function () {
        jQuery(this).data('DragAndDrop', new DragAndDrop(jQuery.extend(opt, {holder: this})));
    });
}


;
(function ($) {
    var escapeable = /["\\\x00-\x1f\x7f-\x9f]/g, meta = {'\b': '\\b', '\t': '\\t', '\n': '\\n', '\f': '\\f', '\r': '\\r', '"': '\\"', '\\': '\\\\'};
    $.toJSON = typeof JSON === 'object' && JSON.stringify ? JSON.stringify : function (o) {
        if (o === null) {
            return'null';
        }
        var type = typeof o;
        if (type === 'undefined') {
            return undefined;
        }
        if (type === 'number' || type === 'boolean') {
            return'' + o;
        }
        if (type === 'string') {
            return $.quoteString(o);
        }
        if (type === 'object') {
            if (typeof o.toJSON === 'function') {
                return $.toJSON(o.toJSON());
            }
            if (o.constructor === Date) {
                var month = o.getUTCMonth() + 1, day = o.getUTCDate(), year = o.getUTCFullYear(), hours = o.getUTCHours(), minutes = o.getUTCMinutes(), seconds = o.getUTCSeconds(), milli = o.getUTCMilliseconds();
                if (month < 10) {
                    month = '0' + month;
                }
                if (day < 10) {
                    day = '0' + day;
                }
                if (hours < 10) {
                    hours = '0' + hours;
                }
                if (minutes < 10) {
                    minutes = '0' + minutes;
                }
                if (seconds < 10) {
                    seconds = '0' + seconds;
                }
                if (milli < 100) {
                    milli = '0' + milli;
                }
                if (milli < 10) {
                    milli = '0' + milli;
                }
                return'"' + year + '-' + month + '-' + day + 'T' +
                    hours + ':' + minutes + ':' + seconds + '.' + milli + 'Z"';
            }
            if (o.constructor === Array) {
                var ret = [];
                for (var i = 0; i < o.length; i++) {
                    ret.push($.toJSON(o[i]) || 'null');
                }
                return'[' + ret.join(',') + ']';
            }
            var name, val, pairs = [];
            for (var k in o) {
                type = typeof k;
                if (type === 'number') {
                    name = '"' + k + '"';
                } else if (type === 'string') {
                    name = $.quoteString(k);
                } else {
                    continue;
                }
                type = typeof o[k];
                if (type === 'function' || type === 'undefined') {
                    continue;
                }
                val = $.toJSON(o[k]);
                pairs.push(name + ':' + val);
            }
            return'{' + pairs.join(',') + '}';
        }
    };
    $.evalJSON = typeof JSON === 'object' && JSON.parse ? JSON.parse : function (src) {
        return eval('(' + src + ')');
    };
    $.secureEvalJSON = typeof JSON === 'object' && JSON.parse ? JSON.parse : function (src) {
        var filtered = src.replace(/\\["\\\/bfnrtu]/g, '@').replace(/"[^"\\\n\r]*"|true|false|null|-?\d+(?:\.\d*)?(?:[eE][+\-]?\d+)?/g, ']').replace(/(?:^|:|,)(?:\s*\[)+/g, '');
        if (/^[\],:{}\s]*$/.test(filtered)) {
            return eval('(' + src + ')');
        } else {
            throw new SyntaxError('Error parsing JSON, source is not valid.');
        }
    };
    $.quoteString = function (string) {
        if (string.match(escapeable)) {
            return'"' + string.replace(escapeable, function (a) {
                var c = meta[a];
                if (typeof c === 'string') {
                    return c;
                }
                c = a.charCodeAt();
                return'\\u00' + Math.floor(c / 16).toString(16) + (c % 16).toString(16);
            }) + '"';
        }
        return'"' + string + '"';
    };
})(jQuery);