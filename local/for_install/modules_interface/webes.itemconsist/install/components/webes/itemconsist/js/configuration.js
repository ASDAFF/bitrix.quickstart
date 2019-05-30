var w_ic_ajax_path="/bitrix/components/webes/itemconsist/ajax.php";
document.addEventListener("DOMContentLoaded", function () {

jQuery("body").append('<div class="w-ic-modal-overlay"></div><div class="w-ic-modal-base"><div class="w-ic-modal-body"><div class="w-ic-modal-close w-ic-close">X</div><div class="w-ic-modal-title"></div><div class="w-ic-modal-inner"></div></div></div>');
    
jQuery(document).ready(function () {

        if(jQuery(".w-ic-admin_block").is("*"))
        {
            w_ic_view_info_all_elements();
        }

        jQuery(".ic-w-view_next_element").click(function(){
            jQuery(this).find("+").show();
            return false;
        });


    jQuery(".ic-w-help_options_iblocks").click(function(){
        w_ic_modal_show();
        jQuery(".w-ic-modal-title").html("Помощь");
        jQuery(".w-ic-modal-inner").html('<ul>' +
            '<li>При выборе инфоблока без раздела правила применяются ко всем разделам инфоблока.</li>' +
            '<li><b>Корр.коэф.</b> - корректирующий коэффициент. Расчётная цена товара умножается на этот коэффициент. Если хотите оставить без изменений - коэффициент должен быть равен 1.</li>' +
            '<li><b>Затухание</b> - если у вас несколько торговых предложений в товаре - можете установить затухание цены. Например, если затухание равно 10% - у предложения с наименьшей ценой скидка будет 0%, у предложения с наивысшей ценой - 10%.</li>' +
            '</ul>');
        return false;
    });

        if(jQuery(".ic-w-ib_setting_table_block").is("*"))
            ic_w_show_table_setting_iblock();

    if(jQuery(".iblock_id_select").is("select"))
    {
        jQuery.get(w_ic_ajax_path+"?act=get_iblocks",function(res){
            if(typeof(res)=='string')res=JSON.parse(res);
            if(res.result=='error'){alert(res.description);return false;}
            var h='<option>- выберите информационный блок -</option>';
            for(ib_id in res.ibs)
                {
                    h+='<option value="'+ib_id+'">'+res.ibs[ib_id]+'</option>';
                }

            jQuery(".iblock_id_select").html(h);
        });
    }

    jQuery(".ic-w-add_iblock_config").click(function(){
            var ib=parseInt(jQuery(".iblock_id_select").val());
            if(isNaN(ib) || ib == 0){alert("Укажите Информационный блок");return false;}
            var sid=parseInt(jQuery(".section_id_select").val());
            jQuery.get(w_ic_ajax_path+"?act=add_iblock_config&ib="+ib+"&sid="+sid,function(res){
                if(typeof(res)=='string')res=JSON.parse(res);
                if(res.result=='error'){alert(res.description);return false;}
                ic_w_show_table_setting_iblock();
            });

        });

    jQuery(".ic-w-recalc_all_button").click(function(){
        if(confirm("Пересчитать все цены?"))
        {
            jQuery(this).hide();
            jQuery(this).after('<div id="ic-w-waiting"><i>Пересчитываю цены...</i></div>');
            ic_w_recalc_go();
        }
    });


    if(jQuery(".ic-w-ingridients").is("*"))
    {
        ic_w_view_ingridients();
        jQuery(".ic-w-group-add-button").click(function(){
            var name_el=jQuery(this).parent().find("[name='grname']");
            name=name_el.val();
            if(name==''){alert("Задайте имя группы");return false;}
            jQuery.post(w_ic_ajax_path+"?act=add_group",{name:name},function (res) {
                if(typeof(res)=='string')res=JSON.parse(res);
                if(res.result=='error'){alert(res.description);return false;}
                jQuery(".ic-w-add-group-block").hide();
                ic_w_view_ingridients();
                name_el.val("");
            });
        });
    }

    jQuery(".ic-w-calc-item").click(function(){
        var item_id=jQuery(this).data("item_id");
        jQuery.get(w_ic_ajax_path+"?act=calc-item&item_id="+item_id,function(res){
            console.log(res);
        });
    });

}); // onready

    jQuery(".iblock_id_select").on("change",function(){
        var ib=jQuery(this).val();
        jQuery(".section_id_select").remove();
        jQuery.get(w_ic_ajax_path+"?act=get_sections_ibock&ib="+ib,function(res){
            if(typeof(res)=='string')res=JSON.parse(res);
            if(res.result=='error'){alert(res.description);return false;}
                var h='<select name="section_id" class="section_id_select"><option>Для всего ИБ</option>';
                for(id in res)
                    h+='<option value="'+id+'">'+res[id]+'</option>';
                h+='</select>';
                jQuery(".iblock_id_select").after(h);
        });
    });


});

function w_ic_view_info_all_elements()
{
    var ids=[],i=0;
    jQuery(".w-ic-admin_block").each(function(){
        ids[i++]=jQuery(this).data("element_id");
    });
    w_ic_element_statuses(ids);
}

function w_ic_element_statuses(ids)
{
    jQuery.get(w_ic_ajax_path+"?act=get_all_elements_statuses&ids="+ids.join(','),function(res){
        if(typeof(res)=='string')res=JSON.parse(res);
        if(res.result=='error'){alert(res.description);return false;}
            for(id in res.statuses)
            {
                var o=res.statuses[id];

                    var div=jQuery(".w-ic-admin_block[data-element_id='"+id+"']");
                    if(res.statuses[id]==1)div.html('<a href="" class="w-ic-admin_button" style="color:green;" data-element_id="'+id+'">все составы заданы</a>');
                    else div.html('<a href="" class="w-ic-admin_button" style="color:red;" data-element_id="'+id+'">не все составы заданы</a>');
            }

        jQuery(".w-ic-admin_button").click(function(){
            var price_param_id=parseInt(jQuery(this).parent().data("price_param_id"));
            if(isNaN(price_param_id))price_param_id=0;
            w_ic_view_element_redact(jQuery(this).data("element_id"),price_param_id);
            return false;
        });
    });
}


function w_ic_view_element_redact(el_id,price_param_id)
{
    jQuery.get(w_ic_ajax_path+"?act=get_element_data&element_id="+el_id,function(res){
    if(typeof(res)=='string')res=JSON.parse(res);
    if(res.result=='error'){alert(res.description);return false;}
    w_ic_modal_show();
    var ingridients_simple=ic_w_ingridients_simple(res.ingridients.groups);

    var html='<div class="w-ic-vew-next-consist-config" data-price_param_id="'+price_param_id+'" data-parent_id="0" data-id="'+el_id+'" data-consist=\''+JSON.stringify(res.consist)+'\'><b>'+res.name+'</b>'+w_ic_view_line_consist(res.id,res.consist,ingridients_simple)+'</div>';
        if(res.offers_exists)
        {
            for(id in res.offers)
            {
                html=html+'<div style="padding-left:28px;" class="w-ic-vew-next-consist-config"  data-price_param_id="'+price_param_id+'" data-parent_id="'+el_id+'" data-id="'+id+'" data-consist=\''+JSON.stringify(res.offers[id].consist)+'\'>'+res.offers[id].name+w_ic_view_line_consist(id,res.offers[id].consist,ingridients_simple)+'</div>';
            }
        }
        jQuery(".w-ic-modal-title").html("Задать состав наименования");
        jQuery(".w-ic-modal-inner").html(html);

        jQuery(".w-ic-vew-next-consist-config").click(function(){
            var th=jQuery(this);
            var price_param_id=th.data("price_param_id");
            if(th.find("+ .w-ic-view-next-ingridients").is(":visible"))return false;
            if(th.find("+ .w-ic-save-consist").is(":visible"))return false;
            var consist=th.data("consist");
            var id=th.data("id");
            var parent_id=th.data("parent_id");
            if(typeof(consist)=='string')consist=JSON.parse(consist);
            var h='';
            for(grid in res.ingridients.groups)
            {
                h+='<div class="w-ic-view-next-ingridients">'+res.ingridients.groups[grid].name+'</div>';
                h+='<div class="ic-w-hidden">';
                for(iid in res.ingridients.groups[grid].items)
                {
                    var item=res.ingridients.groups[grid].items[iid];
                    if(consist===null)consist=[];
                    h+='<div style="display:flex;justify-content: space-between;/*max-width: 400px;*/margin: auto;background: #EEE;padding: 4px;"><span>'+item.name+'</span><span><input type="text" style="width: 100px;text-align: center;" name="configcnt['+iid+']" value="'+(consist[iid]>0?consist[iid]:0)+'"></span></div>';
                }
                h+='</div>';
            }
            h+='<input type="button" value="Сохранить" class="w-ic-save-consist" data-id="'+id+'" data-parent_id="'+parent_id+'" data-price_param_id="'+price_param_id+'">';
            th.after(h);
            jQuery(".w-ic-view-next-ingridients").click(function(){
                var next=jQuery(this).find("+");
                if(next.is(":visible"))next.hide();
                else next.show();
            });

            jQuery(".w-ic-save-consist").click(function () {
                var th=jQuery(this);
                var id=th.data("id");
                var parent_id=th.data("parent_id");
                var price_param_id=th.data("price_param_id");
                jQuery.post(w_ic_ajax_path+"?act=save_consist&id="+id+"&parent_id="+parent_id+"&price_param_id="+price_param_id,th.parent().find("input").serialize(),function (res) {
                    if(typeof(res)=='string')res=JSON.parse(res);
                    if(res.result=='error'){alert(res.description);return false;}
                    w_ic_view_element_redact(el_id,price_param_id);
                    w_ic_element_statuses([el_id]);
                });
            });
            return false;
        });

});

}


function w_ic_view_line_consist(id,consist,ingridients)
{
    var no_consist=' <span style="color:red;">не задан состав</span>';
    if(typeof(consist)!='object') return no_consist;
    var j=0,ar=[];
    for(i in consist)
        ar[j++]=ingridients[i]+" ("+consist[i]+")";
    if(ar.length > 0)return ' &mdash; <span style="color:green;">'+ar.join(', ')+'</span>';
    return no_consist;
}


function w_ic_modal_show() {
    jQuery("body").addClass("w-ic-modal-lock");
    jQuery(".w-ic-modal-overlay").addClass("active");
    jQuery(".w-ic-modal-base").addClass("active");
    jQuery(".w-ic-modal-overlay").click(function () {
        w_ic_modal_hide();
        return false;
    });
    w_ic_close_init();
    return false;
}
function w_ic_modal_hide() {
    jQuery("body").removeClass("w-ic-modal-lock");
    jQuery(".w-ic-modal-overlay").removeClass("active");
    jQuery(".w-ic-modal-base").removeClass("active");
}
function w_ic_close_init() {

    jQuery("html").on("keyup keydown", function (e) {
        if (jQuery(".w-ic-modal-overlay").is(":visible") && e.which == 27) {
            w_ic_modal_hide();
        }
    });

    jQuery(".w-ic-close").click(function () {
        w_ic_modal_hide();
        return false;
    });
}


function ic_w_show_table_setting_iblock()
{
    if(jQuery(".ic-w-ib_setting_table_block").is("*"))
    {
        html='<table class="ic-w-config-ib-table"><thead><tr><th>Инфоблок</th><th>Раздел</th><th>Корр.коэф.</th><th>Затухание, %</th><th>Округление</th><th>Действие</th></tr></thead>';
        jQuery.get(w_ic_ajax_path+"?act=get_all_ib_settings",function(res){
            if(typeof(res)=='string')res=JSON.parse(res);
            if(res.result=='error'){alert(res.description);return false;}
            if(typeof(res.settings)=='object')
            {
                for(id in res.settings)
                {
                    var p={koef:0,zatuhan:0,okrugl:0};
                    if(res.settings[id].params)
                    {
                        p=res.settings[id].params;
                    }
                    html+='<tr data-cid="'+id+'">' +
                        '<td>'+res.settings[id].ib_name+'</td><td>'+res.settings[id].s_name+'</td>' +
                        '<td><input type="text" name="config[koef]" class="ic-w-koef ic-w-config-bckgrd" value="'+p.koef+'"></td>' +
                        '<td><input type="text" name="config[zatuhan]" class="ic-w-zatuhan ic-w-config-bckgrd" value="'+p.zatuhan+'"></td>' +
                        '<td><select name="config[okrugl]" class="ic-w-okrugl ic-w-config-bckgrd">' +
                        '<option value="0"'+(p.okrugl==0?' selected ':'')+'>Не округлять</option>' +
                        '<option value="1"'+(p.okrugl==1?' selected ':'')+'>До целого числа</option>' +
                        '<option value="2"'+(p.okrugl==2?' selected ':'')+'>До десяти</option>' +
                        '<option value="3"'+(p.okrugl==3?' selected ':'')+'>До 50</option>' +
                        '<option value="4"'+(p.okrugl==4?' selected ':'')+'>До 100</option>' +
                        '</select><br></td>' +
                        '<td><input type="button" class="ic-w-config_save" value="Сохранить">' +
                        '<input type="button" class="ic-w-config_delete" value="Удалить" data-cid="'+id+'">' +
                        '</td>' +
                        '</tr>';
                }
            }
            html+="</table>";
            jQuery(".ic-w-ib_setting_table_block").html(html);
            jQuery(".ic-w-config-bckgrd").on("change keyup click",function(){
                var th=jQuery(this);
                th.closest("tr").find("input[type=text], select, textarea").removeClass("ic-w-config-bckgrd");
            });
            jQuery(".ic-w-config_save").click(function(){
                var th=jQuery(this);
                var cid=th.closest("tr").data("cid");
                jQuery.post(w_ic_ajax_path+"?act=save_ib_params&cid="+cid,th.closest("tr").find("input[type=text], select, textarea").serialize(),function(res){
                    if(typeof(res)=='string')res=JSON.parse(res);
                    if(res.result=='error'){alert(res.description);return false;}
                    th.closest("tr").find("input[type=text], select, textarea").addClass("ic-w-config-bckgrd");
                });
            });

            jQuery(".ic-w-config_delete").click(function(){
                var th=jQuery(this);
                var cid=th.data("cid");
                if(confirm("Удалить строку?"))
                    jQuery.get(w_ic_ajax_path+"?act=delete_ib_config&cid="+cid,function(res){
                        th.closest("tr").remove();
                    });
            });
        });

    }
}

function ic_w_view_ingridients()
{
    var block=jQuery(".ic-w-ingridients");
    jQuery.get(w_ic_ajax_path+"?act=get_all_ingridients",function(res){
        if(typeof(res)=='string')res=JSON.parse(res);
        if(res.result=='error'){alert(res.description);return false;}
        var h='';
        for(g_id in res.groups)
        {
            h+='<div class="ic-w-group-title" data-group_id="'+g_id+'"><span><b class="ic-w-group-redact" data-group_id="'+g_id+'">'+res.groups[g_id].name+'</b></span>' +
                '<a href="" class="ic-w-add-ingridient" data-group_id="'+g_id+'">+ добавить ингредиент</a><!--<input type="button" class="ic-w-add-ingridient" data-group_id="'+g_id+'" value="+ добавить ингредиент">-->' +
                '</div>' +
                '<div class="ic-w-add-ingridient ic-w-hidden">' +
                'Название:<br>' +
                '<input type="text" name="name"><br><br>' +
                'Цена<br>' +
                '<input type="text" name="price"><br><br>' +
                '<input type="button" value="Добавить" class="ic-w-add-ingridient-button" data-group_id="'+g_id+'">' +
                '</div>';
            if(typeof(res.groups[g_id].items)=='object')
            {
                h=h+'<br><table><thead><tr><th>Название</th><th>Цена за шт.</th><th>Действие</th></tr></thead><tbody>';
                for(i_id in res.groups[g_id].items)
                    h=h+'<tr>' +
                        '<td><input type="text" class="ic-w-config-bckgrd" name="name" value="'+res.groups[g_id].items[i_id].name+'"></td>' +
                        '<td><input type="text" class="ic-w-config-bckgrd" name="price" value="'+res.groups[g_id].items[i_id].price+'"></td>' +
                        '<td><input type="button" class="ic-w-save_ingridient" data-i_id="'+i_id+'" value="Сохранить">' +
                        '<input type="button" class="ic-w-delete_ingridient" data-i_id="'+i_id+'" value="Удалить"></td>' +
                        '</tr>';
                h=h+'</tbody></table>';

            }

        }
        block.html(h);

        jQuery(".ic-w-add-ingridient").click(function(){jQuery(this).parent().find("+ .ic-w-add-ingridient").show();return false;});
        jQuery(".ic-w-add-ingridient-button").click(function(){
            var th=jQuery(this);
            var group_id=th.data("group_id");
            jQuery.post(w_ic_ajax_path+"?act=add_ingridient&group_id="+group_id,th.parent().find("input,textarea,select").serialize(),function(res){
                if(typeof(res)=='string')res=JSON.parse(res);
                if(res.result=='error'){alert(res.description);return false;}
                ic_w_view_ingridients();
            });
            return false;
        });

        jQuery(".ic-w-config-bckgrd").on("change keyup click",function(){
            var th=jQuery(this);
            th.closest("tr").find("input, select, textarea").removeClass("ic-w-config-bckgrd");
        });

        jQuery(".ic-w-save_ingridient").click(function(){
            var th=jQuery(this);
            var i_id=th.data("i_id");
            jQuery.post(w_ic_ajax_path+"?act=save_ingridient&i_id="+i_id,th.closest("tr").find("select,input,textarea").serialize(),function(res){
                if(typeof(res)=='string')res=JSON.parse(res);
                if(res.result=='error'){alert(res.description);return false;}
                th.closest("tr").find("input[type=text], select, textarea").addClass("ic-w-config-bckgrd");
            });
        });

        jQuery(".ic-w-delete_ingridient").click(function(){
            var th=jQuery(this);
            var i_id=th.data("i_id");
            if(confirm("Удалить ингредиент?"))
                jQuery.get(w_ic_ajax_path+"?act=delete_ingridient&i_id="+i_id,function(res){
                    if(typeof(res)=='string')res=JSON.parse(res);
                    if(res.result=='error'){alert(res.description);return false;}
                    th.closest("tr").remove();
                });
        });

        jQuery(".ic-w-group-redact").click(function(){
            var th=jQuery(this);
            var group_id=th.data("group_id");
            var old=th.html();
            if(new_name=prompt("Новое название:",old))
            {
                jQuery.post(w_ic_ajax_path+"?act=save_group_params&group_id="+group_id,{new_name:new_name},function(res){
                    if(typeof(res)=='string')res=JSON.parse(res);
                    if(res.result=='error'){alert(res.description);return false;}
                    th.html(new_name);
                });
            }
        });

    });
}

function ic_w_ingridients_simple(groups)
{
    var ret=[];
    for(gid in groups)
        for(i in groups[gid].items)
            ret[i]=groups[gid].items[i].name;
    return ret;
}


function ic_w_recalc_go()
{
    var th=jQuery(this);
    var id_price=jQuery(".ic-w-id-price-input").val();
    jQuery.get(w_ic_ajax_path+"?act=recalc_all&id_price="+id_price,function(html){
        jQuery("#ic-w-waiting").html(html);
    });
}
