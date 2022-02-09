$(document).ready(function(){
	function wishlist_delete(object){
		var category = object.attr('cat');
		var button = object;
		if(category){
			$.ajax({
				type: "POST",
				url: "/includes/ajax/wishlist/delete_category.php",
				data: ({cat : category}),
				success: function(){
					button.parent(".b-sidebar-wishlist__section").remove()
				}
			})
		}
	}
	$(".b-wishlist_list_delete").click(function(){
		wishlist_delete($(this))
	})
	$("#wishlist_add").click(function(){
		var name = $("#wishlist_name").val();
		if(name){ 
			$.ajax({
				type: "POST",
				url: "/includes/ajax/wishlist/add_category.php",
				data: ({NAME : name}),
				success: function(html){
					var obj = eval(html)
					$("#wishlist_sidebar").prepend('<div class="b-sidebar-wishlist__section"><div class="b-sidebar-wishlist__title"><h2 class="b-sidebar-wishlist__h2"><a href="/wishlist/'+obj.cat.CODE+'/">'+obj.cat.NAME+'</a><span class="b-sidebar-wishlist__count">0</span></h2></div><button cat="'+obj.cat.ID+'" class="b-button__delete m-cart__delete m-wishlist__delete"></button></div>')
					grit_text = 'Товар был успешно добавлен в вишлист!'
					if(obj.cat.USER=='N')
						grit_text += ' Для сохранения вишлистов авторизируйтесь!'
					$.gritter.add({
                                title: 'Добавление вишлиста',
                                text: grit_text,
                                sticky: false,
                                time: 2500
					});
					$(".b-wishlist_list_delete").live('click', function() {
						var category = $(this).attr('cat');
						var button = $(this);
						if(category){
							$.ajax({
								type: "POST",
								url: "/includes/ajax/wishlist/delete_category.php",
								data: ({cat : category}),
								success: function(){
									button.parent(".b-sidebar-wishlist__section").remove()
								}
							})
						}	
					})
				}
			})
		}
	})
})