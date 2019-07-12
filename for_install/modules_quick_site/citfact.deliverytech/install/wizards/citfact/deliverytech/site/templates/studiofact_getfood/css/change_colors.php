<div class="change_colors_block radius5" style="display: none;">
	<div class="item radius5 orange" id="change_color_orange"></div>
	<div class="item radius5 blue" id="change_color_blue"></div>
	<div class="item radius5 red" id="change_color_red"></div>
	<div class="item radius5 purple" id="change_color_purple"></div>
	<div class="item radius5 green" id="change_color_green"></div>
</div>
<style>
.change_colors_block {
	position: fixed;
	top: 50%;
	left: -5px;
	background: #FFFFFF;
	color: #000000;
	width: auto;
	height: auto;
	margin-top: -140px;
	-webkit-box-shadow: 2px 2px 3px #CACACA;
	-moz-box-shadow: 2px 2px 3px #CACACA;
	box-shadow: 2px 2px 3px #CACACA;
	z-index: 50;
}
.change_colors_block .item {
	display: block;
	width: 40px;
	height: 40px;
	margin: 15px;
	cursor: pointer;
}
.change_colors_block .item.orange {
	background: #FC7A38;
}
.change_colors_block .item.blue {
	background: #3498db;	
}
.change_colors_block .item.red {
	background: #e74c3c;	
}
.change_colors_block .item.purple {
	background: #9b59b6;	
}
.change_colors_block .item.green {
	background: #27ae60;	
}
</style>
<script type="text/javascript">
	$(document).on("click", ".change_colors_block .item", function () {
		var color = $(this).attr("id").substring(13);
		if (color == "orange") {
		window.location.href = "/";
		}else{
		window.location.href = "/" + color + "/";
		};
	});
	function open_colors () {
		if (parseFloat(getClientWidth()) > parseFloat($(".change_colors_block").width()) + parseFloat($(".main_container").width()) + 80) {
			// $(".change_colors_block").fadeIn("fast");
		} else {
			$(".change_colors_block").hide();
		}
	}
	$(window).resize(function () { open_colors (); });
	$(document).ready(function () { open_colors (); });
</script>