<script type="text/javascript">
	$(document).on("submit", "#<?=$arParams["PARENT_ID"];?> form", function () {
		$(this).find(".sf_feedback_form_error").remove();
		$.ajax({
			type: "POST",
			url: "<?=$arParams["PATH"];?>",
			data: $(this).serialize(),
			cache: false,
			async: false,
			success: function (html) {
				console.log(html);
				$("#<?=$arParams["PARENT_ID"];?>").html(html);
				$.fancybox.update();
				setTimeout(function() {
					$("#<?=$arParams["PARENT_ID"];?> .input_error").removeClass("input_error");
				}, 1000);
			}
		});

		return false;
	});
</script>