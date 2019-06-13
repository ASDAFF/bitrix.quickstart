$(document).ready(function(){
	$('#asd_a0').live('click', function(){$('#asd_e0').click(); return false;});
	$('.asd_fav_edit').live('click',function(){
		var id = $(this).attr('id').substr(5);
		var val = $('#asd_a'+id).html();
		$('#asd_i'+id).show();
		if (id != 0)
			$('#asd_i'+id+' input').val(val);
		$('.asd_fav_buttons').show();
		$('#asd_i'+id+' input').focus();
	});
	$('#asd_cancel').live('click', function(){
		$('.asd_fav_input').hide();
		$('.asd_fav_input input').val('');
		$('.asd_fav_buttons').hide();
	});
	$('#asd_save').live('click', function(){
		var aFolders = [];
		$('.asd_fav_input input:visible').each(function(i){
			var val = $(this).val().replace(/^\s+|\s+$/, '');
			var id = $(this).parent().attr('id').substr(5);
			if (val.length>0)
			{
				if (id == 0)
				{
					$('#asd_i0 input').val('');
					$.get('/bitrix/tools/asd_favorite.php', {name : val, action : 'add', charset : sCharset, sessid : sSessId, maxchars : iMaxChars, type : sType, key : sKey}, function(data){
						var newId = data;
						if (parseInt(newId) > 0)
						{
							$('#asd_f0').before('<div class="asd_fav_folder" id="asd_f'+newId+'">'+
												$('#asd_f0').html().replace('asd_f0', 'asd_f'+newId)
															.replace('asd_e0', 'asd_e'+newId)
															.replace('asd_a0', 'asd_a'+newId)
															.replace('asd_i0', 'asd_i'+newId)
															.replace('asd_d0', 'asd_d'+newId)
															.replace('asd_s0', 'asd_s'+newId)
												+'</div>');
							$('#asd_e'+newId).removeClass('asd_fav_new');
							$('#asd_e'+newId).attr('title', sTitleEditFolder);
							$('#asd_d'+newId).attr('title', sTitleDeleteFolder);
							$('#asd_a'+newId).removeClass('asd_fav_new_link');
							$('#asd_a'+newId).html(val);
							$('#asd_a'+newId).attr('href', sFolderPath.replace('#ID#', newId));
							$('#asd_f'+newId+' .asd_count').html('0');
						}
					});
				}
				else
				{
					$('#asd_a'+id).html(val);
					aFolders[i] = id+'|'+val;
				}
			}
		});
		if (aFolders.length > 0)
		{
			var obGet = {count : aFolders.length, action : 'upd', charset : sCharset, sessid : sSessId, maxchars : iMaxChars, type : sType, key : sKey};
			for (i=0; i<aFolders.length; i++)
				obGet['folder_'+i] = aFolders[i];
			$.get('/bitrix/tools/asd_favorite.php', obGet);
		}
		$('.asd_fav_input').hide();
		$('.asd_fav_buttons').hide();
	});
	$('.asd_fav_del').live('click',function(){
		var id = $(this).attr('id').substr(5);
		if ($('#asd_f'+id).size() && (id==0 || confirm(sMessDelConfirm)))
		{
			if ($('.asd_fav_input:visible').size() == 1)
				$('#asd_cancel').click();
			if (id != 0)
			{
				$.get('/bitrix/tools/asd_favorite.php', {id : id, action : 'del', sessid : sSessId});
				$('#asd_f'+id).remove();
			}

		}
	});
	$('.asd_fav_star').live('click',function(){
		var id = $(this).attr('id').substr(5);
		$('.asd_fav_star').removeClass('asd_fav_star_act');
		$('.asd_fav_star').attr('title', sTitleStar);
		$(this).addClass('asd_fav_star_act');
		$(this).attr('title', sTitleStarAlready);
		$.get('/bitrix/tools/asd_favorite.php', {id : id, action : 'default', sessid : sSessId, maxchars : iMaxChars, type : sType, key : sKey});
	});
	$(document).keydown(function(e){
		if (e.keyCode == 27)
			$('#asd_cancel').click();
		else if (e.keyCode == 13)
			$('#asd_save').click();
	});
});