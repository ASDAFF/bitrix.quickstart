(function($) {
	$.fn.closest_descendent = function(filter) {
		var $found = $(),
			$currentSet = this; // Current place
		while ($currentSet.length) {
			$found = $currentSet.filter(filter);
			if ($found.length) break;  // At least one match: break loop
			// Get all children of the current set
			$currentSet = $currentSet.children();
		}
		return $found.first(); // Return first match of the collection
	}
})(jQuery);

$(document).ready(function () {

	$(".js-custom-option").each(function () {
		if ($(this).closest_descendent("select#option-type").val() === "L") {
			$(this).closest_descendent(".js-value-type-l").show();
		}
	});

	$("select#option-type").on("change", function () {
		if ($(this).val() === "L") {
			$(".js-value-type-l").show();
		} else {
			$(".js-value-type-l").hide();
		}
	});

	$(".js-add-value-in-list").on("click", function () {
		let countVales = $(this).closest(".js-custom-option").find(".js-value-in-list").length + 1;
		$(".js-list-inputs").append(
			"<input class=\"js-value-in-list\" placeholder=\"Новое значение\" type=\"text\" name=\"" + $(this).data('name') + "[value][" + countVales + "]\"><br>"
		);
	});

	$(".js-remove-option").on("click", function () {
		$($(this).closest(".js-custom-option")).remove();
	});
});