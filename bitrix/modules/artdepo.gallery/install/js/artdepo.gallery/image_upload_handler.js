if (!window.BX && top.BX)
	window.BX = top.BX;
	
BX.ready(function(){

$(document).ready(function () {

    var $fine_uploader = $('#fine-uploader');
    // Init
    $fine_uploader.fineUploader({
        request: {
            endpoint: '../admin/artdepo_gallery_ajax.php?action=edit_item&sessid=' + bxSession.sessid,
            params: {
                item_path: '',
                item_name: '', // set onSubmit
                item_desc: '',
                item_keywords: '',
                item_collections: 0, // set onSubmit
                id: '', // Empty for new files
                source_type: 'PC',
                utime: Math.round((new Date()).getTime() / 1000) // no cache
            },
            paramsInBody: true,
            inputName: 'load_file'
        },
            
        editFilename: {
            enabled: false
        },
        
        autoUpload: true,
        
        validation: {
            allowedExtensions: ['jpeg', 'jpg', 'gif', 'png']
        },
        
        text: {
            uploadButton: BX.message('ADG_UP_BTN_ADD_NEW_IMG'),
            dropProcessing: '',
            dragZone: BX.message('ADG_UP_DRAG_DROP'),
            cancelButton: BX.message('ADG_UP_BTN_CANCEL'),
            retryButton: BX.message('ADG_UP_BTN_REPEAT'),
            deleteButton: BX.message('ADG_UP_BTN_DELETE'),
            failUpload: BX.message('ADG_UP_ERROR_FILE_UPLOAD')
        },

        template: '\
            <div class="qq-uploader adm-detail-content-wrap">\
                <div class="adm-detail-content">\
	                <div class="adm-detail-title" style="color:#5b5e61;">'+BX.message('ADG_UP_TITLE')+'</div>\
	                <div class="adm-detail-content-item-block">\
                        '+BX.message('ADG_UP_DESCRIPTION_TEXT')+'\
                        <span class="qq-drop-processing"><span>{dropProcessingText}</span><span class="qq-drop-processing-spinner"></span></span>\
                        <ul class="qq-upload-list"></ul>\
	                </div>\
                </div>\
                <div class="adm-detail-content-btns-wrap" style="left: 0px;">\
                    <div class="adm-detail-content-btns">\
                        <span id="qq-upload_photo-button" class="qq-upload-button adm-btn adm-btn-save adm-btn-add" style="float:none;width:auto;">{uploadButtonText}</span>\
                        <a href="" class="adm-btn" style="visibility: hidden;">'+BX.message('ADG_UP_BTN_SHOW_UPLOADED_IMAGES')+'</a>\
                    </div>\
                </div>\
            </div>\
            <div class="qq-upload-drop-area add_photo_hint"><span>{dragZoneText}</span></div>\
            '
    });
    // Events
    $fine_uploader.on('submit', function(event, id, namefileName) {
        var collection_id;
        if(artdepo_gallery_section.parent_collection && artdepo_gallery_section.parent_collection > 0){
            collection_id = artdepo_gallery_section.parent_collection;
        }else{
            var url_params = location.search.substring(1),
                re = /find_parent_id=(\d+)/,
                tokens = re.exec(url_params);
            if(tokens[1]) 
                collection_id = tokens[1];
        }
        if(!collection_id)
            return false;
        
        $(this).fineUploader('setParams', {
            'item_name': namefileName.replace(/\.[^/.]+$/, ""),
            'item_collections': collection_id,
            'source_type': 'PC'
        });
    }).on('complete', function(event, id, fileName, response){
        // http://docs.fineuploader.com/integrating/server/index.html
        // Show Save button, once
        $fine_uploader.find(".adm-detail-content-btns-wrap .adm-btn").css("visibility", "visible");
    });
    
});

});
