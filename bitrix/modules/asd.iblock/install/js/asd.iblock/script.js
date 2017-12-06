var sListTable = '';
var sStartCh = '';
var sEndCh = '';
var asdIblockFormLoaded = false;
var asdIblockFormCnt = 0;

function formLoaded(){
	iblockTable = $('#'+sListTable);
	asdIblockFormCnt++;
	if(asdIblockFormCnt < 100){
		if($('tr.adm-list-table-row input[type=text]:visible',iblockTable).length>0){
			asdIblockInputChangeInit();
			asdIblockFormLoaded =  true;
			return true;
		} else {
			setTimeout(formLoaded,75);
			asdIblockFormLoaded = false;
		}
	} else {
		return false;
	}
}

function updateListner(){
	if($('#action_edit_button').length>0){
		$('#action_edit_button').on('click',function(){
			if($(this).hasClass('adm-edit-disable') == false){
				formLoaded();
			}
		});
	} else {
		setTimeout(updateListner,75);
	}
}
function asdIblockInputChangeInit(){
	var numRow = 0;
	iblockTable = $('#'+sListTable);
	$('tr.adm-list-table-row',iblockTable).each(function(e){
		var thisRow = $(this);
		var searchObjects = $('td input[type=text]:visible',thisRow).add('td textarea',thisRow).add('td select',thisRow);
		if(searchObjects.length>0){
			numRow++;
			var numCol = 0;
			searchObjects.each(function(c){
				numCol++;
				var thisCol = $(this);
				thisCol.attr('data-row',numRow);
				thisCol.attr('data-col',numCol);

				thisCol.on('keydown',function(e){
					var obj = {
						jq:$(this),
						event:e,
						where:iblockTable
					};
					if(e.ctrlKey){
						asdIblockChangeCurInput(obj);
					}
				});
				thisCol.on('focus',function(){
					$('.asd_iblock_input_backlight').removeClass('asd_iblock_input_backlight');
					$(this).parents('td.adm-list-table-cell:first').addClass('asd_iblock_input_backlight');
				})
			});

			$('#'+sListTable+'_footer_edit input:visible').on('click',function(){
				updateListner();
			});
		}
	});

}

function asdIblockChangeCurInput(obj){
	var row = obj.jq.data('row');
	var col = obj.jq.data('col');
	var newRow = row;
	var newCol = col;
	switch(obj.event.keyCode){
		case 37:
			newRow = row;
			newCol = col-1;
			break
		case 38:
			newRow = row-1;
			newCol = col;
			break
		case 39:
			newRow = row;
			newCol = col+1;
			break
		case 40:
			newRow = row+1;
			newCol = col;
			break
	}
	if (newRow!=row || newCol!=col) {
		$('[data-col='+newCol+'][data-row='+newRow+']',obj.where).focus().select();
	}
}

$(document).ready(function(){
	var jqueryVersion = $.fn.jquery.match('([0-9]{1})\.([0-9]{1,2})\.([0-9]{1,2})');
	if(jqueryVersion[1]>=1){
		if(jqueryVersion[1]>1 || jqueryVersion[2]>7){
			jQuery.fn.extend({
				live: function( types, data, fn ) {
					jQuery( this.context ).on( types, this.selector, data, fn );
					return this;
				}
			});
		}
	 }
	$('#action_edit_button').on('click',function(){
		if($(this).hasClass('adm-edit-disable') == false){
			formLoaded();
		}
	});

	$('#asd_export_prop_all').live('click', function(){
		var $bChecked = $(this).attr('checked');
		$.each($('.asd_export_prop'), function(){
			if ($bChecked) {
				$(this).attr('checked', 'checked');
			} else {
				$(this).removeAttr('checked');
			}
		});
	});
});

function ASDSelIBChange(value) {
	BX.style(BX('asd_ib_dest_cont'), 'display', ('asd_copy' == value || 'asd_move' == value ? 'inline-block' : 'none'));
	BX.style(BX('asd_ib_dest_sect'), 'display', ('asd_copy' == value || 'asd_move' == value ? 'inline-block' : 'none'));
}

function ASDSelIBShow(lang) {
	if (-1 < BX('asd_ib_dest').selectedIndex) {
		var intIBlockID = BX('asd_ib_dest').options[BX('asd_ib_dest').selectedIndex].value;
		jsUtils.OpenWindow('/bitrix/admin/iblock_section_search.php?lang='+lang+'&IBLOCK_ID='+intIBlockID+'&n=asd_sect_id', 600, 500);
	}
}