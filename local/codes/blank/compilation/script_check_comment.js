<script type="text/javascript">
	//для комментариев админа
	var arrObj = <?php echo json_encode(sestHL::getDataHL(8)); ?>;
	for (i in arrObj) {
		var ufComment = arrObj[i].UF_COMMENT.trim(),
			ufAdmin = arrObj[i].UF_USER.trim();
			
		$('.r-userr').each(function(){
			var strComment = $(this).html().trim();				
			if ( ufComment == strComment && ufAdmin == 'Y')
				$(this).addClass('admin');				
		});
	}	
</script>
