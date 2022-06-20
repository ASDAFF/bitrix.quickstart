<?
/**
 * Copyright (c) 25/7/2019 Created By/Edited By ASDAFF asdaff.asad@yandex.ru
 */

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();


if (empty($arResult['ERRORS'])) {
	if ($arParams['USE_JQUERY'])
		$APPLICATION->AddHeadString('<script type="text/javascript" src="/local/components/eshop/ajaxBasket/js/jquery-1.9.0.min.js"></script>', true);
    if ($arParams['USE_AJAXFORM'])
		$APPLICATION->AddHeadString('<script type="text/javascript" src="/local/components/eshop/ajaxBasket/js/jquery.form.js"></script>', true);
    if ($arParams['USE_FANCYBOX'])
		$APPLICATION->AddHeadString('<link rel="stylesheet" href="/local/components/eshop/ajaxBasket/js/jquery.fancybox.css?v=2.1.5" type="text/css" media="screen" /><script type="text/javascript" src="/local/components/eshop/ajaxBasket/js/jquery.fancybox.pack.js?v=2.1.5"></script>
', true);


}

?>

<script>


   
    $(document).ready( 
      function()
      {
             
         $(".various").fancybox({}); 
         var options = {
         url: '<?=$arParams['LINK']?>',
         type: "POST",
         success:
            function(responseText, statusText, xhr, $form) {
              
              <?if(strlen($arParams['LINK'])<=0):?>
                $("<?=$arParams['BASKET_CONTEINER']?>").html($(responseText).find("<?=$arParams['BASKET_CONTEINER']?>").html()); 
              <?endif;?>
                $(".various").click();
                    
                    <?if(strlen($arParams['INPUT_SELECTOR'])>0):?>
                      if($form.find("input<?=$arParams['INPUT_SELECTOR']?>").hasClass("with_text"))
                        $form.find("input<?=$arParams['INPUT_SELECTOR']?>").val("<?=$arParams['INPUT_TEXT']?>");
                    <?endif;?>
            
            }
         };
         
         <?if(strlen($arParams['LINK'])>0):?>
            options.target = "<?=$arParams['BASKET_CONTEINER']?>";
         <?endif;?>
         
         $("<?=$arParams['FORM_SELECTOR']?>").ajaxForm(options);         
      }
   );
</script>


<a class="various" href="#inline" style="text-align: center;"></a>
<div id="inline" style="width: 350px; display: none; text-align: center;">
	<h2>Товар добавлен в корзину</h2>

	<p style="text-align: center;">
		<a id="add_paragraph" title="Add" class="button button-blue" href="<?=$arParams['BASKET_URL']?>">Перейти в корзину</a>
		&nbsp;
		<a href="javascript:$.fancybox.close();">Продолжить покупки</a>
	</p>
	
</div>


