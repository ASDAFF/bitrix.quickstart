<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();?>

<div class="agreement-wrapper clearfix">
    <p class="agreement">
        
		<label class="nosiform">
        <input class="agreement-input" value="Y" checked="" name="" type="checkbox">
		
            <a href="javascript:;" data-fancybox="modal" data-src="#agreement-detail">
                Я даю свое согласие на обработку моих
                персональных данных, в соответствии с Федеральным законом от 27.07.2006 года №152-ФЗ «О персональных данных», на условиях и для целей,
                определенных в Согласии на обработку персональных данных
            </a>
        </label>
    </p>
    <div id="agreement-detail" style="display: none">
        <div class="agreement-detail-wrapper">
            <?$APPLICATION->IncludeFile("/include/agreement.php",Array(),Array("MODE"=>"php"));?>
        </div>
    </div>
</div>
<script>
    $(".agreement-input").change(function(){
        $(this).parents("form").find(".checkout").toggleClass("disabled");
    });
</script>
<style>

</style>