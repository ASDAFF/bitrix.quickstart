WS.ProjectSettings = function (params) {
    var FIELD_TYPE_NUMERIC = 'number';
    var FIELD_TYPE_STRING = 'string';
    var FIELD_TYPE_LIST = 'list';
    var FIELD_TYPE_SIGN = 'sign';
    var FIELD_TYPE_USER = 'user';
    var FIELD_TYPE_USER_GROUP = 'user_group';
    var FIELD_TYPE_IBLOCK = 'iblock';

    var uniqual = 1;
    function getUniqual() {
        return uniqual++;
    }

    var mainVariants = params.variants;

    var ValueRenderManager = function () {
        var valueRenders = {};
        var dataOperations = {
            setData: function (data) {
                this.data = data;
            },
            getData: function () {
                return this.data;
            },
            onContentChange: function (f) {
                this.contentChange = f;
            }
        };

        // ---- field string >>>
        valueRenders[FIELD_TYPE_STRING] = [];
        var cls = valueRenders[FIELD_TYPE_STRING][0] = function () {};
        $.extend(cls.prototype, dataOperations, {
            render: function () {
                if (!this.dom) {
                    this.dom = $('<input />', {
                            type: 'text',
                            size: '25'
                        })[0];
                    var This = this;
                    $(this.dom).keyup(function () {
                        if (This.onChange && This.onChange.constructor == Function) {
                            This.onChange();
                        }
                    });
                }
                return this.dom;
            },
            remove: function () {
                $(this.render()).remove();
            },
            setData: function (data) {
                $(this.render()).val(data);
            },
            getData: function () {
                return $(this.render()).val();
            }
        });
        var cls = valueRenders[FIELD_TYPE_STRING][1] = function () {};
        $.extend(cls.prototype, dataOperations, {
            render: function () {
                if (!this.dom) {
                    this.dom = $('<div />')[0];
                    this.fieldsContent = $('<div />', {'style': 'margin-bottom: 3px;'}).appendTo(this.dom);
                    this.link = $('<a />', {
                        href: '#'
                    }).text(BX.message('ws_projectsettings_button_more'))[0];
                    $(this.link).appendTo(this.dom);
                    this.contentChange = function () {};
                    this.insertField();
                    var This = this;
                    $(this.link).click(function (e) {
                        e.preventDefault();
                        This.insertField();
                    });
                }
                return this.dom;
            },
            insertField: function (value) {
                var fAttrs = {
                    type: 'text', size: '25',
                    style: 'margin-top: 3px;'
                };
                if (value) {
                    fAttrs['value'] = value
                }
                var $field = $('<input />', fAttrs);
                var This = this;
                $field.keyup(function () {
                    if (This.onChange && This.onChange.constructor == Function) {
                        This.onChange();
                    }
                });
                $(this.fieldsContent).append($('<div />', {style:'margin-top:3px;'}).append($field));
                this.contentChange();
                if (This.onChange && This.onChange.constructor == Function) {
                    This.onChange();
                }
            },
            clear: function () {
                $(this.fieldsContent).empty();
                this.insertField();
            },
            remove: function () {
                $(this.render()).remove();
            },
            setData: function (data) {
                this.render();
                $(this.fieldsContent).empty();
                var This = this;
                if ($(data).size() == 0) {
                    This.insertField('');
                } else {
                    $.each(data, function (i, val) {
                        This.insertField(val);
                    });
                }
            },
            getData: function () {
                var data = [];
                $(this.fieldsContent).find('input[type="text"]').each(function () {
                    var val = $(this).val();
                    val && data.push(val);
                });
                return data;
            }
        });

        // ---- field numeric >>>
        valueRenders[FIELD_TYPE_NUMERIC] = [];
        var cls = valueRenders[FIELD_TYPE_NUMERIC][0] = function () {};
        $.extend(cls.prototype, dataOperations, valueRenders[FIELD_TYPE_STRING][0].prototype, {
            render: function () {
                if (!this.dom) {
                    this.dom = $('<input />', {
                            type: 'text',
                            size: '7'
                        })[0];
                    var This = this;
                    $(this.dom).keyup(function () {
                        if (This.onChange && This.onChange.constructor == Function) {
                            This.onChange();
                        }
                    });
                }
                return this.dom;
            }
        });
        var cls = valueRenders[FIELD_TYPE_NUMERIC][1] = function () {};
        $.extend(cls.prototype, dataOperations, valueRenders[FIELD_TYPE_STRING][1].prototype, {
            insertField: function (value) {
                var fAttrs = {
                    type: 'text', size: '7',
                    style: 'margin-top: 3px;'
                };
                if (value) {
                    fAttrs['value'] = value
                }
                var $field = $('<input />', fAttrs);
                var This = this;
                $field.keyup(function () {
                    if (This.onChange && This.onChange.constructor == Function) {
                        This.onChange();
                    }
                });
                $(this.fieldsContent).append($('<div />', {style:'margin-top:3px;'}).append($field));
                this.contentChange();
            }
        });

        // ---- field sing >>>
        valueRenders[FIELD_TYPE_SIGN] = [];
        var cls = valueRenders[FIELD_TYPE_SIGN][0] = function () {};
        $.extend(cls.prototype, dataOperations, {
            render: function () {
                if (!this.dom) {
                    this.dom = $('<input />', {
                            type: 'checkbox'
                        })[0];
                    var This = this;
                    $(this.dom).change(function () {
                        if (This.onChange && This.onChange.constructor == Function) {
                            This.onChange();
                        }
                    });
                }
                return this.dom;
            },
            remove: function () {
                $(this.render()).remove();
            },
            setData: function (data) {
                data ? $(this.render()).attr('checked', 'checked') : $(this.render()).removeAttr('checked');
            },
            getData: function () {
                return !!$(this.render()).attr('checked');
            }
        });

        // ---- field list >>>
        valueRenders[FIELD_TYPE_LIST] = [];
        var cls = valueRenders[FIELD_TYPE_LIST][0] = function () {};
        $.extend(cls.prototype, dataOperations, {
            render: function () {
                if (!this.dom) {
                    this.dom = $('<select />', {
                        })[0];
                    var This = this;
                    $(this.dom).change(function () {
                        if (This.onChange && This.onChange.constructor == Function) {
                            This.onChange();
                        }
                    });
                }
                return this.dom;
            },
            remove: function () {
                $(this.render()).remove();
            },
            setData: function (value, variants) {
                variants && this.setVariants(variants);
                $(this.render()).val(value);
            },
            getData: function () {
                return $(this.dom).val();
            },
            onAfterClearSelect: function () {
                $(this.render()).append($('<option />', {
                    value: ''
                }).text(BX.message('ws_projectsettings_empty_value')));
            },
            setVariants: function (variants) {
                var select = this.render();
                var val = $(select).val();
                $(select).empty();
                this.onAfterClearSelect();
                $.each(variants, function (value, name) {
                    if (!value && !name) {
                        return ;
                    }
                    $(select).append($('<option />', {value: value}).text(name));
                });
                $(this.render()).val(val);
            }
        });
        var cls = valueRenders[FIELD_TYPE_LIST][1] = function () {};
        $.extend(cls.prototype, dataOperations, valueRenders[FIELD_TYPE_LIST][0].prototype, {
            render: function () {
                if (!this.dom) {
                    this.dom = $('<select />', {
                            multiple: 'multiple',
                            size: 5
                        })[0];
                    var This = this;
                    $(this.dom).change(function () {
                        if (This.onChange && This.onChange.constructor == Function) {
                            This.onChange();
                        }
                    });
                }
                return this.dom;
            },
            onAfterClearSelect: function () {}
        });

        // ---- field user >>>
        valueRenders[FIELD_TYPE_USER] = [];
        var cls = valueRenders[FIELD_TYPE_USER][0] = function () {};
        $.extend(cls.prototype, dataOperations, {
            render: function () {
                if (!this.dom) {
                    var formName = 'form'+getUniqual();
                    var fieldName = 'user'+getUniqual();
                    var $field = $('<input />', {
                        type: 'text',
                        name: fieldName,
                        id: fieldName,
                        value: '',
                        size: 5,
                        maxlength: 10
                    });
                    this.dom = $('<form />', {
                        name: formName
                    })[0];
                    $(this.dom).append($field);
                    var $button = $('<input />', {
                        'type': 'button',
                        'name': 'FindUser',
                        'id': 'FindUser',
                        'value': ' ... '
                    });
                    $(this.dom).append($button);
                    $($button).click(function () {
                        window.open('/bitrix/admin/user_search.php?lang=ru&FN='+formName+'&FC='+fieldName, "", "scrollbars=yes,resizable=yes,width=760,height=500,top="+Math.floor((screen.height - 560)/2-14)+",left="+Math.floor((screen.width - 760)/2-5)+";")
                    });
                    var This = this;
                    function change() {
                        if (This.onChange && This.onChange.constructor == Function) {
                            This.onChange();
                        }
                    }
                    $($field).keyup(change);
                    $($button).click(change);
                }
                return this.dom;
            },
            remove: function () {
                $(this.render()).remove();
            },
            setData: function (value) {
                $(this.render()).find('input[type="text"]').val(value);
            },
            getData: function () {
                return $(this.render()).find('input[type="text"]').val();
            }
        });

        var cls = valueRenders[FIELD_TYPE_USER][1] = function () {};
        $.extend(cls.prototype, dataOperations, {
            render: function () {
                if (!this.dom) {
                    this.dom = $('<div />')[0];
                    this.fieldsContent = $('<div />', {'style': 'margin-bottom: 3px;'}).appendTo(this.dom);
                    this.link = $('<a />', {
                        href: '#'
                    }).text(BX.message('ws_projectsettings_button_more'))[0];
                    $(this.link).appendTo(this.dom);
                    this.contentChange = function () {};
                    this.insertField();
                    var This = this;
                    $(this.link).click(function (e) {
                        e.preventDefault();
                        This.insertField();
                    });
                }
                return this.dom;
            },
            clear: function () {
                $(this.fieldsContent).empty();
                this.insertField();
            },
            insertField: function (value) {
                var formName = 'form'+getUniqual();
                var fieldName = 'user'+getUniqual();
                value = value || '';
                var $field = $('<input />', {
                    type: 'text',
                    name: fieldName,
                    id: fieldName,
                    value: value,
                    size: 5,
                    maxlength: 10
                });
                var form = $('<form />', {
                    name: formName
                })[0];
                var fieldLine = $('<div />', {style: 'margin-top: 3px;'})[0];
                $(fieldLine).append(form);
                var $button = $('<input />', {
                    'type': 'button',
                    'name': 'FindUser',
                    'id': 'FindUser',
                    'value': ' ... '
                });
                $(form).append($field);
                $(form).append($button);
                $(fieldLine).appendTo(this.fieldsContent);
                var This = this;
                $button.click(function () {
                    window.open('/bitrix/admin/user_search.php?lang=ru&FN='+formName+'&FC='+fieldName, "", "scrollbars=yes,resizable=yes,width=760,height=500,top="+Math.floor((screen.height - 560)/2-14)+",left="+Math.floor((screen.width - 760)/2-5)+";")
                });
                var This = this;
                function change() {
                    if (This.onChange && This.onChange.constructor == Function) {
                        This.onChange();
                    }
                }
                $($field).keyup(change);
                $($button).click(change);
                this.contentChange();
            },
            remove: function () {
                $(this.render()).remove();
            },
            setData: function (data) {
                this.render();
                $(this.fieldsContent).empty();
                var This = this;
                $.each(data, function (i, val) {
                    This.insertField(val);
                });
            },
            getData: function () {
                var data = [];
                $(this.fieldsContent).find('input[type="text"]').each(function () {
                    var val = $(this).val()
                    val && data.push(val);
                });
                return data;
            }
        });

        // ---- field iblock >>>
        valueRenders[FIELD_TYPE_IBLOCK] = [];
        var cls = valueRenders[FIELD_TYPE_IBLOCK][0] = function () {
            this.setVariants(mainVariants[FIELD_TYPE_IBLOCK]);
        };
        $.extend(cls.prototype, valueRenders[FIELD_TYPE_LIST][0].prototype, {
            setData: function (value) {
                $(this.render()).val(value);
            }
        });
        var cls = valueRenders[FIELD_TYPE_IBLOCK][1] = function () {
            this.setVariants(mainVariants[FIELD_TYPE_IBLOCK]);
        };
        $.extend(cls.prototype, valueRenders[FIELD_TYPE_LIST][1].prototype, {
            setData: function (value) {
                $(this.render()).val(value);
            }
        });

        // ---- field user_groups >>>
        valueRenders[FIELD_TYPE_USER_GROUP] = [];
        var cls = valueRenders[FIELD_TYPE_USER_GROUP][0] = function () {
            this.setVariants(mainVariants[FIELD_TYPE_USER_GROUP]);
        };
        $.extend(cls.prototype, valueRenders[FIELD_TYPE_LIST][0].prototype, {
            setData: function (value) {
                $(this.render()).val(value);
            }
        });
        var cls = valueRenders[FIELD_TYPE_USER_GROUP][1] = function () {
            this.setVariants(mainVariants[FIELD_TYPE_USER_GROUP]);
        };
        $.extend(cls.prototype, valueRenders[FIELD_TYPE_LIST][1].prototype, {
            setData: function (value) {
                $(this.render()).val(value);
            }
        });

        $.each(params.customLists || [], function (name, variants) {
            valueRenders[name] = [];
            var cls = valueRenders[name][0] = function () {
                this.setVariants(variants);
            };
            $.extend(cls.prototype, valueRenders[FIELD_TYPE_LIST][0].prototype, {
                setData: function (value) {
                    $(this.render()).val(value);
                }
            });
            var cls = valueRenders[name][1] = function () {
                this.setVariants(variants);
            };
            $.extend(cls.prototype, valueRenders[FIELD_TYPE_LIST][1].prototype, {
                setData: function (value) {
                    $(this.render()).val(value);
                }
            });
        });

        return {
            getViewRenderByType: function (type, isMany) {
                var manyIndex = isMany ? 1 : 0;
                return new valueRenders[type][manyIndex]();
            }
        }
    }();

    var addFieldDialog = function () {

        // content reate
        var content = $('<div />', {
            'class': 'ws-project-settings-popup'
        })[0];
        var $line = $('<div class="line"><div class="label"></div><span class="input"></span></div>');

        var $label = $line.clone();
        $label.find('.input').append($('<input type="text" name="label" />'));
        $label.find('.label').text(BX.message('ws_projectsettings_popup_label_label'));
        $label.appendTo(content);
        var setLabel = function (value) {
            $label.find('input[name="label"]').val(value);
        };
        var getLabel = function () {
            return $label.find('input[name="label"]').val();
        }

        var $name = $label.clone();
        $name.find('input').attr('name', 'name');
        $name.find('.label').text(BX.message('ws_projectsettings_popup_label_name'));
        $name.appendTo(content);
        var setName = function (value) {
            $name.find('input[name="name"]').val(value);
        };
        var getName = function () {
            return $name.find('input[name="name"]').val();
        }

        var $sort = $label.clone();
        $sort.find('input').attr('name', 'sort');
        $sort.find('.label').text(BX.message('ws_projectsettings_popup_label_sort'));
        $sort.appendTo(content);
        var setSort = function (value) {
            $sort.find('input[name="sort"]').val(value);
        };
        var getSort = function () {
            return $sort.find('input[name="sort"]').val();
        };

        var $isMany = $line.clone();
        $isMany.find('.input').append('<input type="checkbox" name="isMany" />');
        $isMany.find('.label').text(BX.message('ws_projectsettings_popup_label_many'));
        $isMany.appendTo(content);
        var setMany = function (sign) {
            var $ch = $isMany.find('input[name="isMany"]');
            sign ? $ch.attr('checked', 'checked') : $ch.removeAttr('checked');
        }
        var isMany = function () {
            return !!$isMany.find('input[name="isMany"]').attr('checked');
        };
        $isMany.find('input[name="isMany"]').change(function () {
            initType();
        });

        var $type = $line.clone();
        $type.find('.label').text(BX.message('ws_projectsettings_popup_label_type'));
        var typeSelect = $('<select />', {
            name: 'type'
        });
        $.each(params.types, function (value, name) {
            $('<option />', {
                'value' : value
            }).text(name).appendTo(typeSelect);
        });
        $type.find('.input').append(typeSelect);
        $type.appendTo(content);
        var defaultViewRender = null;

        var setType = function (value) {
            $(typeSelect).val(value);
        }
        var getType = function () {
            return $(typeSelect).val();
        }

        $(typeSelect).change(function () {
            initType();
        });

        var $default = $line.clone();
        $default.find('.label').text(BX.message('ws_projectsettings_popup_label_default'));
        $default.appendTo(content);
        function setDefaultRender (el) {
            $(el).css('margin-left', '200px');
            $default.find('.input').append(el);
        };
        var setDefault = function (value, variants) {
            defaultViewRender.setData(value, variants);
        };
        var getDefault = function () {
            return defaultViewRender.getData();
        };

        var listVariants = function () {
            var $listValues = $('<div />', {
                'class': 'list-values'
            });
            $listValues.append('<div class="title">'+BX.message('ws_projectsettings_popup_listValues_title')+'</div>');
            var $listValuesHead = $(
                '<div class="line"><div class="value">'+BX.message('ws_projectsettings_popup_listValues_label_value')+'</div>'+
                '<div class="name">'+BX.message('ws_projectsettings_popup_listValues_label_label')+'</div>'+
                '</div>'
            );
            $listValues.append($listValuesHead);
            var $listValuesValuesLine = $(
                '<div class="line"><div class="value"><input type="text" name="listValues[][value]" /></div>'+
                '<div class="name"><input type="text" name="listValues[][name]" size="50"/></div>'+
                '</div>'
            );
            var $linesContainer = $('<div />').appendTo($listValues);
            var $linkListValuesMore = $('<a />', {
                href: '#'
            }).text(BX.message('ws_projectsettings_button_more'));
            $listValues.append($('<div />', {'class': 'line'}).append($linkListValuesMore));
            $listValues.appendTo(content);
            function hide() {
                $listValues.hide();
            }
            function show() {
                $listValues.show();
            }
            function addLine(name, value) {
                var $line = $listValuesValuesLine.clone();
                $line.find('.name input:first').val(name);
                $line.find('.value input:first').val(value);
                $linesContainer.append($line);
                $line.find('input').blur(function () {
                    defaultViewRender.setVariants(getVariants());
                });
                winReinit();
            }
            function clear(notAddLine) {
                $linesContainer.empty();
                !notAddLine && addEmptyLine();
                winReinit();
            }
            function setVariants(variants) {
                clear(true);
                $.each(variants, function(value, name) {
                    if (!name && !value) {
                        return ;
                    }
                    addLine(name, value);
                });
                addEmptyLine();
            }
            function getVariants() {
                var data = {};
                $linesContainer.children().each(function () {
                    var val = $(this).find('.value input:first').val();
                    var name = $(this).find('.name input:first').val();
                    if (val && name) {
                        data[val] = name;
                    }
                });
                return data;
            }
            function addEmptyLine() {
                addLine('', '');
            }
            $linkListValuesMore.click(function (e) {
                e.preventDefault();
                addEmptyLine();
            });
            clear();
            return {
                setVariants: setVariants,
                getVariants: getVariants,
                clear: clear,
                hide: hide,
                show: show
            };
        }();

        var lastType = null;
        var lastIsMany = null;
        function initType() {
            var type = $(typeSelect).val();
            if (type == FIELD_TYPE_LIST) {
                listVariants.show();
            } else {
                listVariants.hide();
                listVariants.clear();
            }
            if (type == FIELD_TYPE_SIGN) {
                setMany(false);
                $isMany.hide();
            } else {
                $isMany.show();
            }

            if (lastIsMany != isMany() || lastType != type) {
                if (lastType == type && type == FIELD_TYPE_LIST) {
                    var variants = listVariants.getVariants();
                }
                if (defaultViewRender) {
                    defaultViewRender.remove();
                }
                defaultViewRender = ValueRenderManager.getViewRenderByType(type, isMany());
                setDefaultRender(defaultViewRender.render());
                if (variants) {
                    defaultViewRender.setVariants(variants);
                }
                defaultViewRender.onContentChange(winReinit);
                lastIsMany = isMany();
                lastType = type;
            }
            winReinit();
        };

        var win = new BX.CDialog({
            'height': 250,
            'width': 650,
            'title': BX.message('ws_projectsettings_field_setup_win_title'),
            'content': content,
            'buttons': [
            {
                'title': BX.message('ws_projectsettings_apply_btn_title'),
                'action': function () {
                    funcOnSave();
                    win.Close();
                }
            }
            ,BX.CDialog.btnCancel
            ]
        });

        function winReinit() {
            if (win) {
                win.SetSize({
                    'width': 650,
                    'height': $(content).outerHeight()
                });
            }
        }
        initType();
        var funcOnSave = function () {};
        return {
            open: function (f) {
                win.Show();
                funcOnSave = f || function (){};
                initType();
            },
            setData: function (data) {
                setLabel(data.label);
                setName(data.name);
                setMany(data.isMany);
                setSort(data.sort);
                setType(data.type);
                if (data.variants && data.type == FIELD_TYPE_LIST) {
                    listVariants.setVariants(data.variants);
                }
                initType();
                setDefault(data['default'], data.variants);
            },
            getData: function () {
                var variants = '';
                if (getType() == FIELD_TYPE_LIST) {
                    variants = listVariants.getVariants();
                }
                return {
                    label: getLabel(),
                    name: getName(),
                    sort: getSort(),
                    isMany: isMany(),
                    type: getType(),
                    'default': getDefault(),
                    variants: variants
                };
            },
            clear: function () {
                setName('');
                setLabel('');
                setType(FIELD_TYPE_STRING);
                setMany(false);
                setSort('');
                initType();
                setDefault('');
            }
        };
    }();

    var newFieldButton = document.getElementById(params.newFieldButton);
    var toogleSimCodesButton = document.getElementById(params.toogleSimCodesButton);
    var mainContainer = $(newFieldButton).parents('table:first')[0];

    var rowItem = function (manager) {
        this.row = $('<tr><td valign="top" width="40%" class="ws-project-settings field-name"><a href="#" class="ws-project-settings remove">(x)</a>&nbsp;&nbsp;&nbsp;<a href="#" class="setup ws-project-settings">('+BX.message('ws_projectsettings_setup_field')+')</a>&nbsp;&nbsp;&nbsp;<label></label></td><td valign="middle" class="value"></td></tr>')[0];
        this.removeLink = $('a.remove', this.row);
        this.setupLink = $('a.setup', this.row);
        this.removed = false;
        var This = this;
        $(this.removeLink).click(function (e) {
            e.preventDefault();
            if (confirm(BX.message('ws_projectsettings_confirm_remove_field'))) {
                This.remove();
            }
        });
        $(this.setupLink).click(function (e) {
            e.preventDefault();
            manager.setupItem(This);
        });
        manager.subscribeToGetData(function (data) {
            if (!This.removed) {
                data[This.getData().name] = This.getData();
            }
        });
        manager.subscribeToDefault(function () {
            This.setAsDefult();
        });
        manager.subscribeToSort(function (els) {
            if (!This.removed) {
                //$(This.getDom()).remove();
                els.push(This);
            }
        });
        manager.subscribeToToggleCodes(function (isShow) {
            var $label = $('label:first', This.getDom());
            var text = This.getData().label;
            if (isShow) {
                text += ' ['+This.getData().name+']';
            }
            $label.text(text);
        });
        this.manager = manager;
    };

    $.extend(rowItem.prototype, {
        'initByData': function (data) {
            var tmpData = this.data;
            this.data = data;
            $('label:first', this.row).text(data.label+':');
            if (!tmpData || tmpData.type != this.data.type || tmpData.isMany != this.data.isMany) {
                this.viewRender && $(this.viewRender.render()).remove();
                this.viewRender = ValueRenderManager.getViewRenderByType(data.type, data.isMany);
                if (data.variants && data.type == FIELD_TYPE_LIST) {
                    this.viewRender.setVariants(data.variants);
                }
                data.value && this.viewRender.setData(data.value);
                $('td.value', this.row).append(this.viewRender.render());
                var This = this;
                this.viewRender.onChange = function () {
                    This.onChange();
                };
            }
            this.onChange();
        },
        'onChange': function () {
            this.manager.onFieldChange(this);
        },
        'getData': function () {
            var data = this.data;
            data.value = this.viewRender.getData();
            return data;
        },
        'getDom': function () {
            return this.row;
        },
        'remove': function () {
            $(this.getDom()).remove();
            this.removed = true;
            this.onChange();
        },
        'setAsDefult': function () {
            this.viewRender.setData(this.data['default']);
            this.onChange();
        },
        'getSort': function () {
            return this.getData().sort;
        }
    });

    var manager = {
        inited: false,
        getDataFunctions: [],
        setupDafaultFunctions: [],
        sortFunctions: [],
        toogleCodesFunctions: [],
        changedFieldFunctions: [],
        insertItem: function (data) {
            if (!data.value) {
                data.value = data['default'];
            }

            this.initItem(data);
            this.sort();
        },
        initItem: function (data) {
            var row = new rowItem(this);
            row.initByData(data);
        },
        setupItem: function (item) {
            addFieldDialog.clear();
            addFieldDialog.setData(item.getData());
            var This = this;
            addFieldDialog.open(function () {
                item.initByData(addFieldDialog.getData());
                This.sort();
            });
        },
        subscribeToGetData: function (f) {
            this.getDataFunctions.push(f);
        },
        subscribeToDefault: function (f) {
            this.setupDafaultFunctions.push(f);
        },
        subscribeToSort: function (f) {
            this.sortFunctions.push(f);
        },
        subscribeToFieldsChange: function(f) {
            this.changedFieldFunctions.push(f);
        },
        onFieldChange: function () {
            if (!this.inited) {
                return ;
            }
            $.each(this.changedFieldFunctions, function (i, f) {
                f();
            });
        },
        getData: function () {
            var data = {};
            $.each(this.getDataFunctions, function (i, f) {
                f(data);
            });
            return data;
        },
        toDefault: function () {
            $.each(this.setupDafaultFunctions, function (i, f) {
                f();
            });
        },
        sort: function () {
            var els = [];
            $.each(this.sortFunctions, function (i, f) {
                f(els);
            });
            els.sort(function (el1, el2) {
                return el2.getSort() - el1.getSort();
            });
            $.each(els, function (i, el) {
                $(el.getDom()).prependTo(mainContainer);
            });
        },
        subscribeToToggleCodes: function (f) {
            this.toogleCodesFunctions.push(f);
        },
        toggleSCodes: function (isShow) {
            $.each(this.toogleCodesFunctions, function (i, f) {
                f(isShow);
            });
        },
        showSCodes: function () {
            this.toggleSCodes(true);
        },
        hideSCodes: function () {
            this.toggleSCodes(false);
        }
    };

    $.each(params.fields, function (fName, fData) {
        manager.initItem(fData);
    });
    manager.sort();
    manager.inited = true;

    $(newFieldButton).click(function (e) {
        e.preventDefault();
        addFieldDialog.clear();
        addFieldDialog.open(function () {
            manager.insertItem(addFieldDialog.getData());
        });
    });

    var showedSCodes = false;
    $(toogleSimCodesButton).click(function (e) {
        e.preventDefault();
        showedSCodes = !showedSCodes;
        var text = BX.message(showedSCodes ? 'ws_projectsettings_toogle_sim_codes_hide_link' : 'ws_projectsettings_toogle_sim_codes_show_link');
        if (showedSCodes) {
            manager.showSCodes();
        } else {
            manager.hideSCodes();
        }
        $(this).text(text);
    });

    var submitButton = document.getElementById(params.submitButton);

    manager.subscribeToFieldsChange(function () {
        $(submitButton).removeAttr('disabled');
    });
    $(document.getElementById(params.defaultButton)).click(function (e) {
        e.preventDefault();
        if (confirm(BX.message('ws_projectsettings_confirm_all_default'))) {
            manager.toDefault();
            $(submitButton).click();
        }
    });

    $(document.getElementById(params.cancelButton)).click(function (e) {
        e.preventDefault();
        document.location.href = params.curUri;
    });

    $(submitButton).click(function (e) {
        e.preventDefault();
        var data = manager.getData();
        var form = $('<form />', {
            'method': 'post'
        });
        $.each(manager.getData(), function (name, data) {
            if (data.isMany) {
                $.each(data.value, function (i, value) {
                    if (!value) {
                        return ;
                    }
                    $(form).append($('<input />', {
                        type: 'hidden',
                        name: 'fields['+name+'][value][]',
                        value: value
                    }));
                });
                $.each(data['default'], function (i, value) {
                    if (!value) {
                        return ;
                    }
                    $(form).append($('<input />', {
                        type: 'hidden',
                        name: 'fields['+name+'][default][]',
                        value: value
                    }));
                });
            } else {
                if (data.type == FIELD_TYPE_SIGN) {
                    data['default'] = data['default'] ? 'Y' : 'N';
                    data['value'] = data['value'] ? 'Y' : 'N';
                }
                $(form).append($('<input />', {
                    type: 'hidden',
                    name: 'fields['+name+'][default]',
                    value: data['default']
                }));
                $(form).append($('<input />', {
                    type: 'hidden',
                    name: 'fields['+name+'][value]',
                    value: data.value
                }));
            }
            $(form).append($('<input />', {
                type: 'hidden',
                name: 'fields['+name+'][isMany]',
                value: data.isMany ? 'Y' : 'N'
            }));
            $(form).append($('<input />', {
                type: 'hidden',
                name: 'fields['+name+'][name]',
                value: name
            }));
            $(form).append($('<input />', {
                type: 'hidden',
                name: 'fields['+name+'][label]',
                value: data.label
            }));
            $(form).append($('<input />', {
                type: 'hidden',
                name: 'fields['+name+'][type]',
                value: data.type
            }));
            $(form).append($('<input />', {
                type: 'hidden',
                name: 'fields['+name+'][sort]',
                value: data.sort
            }));
            if (data.type == FIELD_TYPE_LIST) {
                $.each(data.variants, function (value, label) {
                    $(form).append($('<input />', {
                        type: 'hidden',
                        name: 'fields['+name+'][variants]['+value+']',
                        value: label
                    }));
                });
            }
        });
        $('body').append(form).submit();
        $(form).submit();
    });
};
