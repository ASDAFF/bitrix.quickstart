$( document ).ready(function() {

	$('.-add-ext').on('click', function(){

		var $to = $('.-settings-form').find('tbody'),
			id  = $('.-last-id').val(),
			template = '<tr> \
				<td> \
				<input \
					type="text" \
					id="exeptions" \
					name="iblocks[{{ID}}]"> \
				<input \
					type="text" \
					id="exeptions" \
					name="props[{{ID}}]"> \
				</td> \
				</tr>';

		$(template.replace(/{{ID}}/g, ++id)).insertBefore('.-insert');

		$('.-last-id').val(id);
	});

});