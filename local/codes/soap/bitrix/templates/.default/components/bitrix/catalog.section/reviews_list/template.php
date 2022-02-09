<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<div class="b-detail-review">
								<div class="b-detail-review__add">
									<script>
        $(document).ready(
            function(){
                //ajax добавление в корзину
                vote_btns = $('a[href*="vote"]');
                vote_btns.each(
                    function(){
                        $(this).attr("rel", $(this).attr("href"));
                    }
                );
                vote_btns.attr("href","javascript:void(0);");

                $('a[rel*="vote"]').click(                
                    function(){
                        var voteid = $(this).attr("id");
						var tag = $(this).attr("tag");
						var dataurl = "voteid=" + voteid +"&tag=" + tag;
						$.ajax({
                                type: "POST",
                                url: "/includes/vote.php",
								data: $(this).attr("rel"),
                                dataType: "html",
                                success: function(out){
if(parseInt($(out).text(), 10)!=""){
$(out).text(parseInt($(out).text(), 10) + 1);
}else{
$(out).text(parseInt(1));
}					}
						});
					}
				);
			}
		);
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
												
											$("input[name='" + input + "']").val(value);
											$(".b-rating__vote .current").width(20 * value + "%");
										});
									});
									</script>


</div>
		<?foreach($arResult["ITEMS"] as $cell=>$arElement):?>
		<?
		$this->AddEditAction($arElement['ID'], $arElement['EDIT_LINK'], CIBlock::GetArrayByID($arParams["IBLOCK_ID"], "ELEMENT_EDIT"));
		$this->AddDeleteAction($arElement['ID'], $arElement['DELETE_LINK'], CIBlock::GetArrayByID($arParams["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BCS_ELEMENT_DELETE_CONFIRM')));
		?>
<div class="b-detail-review__item">
			<div class="b-review-author clearfix">
			<div class="b-review-author__name">
<?
$rsUser = CUser::GetByID($arElement['CREATED_BY']);
$arUser = $rsUser->Fetch();
?>
			<a href="" class="b-review-author__link"><?=$arUser["LAST_NAME"];?></a>
			</div>
			<div class="b-review-author__date"><?=$arElement['DATE_CREATE']?></div>
			</div>
			<div class="b-review-rating">
				<span class="b-rating m-review-rating"><span style="width: <?=$arElement['PROPERTIES']['rating']['VALUE']*20?>%"></span></span>
				<span class="b-rating__text"><?=$arElement['NAME']?></span>
			</div>
<?if($arElement['PROPERTIES']['value']['VALUE']['TEXT']!=""):?>			
<div class="b-review-text"><b>Достоинства:</b> <?=$arElement['PROPERTIES']['value']['VALUE']['TEXT']?></div>
<?endif?>
<?if($arElement['PROPERTIES']['limitations']['VALUE']['TEXT']!=""):?>			
<div class="b-review-text"><b>Недостатки:</b> <?=$arElement['PROPERTIES']['limitations']['VALUE']['TEXT']?></div>
<?endif?>
<?if($arElement['PREVIEW_TEXT']!=""):?>			
<div class="b-review-text"><b>Комментарий:</b> <?=$arElement['PREVIEW_TEXT']?></div>
<?endif?>
			<div class="b-review-usefull">Отзыв полезен? <a href="?action=vote&id=<?=$arElement['ID']?>&tag=plus" class="b-review-usefull__yes">Да</a> <span id="yes"><?=$arElement['PROPERTIES']['helpful']['VALUE']?></span> / <a href="?action=vote&id=<?=$arElement['ID']?>&tag=minus" class="b-review-usefull__no">Нет</a> <span id="no"><?=$arElement['PROPERTIES']['useless']['VALUE']?></span></div>
</div>

		<?endforeach; // foreach($arResult["ITEMS"] as $arElement):?>
</div>
<!--<div><button class="b-button">Показать следующие 5 отзывов</button></div>-->
