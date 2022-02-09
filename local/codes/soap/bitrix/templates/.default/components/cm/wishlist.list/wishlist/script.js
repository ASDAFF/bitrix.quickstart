$(document).ready(function(){
	function wishlist_element_delete(object){
		var ID = object.attr('el')
		var category = object.attr('cat')
		var button = object
		$.ajax({
			type: "POST",
			url: "/includes/ajax/wishlist/delete_element.php",
			data: ({
					element : ID,
					cat : category
					}),
			success: function(html){
				button.parent(".b-slider__item").remove()
				$(".b-sidebar-wishlist__count span[cat="+category+"]").each(function(){
					var val = $(this).text()
					val--
					$(this).text(val)
				})
			}
		})
	}
	$(".b-delete_element").click(function(){
		var button = $(this)
		if(button.attr('el')&&button.attr('cat')){
			wishlist_element_delete(button)
		}
		
	}) 
	$('.b-wishlist__rename').click(function(){
		$('#b-wishlist__rename').show();	
	})
	$('#wishlist_rename_cat').click(function(){
		var category = $(this).attr('cat')
		var cat_name = $("#wishlist_rename_field").val()
		//var button = $(this)
		$.ajax({
			type: "POST",
			url: "/includes/ajax/wishlist/rename_category.php",
			data: ({
					cat : category,
					name: cat_name
					}),
			success: function(html){
				$('#b-wishlist__rename').hide()
				var obj = eval(html)
				location.href = '/wishlist/'+obj.cat.CODE+'/'
				console.log(html)
			}
		})
	})
})