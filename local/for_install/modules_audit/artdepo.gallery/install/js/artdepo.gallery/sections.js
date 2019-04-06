if (!window.BX && top.BX)
	window.BX = top.BX;

var ArtDepoGallery = {
    'add': function(){
        this.showDialog({
            'title': BX.message('ADG_WTITLE_NEW_SECTION')
        });
    },
    
    'edit': function(id, mode){
        var self = this;
        BX.ready(function(){
            mode = (mode == 'item') ? 'item' : 'collection';
            var url = "/bitrix/admin/artdepo_gallery_ajax.php";
            url += "?action=get_"+mode+"&sessid=" + bxSession.sessid;
            BX.ajax.loadJSON(url, {'id': id}, function(data){
                self.showEditDialog(data, mode);
            }, function(){
                alert("Error!");
            });
            return false;
        });
        return false;
    },
    
    'showEditDialog': function(item, save_mode){
        save_mode = (save_mode == "item") ? save_mode : 'collection';
        var parent_id = this.getParentID();
        BX.ready(function(){
            if ( parent_id <= 0) {
                alert(BX.message('ADG_ERROR_NO_SECTION'));
                return false;
            }
            var sort = item.SORT || "500",
                languages = artdepo_gallery_section.languages,
                inner_content = "",
                action_url = '/bitrix/admin/artdepo_gallery_ajax.php?action=edit_'+save_mode+'&lang='+artdepo_gallery_section.site_id+'&sessid='+bxSession.sessid
            
            inner_content += '<form method="POST" style="overflow:hidden;" action="'+action_url+'" enctype="multipart/form-data">';
            inner_content += '<input type="hidden" name="id" value="'+item.ID+'">';
            inner_content += '<input type="hidden" name="parent" value="'+parent_id+'">';
            inner_content += '<table class="mlsd-fields-tbl"><tbody>';
            for(var i=0, lan = languages.length, val = ""; i < lan; i++){
                val = item["NAME_" + languages[i].lid.toUpperCase()];
                if (!val)
                    val = "";
                inner_content += '<tr><td><b>'+BX.message('ADG_LBL_NAME')+' ('+languages[i].name+'):</b></td><td><input type="text" name="name_'+languages[i].lid+'" value="'+val+'"></td></tr>';
            }
            inner_content += '<tr><td><b>'+BX.message('ADG_LBL_SORT')+':</b></td><td><input type="text" name="sort" value="'+sort+'"></td></tr>';
            inner_content += '</tbody></table>';
            
            inner_content += '</form>';

            var btnSave = { 
                'title': BX.message('ADG_LBL_SAVE'), 
                'id': 'action_send', 
                'name': 'savebtn',
                'className': BX.browser.IsIE() && BX.browser.IsDoctype() && !BX.browser.IsIE10() ? '' : 'adm-btn-save',
                'action': function(){ 
                    var form = this.parentWindow.GetForm();
                        form_elements = form.elements,
                        firstPassed = false;
                    for(var i = 0, len = form_elements.length; i < len; i++){
                        var el = form_elements[i];
                        if(el.type == "text" && !firstPassed){
                            if(el.value == ''){
                                alert(BX.message('ADG_ERROR_EMPTY_NAME'));
                                return false;
                            }
                            firstPassed = true;
                        }
                        if(el.type == "text")
                            el.value = escape(el.value);
                    }
                    this.disableUntilError();
                    this.parentWindow.PostParameters();
                    this.parentWindow.Close(); 
                } 
            }

            var Dialog = new BX.CAdminDialog({
                title: BX.message('ADG_WTITLE_EDIT_SECTION'),
                head: BX.message('ADG_MSG_ENTER_NAME'),
                content: inner_content,
                icon: 'head-block',
                resizable: true,
                draggable: true,
                height: '168',
                width: '400',
                content_url: action_url,
                buttons: [btnSave, BX.CDialog.btnCancel]
            });
            Dialog.Show();    
        });
    },
    
    'showDialog': function(params){
        var self = this,
            parent_id = self.getParentID();
        BX.ready(function(){
            if ( parent_id <= 0 ) {
                alert(BX.message('ADG_ERROR_NO_SECTION'));
                return false;
            }
            var languages = artdepo_gallery_section.languages,
                inner_content = "";
            
            inner_content += '<form method="POST" style="overflow:hidden;" action="">';
            inner_content += '<input type="hidden" name="parent" value="'+parent_id+'">';
            inner_content += '<table class="mlsd-fields-tbl"><tbody>';
            for(var i=0, lan = languages.length; i < lan; i++){
                inner_content += '<tr><td><b>'+BX.message('ADG_LBL_NAME')+' ('+languages[i].name+'):</b></td><td><input type="text" name="name_'+languages[i].lid+'"></td></tr>';
            }
            inner_content += '</tbody></table></form>';

            var btnSave = { 
                'title': BX.message('ADG_LBL_SAVE'),
                'id': 'action_send', 
                'name': 'savebtn',
                'className': BX.browser.IsIE() && BX.browser.IsDoctype() && !BX.browser.IsIE10() ? '' : 'adm-btn-save',
                'action': function(){ 
                    var form = this.parentWindow.GetForm();
                        form_elements = form.elements,
                        firstPassed = false;
                    for(var i = 0, len = form_elements.length; i < len; i++){
                        var el = form_elements[i];
                        if(el.type != "text")
                            continue;
                        el.value = escape(el.value);
                        if(el.type == "text" && !firstPassed){
                            if(el.value == ''){
                                alert(BX.message('ADG_ERROR_EMPTY_NAME'));
                                return false;
                            }
                            firstPassed = true;
                        }
                    }
                    this.disableUntilError();
                    this.parentWindow.PostParameters();
                    this.parentWindow.Close(); 
                } 
            }

            var Dialog = new BX.CAdminDialog({
                title: params.title,
                head: BX.message('ADG_MSG_ENTER_NAME'),
                content: inner_content,
                icon: 'head-block',
                resizable: true,
                draggable: true,
                height: '168',
                width: '400',
                content_url: '/bitrix/admin/artdepo_gallery_ajax.php?action=edit_collection&lang='+artdepo_gallery_section.site_id+'&sessid='+bxSession.sessid,
                buttons: [btnSave, BX.CDialog.btnCancel]
            });
            Dialog.Show();
        });
    },
    
    'getParentID': function(){
        var collection_id,
            url_params = location.search.substring(1),
            re = /find_parent_id=(\d+)/,
            tokens = re.exec(url_params);
        if(tokens && tokens[1]) 
            collection_id = tokens[1];
        if(!collection_id)
            collection_id = artdepo_gallery_section.parent_collection;
        return collection_id;
    }
};
