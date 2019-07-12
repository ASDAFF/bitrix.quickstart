<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<script>

function showComment(parentId, editId, btn) {

	parentId = parentId || 0;
	editId = editId || '';

	document.form_comment.parentId.value = parentId;
	document.form_comment.edit_id.value = editId;
	document.form_comment.act.value = 'add';
	document.form_comment.action = document.form_comment.action + "#" + parentId;
	document.form_comment.comment.parentNode.className = document.form_comment.comment.parentNode.className.replace(' has-error', " ");
	document.form_comment.review_text_comment.nextElementSibling.style.display = "none";

	if(document.form_comment.user_name) {
		document.form_comment.user_name.parentNode.className = document.form_comment.user_name.parentNode.className.replace(' has-error', " ");
		document.form_comment.user_name.nextElementSibling.style.display = "none";
	}

	if(window.innerWidth > rsFlyaway.breakpoints.sm) {
		openPopup("#form_reviews", 'popup', {
			title: btn.title
		});
	} else {
		var formReviews = BX("form_reviews");
		if(!formReviews) {
			return;
		}

		formReviews.style.display = !formReviews.style.display ||formReviews.style.display == 'none' ? 'block' : 'none';
	}
}

function clearFormFields() {
	document.form_comment.review_text_plus.value = '';
	document.form_comment.review_text_minus.value = '';
	document.form_comment.review_text_comment.value = '';
	document.form_comment.comment.value = '';
	document.form_comment.review_rating.value = 0;

	var stars = document.form_comment.querySelector('.js-stars');

	if(stars) {
		stars.className = stars.className.replace(/ rating-[5]/g, "");
	}
}



function waitResult(id)
{
	r = 'new_comment_' + id;
	ob = BX(r);
	if(ob.innerHTML.length > 0)
	{
		var obNew = BX.processHTML(ob.innerHTML, true);
		scripts = obNew.SCRIPT;
		BX.ajax.processScripts(scripts, true);
		if(window.commentEr && window.commentEr == "Y")
		{
			BX('err_comment_'+id).innerHTML = ob.innerHTML;
			ob.innerHTML = '';
			$.fancybox.close();
		}
		else
		{
			if(BX('edit_id').value > 0)
			{
				if(BX('blg-comment-'+id))
				{
					BX('blg-comment-'+id+'old').innerHTML = BX('blg-comment-'+id).innerHTML;
					BX('blg-comment-'+id+'old').id = 'blg-comment-'+id;
					if(BX.browser.IsIE()) //for IE, numbered list not rendering well
						setTimeout(function (){BX('blg-comment-'+id).innerHTML = BX('blg-comment-'+id).innerHTML}, 10);
				}
				else
				{
					BX('blg-comment-'+id+'old').innerHTML = ob.innerHTML;
					if(BX.browser.IsIE()) //for IE, numbered list not rendering well
						setTimeout(function (){BX('blg-comment-'+id+'old').innerHTML = BX('blg-comment-'+id+'old').innerHTML}, 10);

				}
			}
			else
			{
				BX('new_comment_cont_'+id).innerHTML = ob.innerHTML + BX('new_comment_cont_'+id).innerHTML;
				if(BX.browser.IsIE()) //for IE, numbered list not rendering well
					setTimeout(function (){BX('new_comment_cont_'+id).innerHTML = BX('new_comment_cont_'+id).innerHTML}, 10);
			}
			ob.innerHTML = '';
			BX('form_c_del').style.display = "none";
		}
		window.commentEr = false;

		BX('post-button').disabled = false;
		BX.onCustomEvent("onIblockCatalogCommentSubmit");
		$.fancybox.close();
		clearFormFields();
	}
	else
		setTimeout("waitResult('"+id+"')", 500);
}

function validReviewForm() {
	var plus = document.form_comment.review_text_plus;
	var minus = document.form_comment.review_text_minus;
	var comment = document.form_comment.review_text_comment;
	var username = document.form_comment.user_name;
	var isValid = function(prop) {

		if(prop && !prop.value.trim()) {

			prop.parentNode.className += " has-error";
			prop.nextElementSibling.style.display = "";

			return false;
		} else if(prop) {
			prop.parentNode.className = prop.parentNode.className.replace(' has-error', " ");
			prop.nextElementSibling.style.display = "none";
		}

		return true;
	};

	return [username, comment].every(isValid);
}

function submitComment() {

	if(!validReviewForm()) {
		return false;
	}

	makeReviewComment();

	BX('post-button').focus();
	BX('post-button').disabled = true;
	obForm = BX('form_comment');
	<?
	if($arParams["AJAX_POST"] == "Y")
	{
		?>
		if(BX('edit_id').value > 0)
		{
			val = BX('edit_id').value;
			BX('blg-comment-'+val).id = 'blg-comment-'+val+'old';
		}
		else
			val = BX('parentId').value;

		id = 'new_comment_' + val;
		if(BX('err_comment_'+val))
			BX('err_comment_'+val).innerHTML = '';

		BX.ajax.submitComponentForm(obForm, id);
		setTimeout("waitResult('"+val+"')", 100);
		<?
	}
	?>
	BX.submit(obForm);
}

function hideShowComment(url, id)
{
	var siteID = '<? echo SITE_ID; ?>';
	var bcn = BX('blg-comment-'+id);
	rsFlyaway.darken($(bcn));
	bcn.id = 'blg-comment-'+id+'old';
	url += '&SITE_ID='+siteID;
	BX.ajax.get(url, function(data) {
		var obNew = BX.processHTML(data, true);
		scripts = obNew.SCRIPT;
		BX.ajax.processScripts(scripts, true);

		bcn.innerHTML = data;

		var bc = BX('blg-comment-'+id);
		bcn.innerHTML = bc.innerHTML;
		bcn.id = 'blg-comment-'+id;

		rsFlyaway.darken($(bcn));
	});

	return false;
}

function deleteComment(url, id)
{
	var siteID = '<? echo SITE_ID; ?>';
	rsFlyaway.darken($('#blg-comment-'+id));
	url += '&SITE_ID='+siteID;
	BX.ajax.get(url, function(data) {
		var obNew = BX.processHTML(data, true);
		scripts = obNew.SCRIPT;
		BX.ajax.processScripts(scripts, true);
		BX('blg-comment-'+id).innerHTML = '';
		rsFlyaway.darken($('#blg-comment-'+id));
	});

	return false;
}
<?if($arResult["NEED_NAV"] == "Y"):?>
function bcNav(page, th)
{
	$(th).button('loading');
	setTimeout(function() {
		for(i=1; i <= <?=$arResult["PAGE_COUNT"]?>; i++)
		{
			if(i == page)
			{
				BX.addClass(BX('blog-comment-nav-t'+i), 'blog-comment-nav-item-sel');
				BX.addClass(BX('blog-comment-nav-b'+i), 'blog-comment-nav-item-sel');
				BX('blog-comment-page-'+i).style.display = "";
			}
			else
			{
				BX.removeClass(BX('blog-comment-nav-t'+i), 'blog-comment-nav-item-sel');
				BX.removeClass(BX('blog-comment-nav-b'+i), 'blog-comment-nav-item-sel');
				//BX('blog-comment-page-'+i).style.display = "none";
			}
		}
		$(th).button('reset');
		}, 300);
	return false;
}

function loadMoreReviews(btn) {
	var currentPage = $("[id^=blog-comment-page-]:visible:last").data('page');
	var lastPage = currentPage - 1;

	if(lastPage >= 1) {
		bcNav(lastPage, btn)
	}
	if(lastPage <= 1) {
		$(btn).hide();
	}

}
<?endif;?>

function makeReviewComment() {
	var separator = ":S:",
		plus = document.form_comment.review_text_plus,
		minus = document.form_comment.review_text_minus,
		comment = document.form_comment.review_text_comment,
		rating = document.form_comment.review_rating;

	if(document.form_comment.comment) {
		document.form_comment.comment.value = rating.value + separator + plus.value + separator + minus.value + separator + comment.value;
	}
}

$(function () {

	var $stars = $(".js-stars > .star"),
		$starsWrapper = $stars.parent(),
		selectedRating = 0;

	$stars.on("mouseenter", function () {
		var $this = $(this),
			index = $this.data('index');
		$starsWrapper.removeClass("rating-" + selectedRating);

		$stars.filter(":lt(" + index + ")").addClass("selected");
	});

	$stars.on("mouseleave", function () {
		var $this = $(this);
		$stars.removeClass("selected");
		$starsWrapper.addClass("rating-" + selectedRating);
	});

	$stars.on("click", function () {
		var $this = $(this),
			index = $this.data('index');

		if(selectedRating == index) {
			$starsWrapper.removeClass("rating-" + selectedRating);
			selectedRating = 0;
			document.form_comment.review_rating.value = 0;
			return false;
		}
		if(selectedRating != 0) {
			$starsWrapper.removeClass("rating-" + selectedRating);
		}
		$starsWrapper.addClass("rating-" + index);
		selectedRating = index;
		document.form_comment.review_rating.value = selectedRating;
	});

});
</script>
