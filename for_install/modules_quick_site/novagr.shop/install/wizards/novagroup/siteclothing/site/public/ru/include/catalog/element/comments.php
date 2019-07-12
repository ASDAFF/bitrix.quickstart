<?php
if ($arResult["COMMENTS_ON"] == 1) {

    ?>
    <div id="comment" class="tab-pane fade">

        <div class="coment ">
            <div class="wrap-tab">
                <h4><?=GetMessage('COMMENTS_LABEL')?> <a class="refresh refreshComments" href="#" title="<?=GetMessage('COMMENTS_LABEL2')?>"><span class="icon-refresh"></span></a></h4>

                <div class="comments-list" id="comments-list"></div>
                <form id="commenForm" method="post" action="<?=$ajaxUrl?>">

                    <div class="comment-refresh" id="comment-refresh" style="display:none">
                        <a class="refresh refreshComments" href="#" title="<?=GetMessage('COMMENTS_LABEL2')?>"><span class="icon-refresh"></span> <?=GetMessage('COMMENTS_LABEL2')?></a>
                    </div>


                    <div id="accordion2" class="accordion smiles-accordeon">
                        <div class="accordion-group">
                            <div class="accordion-heading">
                                <a href="#collapseOne" data-parent="#accordion2" data-toggle="collapse" class="accordion-toggle"><span class="icon-addcomment"></span> <?=GetMessage('COMMENTS_LABEL3')?></a>
                            </div>
                            <div class="accordion-body collapse" id="collapseOne">
                                <div class="accordion-inner">

                                    <div id="alert"></div>
                                    <input type="hidden" name="action" value="comment">
                                    <input type="hidden" name="productId" value="<?=$arResult["ELEMENT"]["ID"]?>">
                                    <input type="hidden" name="productCode" value="<?=$arResult["ELEMENT"]["CODE"]?>">


                                    <div id="controlGroupName" class="control-group">
                                    </div>
                                    <div id="controlGroupEmail" class="control-group">
                                    </div>

                                    <div id="controlGroupText" class="control-group">
                                        <textarea id="REVIEW_TEXT" tabindex="5"  rows="8" cols="35" name="REVIEW_TEXT" class="comments-form-comment"></textarea>
                                    </div>

                                    <div><input id="sendComment" type="submit" class="btn" value="<?=GetMessage('COMMENTS_LABEL5')?>"></div>

                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <div class="clear"></div>

    </div>

<?
}
?>