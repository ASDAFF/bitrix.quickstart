# template-bitrix-sale.order.ajax
Метом кастомизации трудоемкого шаблона bitrix sale.order.ajax

Метод заключаеться в том что бы скрыть основные оформление стандартного шаблона и с помощью JS вызывать нужные события!

# Стили для скрытия стандартного блока 

#bx-soa-main-notifications,

#bx-soa-auth,
#bx-soa-total-mobile,
#bx-soa-region,
#bx-soa-delivery,
#bx-soa-pickup,
#bx-soa-paysystem,
#bx-soa-properties,
#bx-soa-basket,
#bx-soa-total
{
	display: none !important;
}

.col-sm-9 bx-soa{
	width: 100%!important;
	height: auto!important;
	margin: 0!important;
	padding: 0!important;
}

# файл нового оформления 
template_view.php который надо подключать перед кнопкой оформления заказа, саму кнопку перенести в уже новое оформление!  

  <!--	ORDER SAVE BLOCK	-->
	<? if(is_file(dirname(__FILE__).'/template_view.php')){
		require_once dirname(__FILE__).'/template_view.php';
	}?>
  
  
