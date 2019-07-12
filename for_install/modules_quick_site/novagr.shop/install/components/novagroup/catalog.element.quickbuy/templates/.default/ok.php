<?php if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
?>
<div class="modal-header">
    <button aria-hidden="true" data-dismiss="modal" class="close" type="button">&times;</button>
    <h3 id="myModalLabel-01"><?=GetMessage('BUY_ONE_CLICK')?></h3>
</div>
<div class="modal-body">
    <input type="hidden" name="colorId" value="0">
    <input type="hidden" name="sizeId" value="0">
    <input type="hidden" name="productId" value="0">
    <input type="hidden" name="url" value="">

    <div id="autorize_inputs_i">
        <div class="login">
            <div>
                <?=GetMessage('THANKS')?>
            </div>
        </div>

    </div>
    <div class="clear"></div>

</div>
<script type="text/javascript">/*setTimeout(function(){$('#oneClick').modal('hide')},1000)*/</script>