<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
?>
<?
//echo "<pre>Template arParams: "; print_r($arParams); echo "</pre>";
//echo "<pre>Template arResult: "; print_r($arResult); echo "</pre>";
//exit();
?>

<?if (count($arResult["ERRORS"])):?>
	<?=ShowError(implode("<br />", $arResult["ERRORS"]))?>
<?endif?>
<?if (strlen($arResult["MESSAGE"]) > 0):?>
	<?=ShowNote($arResult["MESSAGE"])?>
<?endif?>
<form name="iblock_add" action="<?=POST_FORM_ACTION_URI?>" method="post" enctype="multipart/form-data">

	<?=bitrix_sessid_post()?>
<div class="b-detail-review__add">
									<script>
									$(function() {
										$("#b-review__add").click(function() {
											$(".b-review-form").show();
											$(".b-button__del").show();
											
											return false;
										});
										$(".b-button__del").click(function() {
											$(".b-review-form").hide();
											$(".b-button__del").hide();
											
											return false;
										});
										$(".b-rating__vote span").click(function() {
											var input = $(this).closest(".b-rating__vote").attr("id"),
												value = $(this).data("value");
												
											$("input[id='" + input + "']").val(value);
											$(".b-rating__vote .current").width(20 * value + "%");
										});
									});
									</script>
									<div class="b-detail-review__btn">
										<button class="b-button" id="b-review__add">Добавить отзыв</button>
										<a href="#" class="b-button__del"></a>
									</div>
									<div class="b-review-form">
										<div class="b-review-form__field">
											<label class="b-review-form__label">Название</label>
											<input type="text" class="b-review-form__input" name="PROPERTY[NAME][0]"/>
											<input type="hidden" class="b-review-form__input" name="PROPERTY[33][0]" value="<?=$arParams['ELEMENT_ID']?>"/>
										</div>
										<div class="b-review-form__field">
											<label class="b-review-form__label">Достоинства</label>
											<input type="text" class="b-review-form__input" name="PROPERTY[34][0][VALUE][TEXT]"/>
										</div>
										<div class="b-review-form__field">
											<label class="b-review-form__label">Недостатки</label>
											<input type="text" class="b-review-form__input" name="PROPERTY[35][0][VALUE][TEXT]"/>
										</div>
										<div class="b-review-form__field">
											<label class="b-review-form__label">Комментарий</label>
											<textarea rows="3" class="b-review-form__input" name="PROPERTY[PREVIEW_TEXT][0]"></textarea>
										</div>
										<div class="b-review-rating__add clearfix">
											<span class="b-rating__vote_text">Оценка:</span>
											<ul class="b-rating__vote" id="VOTE_1">
												<li class="current" style="width: 60%"><span class="star1" title="Рейтинг 1 из 5" data-value="1">Ужасно</span></li>
												<li><span class="star2" title="Рейтинг 2 из 5" data-value="2">Плохо</span></li>
												<li><span class="star3" title="Рейтинг 3 из 5" data-value="3">Нормально</span></li>
												<li><span class="star4" title="Рейтинг 4 из 5" data-value="4">Хорошо</span></li>
												<li><span class="star5" title="Рейтинг 5 из 5" data-value="5">Отлично</span></li>
											</ul>
											<input type="hidden" name="PROPERTY[38][0][VALUE]" value="" id="VOTE_1"/>
										</div>
										<div class="b-detail-review__btn">
											<input type="submit" class="b-button m-orange" name="iblock_submit" value="Отправить" />
										</div>
									</div>
								</div>
</form>