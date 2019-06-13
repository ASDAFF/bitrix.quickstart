window.onload = doOnload;

$(window).bind('resize', function(){    
        resizeWidth();
});

function doOnload(){
    $("#tab_cont_edit12").click(function(){
        resizeWidth();
    });
    
    $(".select-chosen").chosen({no_results_text:'Нет результатов по '});
    
    resizeWidth();   
 
 
    $("#submitIndexProperty").click(function(){
        $(".row_dop_prop").remove();
        $("input[name='SETTINGS[catalog][selector_product][LENGTH]']").val('');
        $("input[name='SETTINGS[catalog][selector_product][WIDTH]']").val('');
        $("input[name='SETTINGS[catalog][selector_product][HEIGHT]']").val('');
        $("input[name='SETTINGS[catalog][selector_product][WEIGHT]']").val('');
        $("input[name='SETTINGS[catalog][preview_count]']").val('');
        $("input[name='SETTINGS[catalog][preview_price]']").val('');
     
        $("input[name='SETTINGS[catalog][name]']").val('');
        $("input[name='SETTINGS[catalog][xml_id_selector]']").val('');
        $("input[name='SETTINGS[catalog][id_selector]']").val('');
        $("input[name='SETTINGS[catalog][preview_picture]']").val('');
        $("input[name='SETTINGS[catalog][detail_picture]']").val('');
        $("input[name='SETTINGS[catalog][detail_text_selector]']").val('');
                
        $(".select-chosen").each(function(i){
            var data_property = $(this.options[this.selectedIndex]).closest('optgroup').attr('data-property');
            var text = this.options[this.selectedIndex].innerHTML;
            var val = $(this).val();
            var column = $(this).attr("data-column");
            if(data_property=='property'){
                var str = '<tr class="row_dop_prop"><td width="40%" class="adm-detail-content-cell-l">'+text+':</td><td width="60%" class="adm-detail-content-cell-r"><input type="text" value="'+                           column+'" name="SETTINGS[catalog][selector_prop]['+val+']" data-code="'+val+'" size="40">&nbsp;<a href="#" class="prop_delete">Delete</a></td></tr>';
                $(".select_dop_property").before(str);
            } else if(data_property=='catalog'){
                if(val=='ICAT_LENGTH'){
                    $("input[name='SETTINGS[catalog][selector_product][LENGTH]']").val(column);
                }else if(val=='ICAT_WIDTH'){
                    $("input[name='SETTINGS[catalog][selector_product][WIDTH]']").val(column);
                }else if(val=='ICAT_HEIGHT'){
                    $("input[name='SETTINGS[catalog][selector_product][HEIGHT]']").val(column);
                }else if(val=='ICAT_WEIGHT'){
                    $("input[name='SETTINGS[catalog][selector_product][WEIGHT]']").val(column);
                }else if(val=='ICAT_QUANTITY'){
                    $("input[name='SETTINGS[catalog][preview_count]']").val(column);
                }else if(val=='ICAT_PREVIEW_PRICE'){
                    $("input[name='SETTINGS[catalog][preview_price]']").val(column);
                }
            } else if(data_property=='element'){
                if(val=='IE_NAME'){
                    $("input[name='SETTINGS[catalog][name]']").val($("input[name='SETTINGS[catalog][name]']").val()+column+',');
                } else if(val=='IE_ID'){
                    $("input[name='SETTINGS[catalog][id_selector]']").val(column);
                } else if(val=='IE_XML_ID'){
                    $("input[name='SETTINGS[catalog][xml_id_selector]']").val(column);
                } else if(val=='IE_PREVIEW_PICTURE'){
                    $("input[name='SETTINGS[catalog][preview_picture]']").val(column);
                } else if(val=='IE_DETAIL_PICTURE'){
                    $("input[name='SETTINGS[catalog][detail_picture]']").val(column);
                } else if(val=='IE_DETAIL_TEXT'){
                    $("input[name='SETTINGS[catalog][detail_text_selector]']").val($("input[name='SETTINGS[catalog][detail_text_selector]']").val()+column+',');
                } else if(val=='IE_PARENT_NAME'){
                    $("input[name='SETTINGS[catalog][id_section]']").val(column);
                    $("input[name='SETTINGS[catalog][section_by_name]']").prop("checked", true);
                }
            }
        });
        var name = $("input[name='SETTINGS[catalog][name]']").val();
        var descr = $("input[name='SETTINGS[catalog][detail_text_selector]']").val();
        
        if(name[name.length-1]==','){
            $("input[name='SETTINGS[catalog][name]']").val(name.substr(0,name.length-1));                
        }
        if(descr[descr.length-1]==','){
            $("input[name='SETTINGS[catalog][detail_text_selector]']").val(descr.substr(0,descr.length-1));                
        }
     });
}

function resizeWidth(){
    $('div.wrap-table').each(function(){
            var div = $(this);
            div.css('width', 0);
            div.prev('.set-scroll').css('width', 0);
            var timer = setInterval(function(){
                var width = div.parent().width();
                if(width > 0)
                {
                    div.css('width', width);
                    div.prev('.set-scroll').css('width', width).find('>div').css('width', div.find('>table.preview-xls-data').width());
                    clearInterval(timer);
                }
            }, 100);
            setTimeout(function(){clearInterval(timer);}, 3000);
    });
}
            
$(document).ready(function(){
    
    $("select.select_catalog_level").each(function(i){
        var value =  this.value;        
        if(value!='--' && value!='')
            $("select.select_catalog_level").not(this).find("option[value="+value+"]").attr("disabled","disabled");
    });
    
    var previous;
    $("select.select_catalog_level").on("focus",function () {
        previous = this.value;
    }).change(function() {
        var value =  this.value;
        
        if(previous!='--' && previous!='') {
            $("select.select_catalog_level option[value="+previous+"]").removeAttr("disabled");      
            
            var row = $(this).parent().parent().prev();
            if(row.hasClass("catalog-row"))
                row.remove();            
        }
               
        if(value!='--' && value!='') {            
            $("select.select_catalog_level").not(this).find("option[value="+value+"]").attr("disabled","disabled");
            
            var row = $(this).parent().parent();
            var l = row.children().length-2;
            var i, str, select;
            str='<tr class="catalog-row">';
            str+='<td class="num-cell"></td><td class="menu-cell">Свойства раздела</td>';
            for(i = 0; i < l; i++){
            select = '<select class="typeselect" name="SETTINGS[catalog_level_p][property][lvl_'+value+']['+i+']" id="SETTINGS[catalog_level_p][property][lvl_'+value+']['+i+']"><option value="">Выберите свойство</option><option value="CATALOG_NAME">Название раздела</option></select>';
                str+='<td>'+select+'</td>';
            }
            str+='</tr>';
            row.before(str);
        }
        previous = this.value;
        var str = '';
    });

})            