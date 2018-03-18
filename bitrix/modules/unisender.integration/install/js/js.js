$(document).ready(function(){
	
	var fields = {};
	var fields_selected = {};
	
	if ($(".uni_export_form").length)
	{
		$('#name option').each(function(index){
			fields[this.value] = this.text;
		});
		
		$('select.fields_group').each(function(index){
			fields_selected[this.id] = this.value;
		});
		
		$('select.fields_group').change(function() {
			fields_selected[this.id] = this.value;
			updFields(this.id);
		});
		
		 $("#export_form").submit(function() {
			var submit = false;
			$('input.groups').each(function(index){
				if ( $(this).is(':checked') ) submit = true;
			});
			
			if (submit == false){
				alert("Выберите группу пользователей для переноса!");
			}
			return submit;
		});
		
		function updFields(field)
		{
			//alert($("#"+field+" :selected").text());
			
			$('select.fields_group').each(function(index){
				if (this.id!=field)
				{
					$(this).empty();
					$(this).append( $('<option value="0">'+fields[0]+'</option>'));
					for (id in fields)
					{
						//alert(in_Array(fields[id], fields_selected));
						if (!in_Array(id, fields_selected) && id > 0 )
							$(this).append( $('<option value="'+id+'">'+fields[id]+'</option>'));
						if (fields_selected[this.id]==id && id > 0)
							$(this).append( $('<option selected value="'+id+'">'+fields[id]+'</option>'));
					}
				}
			});
		}
	}
});

function in_Array(value, arr) {
	for (i in arr) if (arr[i] == value) return true;
	return false;
};