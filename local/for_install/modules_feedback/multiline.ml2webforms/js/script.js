"use strict";
var Ml2WebFormsEdit = {
    fieldsEditor: null,
    addButton: null,

    init: function() {

        var lang_list = 'input[name="type_list[]"]';

        var multiple_list = {};
        var input_typelist = document.querySelectorAll(lang_list);
        for (var i = 0; i < input_typelist.length; i++) {
            multiple_list[i]=input_typelist[i].value.split('|||');
        };

        var self = this;

        self.fieldsEditor = jQuery('#form1_ml2webforms_edit .fields');
        self.addButton = self.fieldsEditor.find('input.add');

        var initialFeilds = self.fieldsEditor.find('.field');
        initialFeilds.each(function(ind, el) {
            self.initField(jQuery(el));
        });

        var need_tr;

        var btn_save = {
            title: BX.message('JS_CORE_WINDOW_SAVE'),
            id: 'savebtn',
            name: 'savebtn',
            className: BX.browser.IsIE() && BX.browser.IsDoctype() && !BX.browser.IsIE10() ? '' : 'adm-btn-save',
            action: function () {
                var res_save = document.querySelectorAll("#selected_tabs option");
                multiple_list[need_tr] = [];
                var i;
                for (i = 0; i < res_save.length; i++) {
                    multiple_list[need_tr].push(res_save[i].value);
                };
                multiple_list[need_tr]=multiple_list[need_tr].join('|||');
                document.querySelectorAll('.fields .field').item(need_tr).querySelector(lang_list).value = multiple_list[need_tr];
                document.querySelectorAll('.fields .field').item(need_tr).querySelector('input[name="type_list_def[]"]').value = getElementIndex(document.querySelector("#selected_tabs option.def"))-1;
                this.parentWindow.Close();
            }
        };

        function get_list(key,def_arg=false) {
            var content_list = '';
            var check_def_item = document.querySelectorAll('input[name="type_list_def[]"]').item(key).value;

            if(!document.querySelector('.change_list.active').hasAttribute('data-eng')){
                lang_list = 'input[name="type_list[]"]';
            }else{
                lang_list = 'input[name="type_list_en[]"]';
            }
            multiple_list = {};
            input_typelist = document.querySelectorAll(lang_list);
            for (var i = 0; i < input_typelist.length; i++) {
                multiple_list[i]=input_typelist[i].value.split('|||');
            };


            if(multiple_list[key] && multiple_list[key]!=''){
                var step;
                var check_def;
                var def_elem = multiple_list[key];
                if(!Array.isArray(def_elem)){
                    def_elem = def_elem.split('|||');
                };
                /*
                if(def_elem.length>0){
                    content_list_def+='<div class="td_title_def">По-умолчанию</div>';
                };*/
                for (step = 0; step < def_elem.length; step++) {
                    if(check_def_item==step){
                        check_def = 'def';
                    }else{
                        check_def='';
                    };
                    content_list+='<option class="'+check_def+'" value="'+def_elem[step]+'">'+def_elem[step]+'</option>';
                };
            };
            if(def_arg){
                //return content_list_def;
            }else{
                return content_list;
            }
        };

        var Dialog = new BX.CDialog({
            title: "Редактировать список",
            content: '<form method="POST" action="/" class="change_list" id="change_list">\
<table width="100%" cellspacing="0">\
            <tbody>\
        <tr valign="center">\
         <td width="60%">\
            <select class="select" name="selected_tabs" id="selected_tabs" size="8" style="height: 190px; width: 100%;">\
            </select>\
        </td>\
        <td width="40%" align="center" class="change_list--buts">\
            <input type="button" name="tabs_up" id="tabs_up" class="button" value="Выше" title="Порядок показа колонок (выше)"  onclick=""><br>\
            <input type="button" name="tabs_down" id="tabs_down" class="button" value="Ниже" title="Порядок показа колонок: ниже" onclick=""><br>\
            <input type="button" name="tabs_rename" id="tabs_rename" class="button" value="Изменить"  onclick=""><br>\
            <input type="button" name="tabs_add" id="tabs_add" class="button" value="Добавить" title="Добавить новую вкладку" onclick=""><br>\
            <input type="button" name="tabs_delete" id="tabs_delete" class="button" value="Удалить" title="Удалить колонки из выбранных"  onclick=""><br>\
            <input type="button" name="tabs_def" id="tabs_def" class="button" value="По-умолчанию" title="По умолчанию"  onclick=""><br>\
            </td>\
            </tr>\
            </tbody></table>\
		</form>',
            icon: 'head-block',
            resizable: false,
            draggable: true,
            height: '240',
            width: '500',
            buttons: [btn_save, BX.CDialog.btnCancel]
        });

        function check_all_list() {
            var select = document.querySelectorAll("select[name='type[]']");
            var dig;
            var button = document.createElement('input');
            button.type='button';
            button.className='change_list';
            button.value = 'Редактировать список';
            function open_dialog() {
                document.querySelector('#selected_tabs').innerHTML =  get_list(need_tr);
                //document.querySelector('.td_def').innerHTML =  get_list(need_tr, true);
                Dialog.Show();
                check_change_list();
            }

            function event_for_but(elem) {
                need_tr =  getElementIndex(elem.parentNode)-1;
                var click_buts = document.querySelectorAll('.change_list');
                for (var i = 0; i < click_buts.length; i++) {
                    click_buts[i].classList.remove('active');
                };
                elem.classList.add('active');
                open_dialog();
            };
            for (dig = 0; dig < select.length; dig++) {
                var new_but = button.cloneNode(true);
                var new_but_en = button.cloneNode(true);
                new_but_en.value='ENG';
                new_but_en.setAttribute('data-eng','1');
                new_but.addEventListener('click',function (e) {
                    event_for_but(this);
                });
                new_but_en.addEventListener('click',function (e) {
                    event_for_but(this);
                });
                if(select[dig].value == 1 || select[dig].value == 6 || select[dig].value == 3) {
                    if(!select[dig].parentNode.querySelector('.change_list')) {
                        select[dig].parentNode.appendChild(new_but);
                        select[dig].parentNode.appendChild(new_but_en);
                    };
                };
                select[dig].addEventListener('change', function () {
                    var new_but = button.cloneNode(true);
                    new_but.addEventListener('click',function () {
                        event_for_but(this);
                    });
                    new_but_en.addEventListener('click',function () {
                        event_for_but(this);
                    });
                    var parent = this.parentNode;
                    var disable_type_val = document.querySelectorAll('.fields .field').item(getElementIndex(this.parentNode)-1);
                    var disable_type_val_in;
                    if(disable_type_val){
                        disable_type_val_in = disable_type_val.querySelector('select[name="value_type[]"]')
                    };
                    if(this.value == 1 || this.value == 6 || this.value == 3){
                        if(!parent.querySelector('.change_list')){
                            parent.appendChild(new_but);
                            parent.appendChild(new_but_en);
                            if(disable_type_val){
                                disable_type_val_in.value = 2;
                                disable_type_val_in.setAttribute('disabled','disabled');
                            }
                        }
                    }else{
                        if(parent.querySelector('.change_list[value=ENG]')){
                            parent.querySelector('.change_list[value=ENG]').remove();
                        };
                        if(parent.querySelector('.change_list')){
                            parent.querySelector('.change_list').remove();
                        };
                        if(disable_type_val){
                            disable_type_val_in.value = '';
                            disable_type_val_in.removeAttribute('disabled');
                        }
                    }
                })
            };
        };

        function getElementIndex(el) {
            if(el){
                if (!el.parentNode)
                    return null;
                var index = 1, cur = el;
                while ((cur = cur.previousSibling)) {
                    if (1 == cur.nodeType)
                        ++index;
                };
                return index;
            };
        };

        check_all_list();







        var select = document.querySelector('#selected_tabs');
        function check_change_list() {
            hide_buts();
        };
        function hide_buts() {
            var change_list_buts = document.querySelectorAll(".change_list--buts input");
            var dig;
            for (dig = 0; dig < change_list_buts.length; dig++) {
                change_list_buts[dig].setAttribute('disabled','');
                if(select.value){
                    if (select.length>0){
                        if(change_list_buts[dig].id=='tabs_add' || change_list_buts[dig].id=='tabs_rename' || change_list_buts[dig].id=='tabs_delete' || change_list_buts[dig].id=='tabs_def'){
                            change_list_buts[dig].removeAttribute('disabled');
                        }
                    };
                    if (select.length>1){
                        if(change_list_buts[dig].id=='tabs_up' || change_list_buts[dig].id=='tabs_down'){
                            change_list_buts[dig].removeAttribute('disabled');
                        }
                    };
                }
                if(change_list_buts[dig].id=='tabs_add'){
                    change_list_buts[dig].removeAttribute('disabled');
                }
            };
        };

        document.querySelector("#tabs_add").addEventListener('click',function () {
            var new_elem = window.prompt('Введите новый элемент');
            if(new_elem!=null && new_elem!=''){
                var option=document.createElement('option');
                option.value=new_elem;
                option.textContent = new_elem;
                select.append(option);
                select.lastElementChild.setAttribute('selected','selected');
                hide_buts();
                if(select.length==1){
                    check_def();
                };
            };
        });

        document.querySelector("#tabs_delete").addEventListener('click',function () {
            if(select.querySelector('option[value="'+select.value+'"]').classList.contains('def')){
                select.querySelectorAll('option').item(0).classList.add('def');
            };
            select.querySelector('option[value="'+select.value+'"]').remove();
            hide_buts();
        });

        document.querySelector("#tabs_rename").addEventListener('click',function () {
            var red_elem = window.prompt('Изменить имя',select.value);
            if(red_elem!=null && red_elem!=''){
                select.querySelector('option[value="'+select.value+'"]').textContent = red_elem;
                select.querySelector('option[value="'+select.value+'"]').value = red_elem;
                select.value = red_elem;
            };
        });

        document.querySelector("#tabs_up").addEventListener('click',function () {
            var def_opt = select.querySelector('option[value="'+select.value+'"]');
            var prev_opt = select.querySelectorAll('option').item(def_opt.index-1);
            if(def_opt.index!=0){
                select.insertBefore(def_opt,prev_opt);
            };
            if(def_opt.classList.contains('def')){
                check_def();
            };
        });

        document.querySelector("#tabs_down").addEventListener('click',function () {
            var def_opt = select.querySelector('option[value="'+select.value+'"]');
            var prev_opt = select.querySelectorAll('option').item(def_opt.index+2);
            select.insertBefore(def_opt,prev_opt);
            if(def_opt.classList.contains('def')){
                check_def();
            };
        });

        function check_def() {
            var def_all = select.querySelectorAll('option');
            var def_opt = select.querySelector('option[value="'+select.value+'"]');
            for (var i = 0; i < def_all.length; i++) {
                def_all[i].classList.remove('def');
            };
            def_opt.classList.add('def')
        }

        document.querySelector("#tabs_def").addEventListener('click',function () {
            check_def();
        });

        select.addEventListener('click',function () {
            if(this.value){
                hide_buts();
            };
        });



        self.addButton.on({
            click: function() {
                var field = self.fieldsEditor.find('.field').clone();
                field.find('input[type="text"]').val('');
                field.find('select option:selected').removeAttr('selected');
                field.find('.change_list').remove();
                self.initField(field);
                self.fieldsEditor.get(0).insertBefore(field.get(0), self.addButton.get(0));
                check_all_list();
            }
        });


    },

    addevent_list: function () {

    },
/*
    addField: function() {
        var self = this;

        var field = self.fieldsEditor.find('.field').clone();
        field.find('input[type="text"]').val('');
        field.find('select option:selected').removeAttr('selected');
        self.initField(field);
        self.fieldsEditor.get(0).insertBefore(field.get(0), self.addButton.get(0));
    },*/

    deleteField: function(field) {
        var self = this;

        if (self.fieldsEditor.find('.field').length > 1) {
            field.remove();
        }
    },

    initField: function(field) {
        var self = this;

        field.find('.del').on({
            click: function() {
                self.deleteField(field);
            }
        })
    }
};

jQuery(function() {
    Ml2WebFormsEdit.init();
});