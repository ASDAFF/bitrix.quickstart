$(document).ready(function(){
	$('.asd_fav_move').click(function(){
		var id = $(this).attr('id').substr(7);
		$(this).hide();
		$('#asd_fs_'+id).show();
		return false;
	});
	$('.asd_fav_item select').change(function(){
		if ($(this).val().length <= 0)
			return;
		var id = $(this).attr('id').substr(7);
		window.location.href = sCurPage + '&move=' + id + '&moveto=' + $(this).val();
	});
	$('.asd_fav_delete').click(function(){
		if (!confirm(sMessConfirmDel))
			return false;
		var id = $(this).attr('id').substr(7);
		window.location.href = sCurPage + '&del=' + id;
		return false;
	});
});