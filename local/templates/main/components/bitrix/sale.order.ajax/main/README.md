В файле template.php блоки заказа отмечены комментариями:

```
BASKET ITEMS BLOCK - корзина заказа - таблица товаров
REGION BLOCK - выбор типа плательщика и ввод местоположения - города покупателя
DELIVERY BLOCK - выбор службы доставки
PAY SYSTEMS BLOCK - выбор способа оплаты
BUYER PROPS BLOCK - форма с полями для ввода данных покупателя
ORDER SAVE BLOCK - итог и кнопка подтверждения заказа
```

Чтобы все блоки были активны, а не только первый комментируем вторую строчку в данном блоке

```javascript
initFirstSection: function()
{
	var firstSection = this.orderBlockNode.querySelector('.bx-soa-section.bx-active');
	//BX.addClass(firstSection, 'bx-selected');
	this.activeSectionId = firstSection.id;
},
```

Если доставки выдают ошибку ошибку вычислений, то блоки с доставкой скрываются. Отключить это можно закомментировав следующий код:

```javascript
/*if (this.result.DELIVERY.length > 0)
{
	BX.addClass(this.deliveryBlockNode, 'bx-active');
	this.deliveryBlockNode.removeAttribute('style');
}
else
{
	BX.removeClass(this.deliveryBlockNode, 'bx-active');
	this.deliveryBlockNode.style.display = 'none';
}*/
```

Убираем сокрытие блоков при авторизации (при выключенной в настройках опции "регистрировать вместе с оформлением заказа").

```javascript
if (this.result.SHOW_AUTH && section.id != this.authBlockNode.id && section.id != this.basketBlockNode.id)
	section.style.display = 'none';
else if (section.id != this.pickUpBlockNode.id)
	section.style.display = '';
```

##Открыть все блоки и убрать лишнее

Чтобы раскрыть все скрытые блоки можно воспользоваться следующими методами (лично использовал на версиях до 20):

Ищем строку var active = section.id == this.activeSectionId и меняем ее на

```javascript
var active = true
```

Далее отключаем реагирование на клик по заголовку блока

```javascript
BX.unbindAll(titleNode);

if (this.result.SHOW_AUTH)
{
	BX.bind(titleNode, 'click', BX.delegate(function(){
		this.animateScrollTo(this.authBlockNode);
		this.addAnimationEffect(this.authBlockNode, 'bx-step-good');
	}, this));
}
else
{
	BX.bind(titleNode, 'click', BX.proxy(this.showByClick, this));
	editButton = titleNode.querySelector('.bx-soa-editstep');
	editButton && BX.bind(editButton, 'click', BX.proxy(this.showByClick, this));
}
```

Чтобы всегда были открыты Регион и Пользователь

```javascript
if (this.activeSectionId !== this.regionBlockNode.id)
	this.editFadeRegionContent(this.regionBlockNode.querySelector('.bx-soa-section-content'));

if (this.activeSectionId != this.propsBlockNode.id)
	this.editFadePropsContent(this.propsBlockNode.querySelector('.bx-soa-section-content'));
```

Чтобы убрать кнопки Далее/Назад

```javascript
node.appendChild(
	BX.create('DIV', {
		props: {className: 'row bx-soa-more'},
		children: [
			BX.create('DIV', {
				props: {className: 'bx-soa-more-btn col-xs-12'},
				children: buttons
			})
		]
	})
);
```

Чтобы убрать ссылки «изменить» у всех блоков в editOrder (~2222 стр.)
в конец функции editOrder добавляем код

```javascript
var editSteps = this.orderBlockNode.querySelectorAll('.bx-soa-editstep'), i;
 for (i in editSteps) {
	if (editSteps.hasOwnProperty(i)) {
	   BX.remove(editSteps[i]);
	}
 }
```

Чтобы скрыть уведомление о том, что данный заполнены автоматически добавляем в конец файла style.css

```css
.alert.alert-warning{display:none;}
```
##Определение местоположения пользователя в автоматическом режиме

```php
<?php

use Bitrix\Main\EventManager; 
$eventManager = EventManager::getInstance();
$eventManager->addEventHandler("sale", "OnSaleComponentOrderProperties", Array("Example", "OnSaleComponentOrderProperties"));

class Example
{
   /**
   * У меня по условию задачи известны ID и NAME местоположения
   */
   static $curCityId = XX;  // числовое значение идентификатора местоположения
   static $curCityName = 'Название города';
   
   /**
   * ID свойств заказа   
   */
   const PROP_LOCATION = 6; 
   const PROP_ZIP = 4; 
   const PROP_LOCATION_NAME = 5;


   static function OnSaleComponentOrderProperties(&$arFields)
   {
      $rsLocaction = CSaleLocation::GetLocationZIP(self::$curCityId); 
      $arLocation = $rsLocaction->Fetch(); 
      $arFields['ORDER_PROP'][self::PROP_ZIP] = $arLocation['ZIP'];
      $arFields['ORDER_PROP'][self::PROP_LOCATION_NAME] = self::$curCityName;
      $arFields['ORDER_PROP'][self::PROP_LOCATION] = CSaleLocation::getLocationCODEbyID(self::$curCityId);
   }
}
```

А вот такая модификация позволяет определить местоположение только по названию города:

```php
<?php

class Example {
   static function OnSaleComponentOrderProperties(&$arFields)
   {
      static $curCityName = 'Название города';
      const PROP_LOCATION = 6;  // - Идентификатор свойства с местоположением
      static function OnSaleComponentOrderProperties(&$arFields)
      {
         $res = Bitrix\Sale\Location\LocationTable::getList(array(
         'filter' => array('=NAME.NAME' => self::$curCityName, '=NAME.LANGUAGE_ID' => LANGUAGE_ID),
         'select' => array('CODE' => 'CODE', 'NAME_RU' => 'NAME.NAME', 'TYPE_CODE' => 'TYPE.CODE') //'*', 
         ));
         while($item = $res->fetch())
         {            
            $code = $item["CODE"];
         }
         $arFields['ORDER_PROP'][self::PROP_LOCATION] = $code;
      }
   }
}
```

##Скрыть какое-то свойство

Если необходимо скрыть какое-то свойство, например, свойство индекс - задать значение по умолчанию и не показывать пользователям это поле, то можно внести корректировку в JS. В функции getPropertyRowNode после switch (propertyType) добавляем скрытие данного свойства:

```javascript
if(property.getId()==6){// идентификатор скрываемого свойства
   var addressInput=propsItemNode.querySelector('textarea');
   propsItemNode.style='display:none;';
   addressInput.value='нужное значение';
}
```

Скрываем сообщение "Выберите свой город в списке. Если вы не нашли свой город, выберите "другое местоположение", а город впишите в поле "Город"
Идем в функцию getDeliveryLocationInput и комментируем код:

```javascript
/*
if (location && location[0])
{
	node.appendChild(
		BX.create('DIV', {
			props: {className: 'bx-soa-reference'},
			html: this.params.MESS_REGION_REFERENCE
		})
	);
}
*/
```

Или с помощью стилей скрываем класс bx-soa-reference

##Исключить из показа нулевой цены за доставку

В функции getDeliveryPriceNodes: function(delivery) в блоке "else" заменяем. Вместо:

``priceNodesArray = [delivery.PRICE_FORMATED];``

пишем:

``if(delivery.PRICE>0) priceNodesArray = [delivery.PRICE_FORMATED];``

Так мы спрячем нулевую цену из свернутого блока с выбранной доставкой.

Дальше нужно скрыть нули в списке служб доставки. Для этого в функции createDeliveryItem: function(item) делаем строгую проверку на ноль. Вместо:

``if (item.PRICE >= 0 || typeof item.DELIVERY_DISCOUNT_PRICE !== 'undefined')``

пишем:

``if (item.PRICE > 0 || typeof item.DELIVERY_DISCOUNT_PRICE !== 'undefined')``

А также вместо:

``else if (deliveryCached && (deliveryCached.PRICE >= 0 || typeof deliveryCached.DELIVERY_DISCOUNT_PRICE !== 'undefined'))``

пишем:

``else if (deliveryCached && (deliveryCached.PRICE > 0 || typeof deliveryCached.DELIVERY_DISCOUNT_PRICE !== 'undefined'))``

И последним нужно скрыть нулевую доставку из итоговых сумм. Для этого в функции editTotalBlock: function() также ставим строгую проверку на ноль. Вместо

``if (parseFloat(total.DELIVERY_PRICE) >= 0 && this.result.DELIVERY.length)``

пишем:

``if (parseFloat(total.DELIVERY_PRICE) > 0 && this.result.DELIVERY.length)``

В результате нулевая доставка не будет показана пользователю.

##Перенос полей "Адрес доставки", "Индекс", "Комментарии", "Местоположения" в sale.order.ajax

###Убираем поле "Адрес доставки" из вывода блока "Пользователь"

Описанные у моих коллег способы в моем случае не сработали. Решил пойти некрасивым, но действенным способом

Идем в функцию editPropsItemsи находим код:

```javascript
if (
	this.deliveryLocationInfo.loc == property.getId()
	|| this.deliveryLocationInfo.zip == property.getId()
	|| this.deliveryLocationInfo.city == property.getId()
)
```

Меняем его на:

```javascript
if (
	this.deliveryLocationInfo.loc == property.getId()
	|| this.deliveryLocationInfo.zip == property.getId()
	|| this.deliveryLocationInfo.city == property.getId()
	|| property.getName()=='Адрес доставки (улица, дом, квартира)' //где property.getName() приравниваем к названию поля адреса в вашей системе
)
```

Поле индекс переносится абсолютно аналогично

##Вывод поля "Адрес доставки" в блоке "Доставка"

Идем в функцию editDeliveryInfo и в самый конец добавляем код:

```javascript
var deliveryItemsContainer = BX.create('DIV', {props: {className: 'col-sm-12 bx-soa-delivery'}}),
	group, property, groupIterator = this.propertyCollection.getGroupIterator(), propsIterator;

if (!deliveryItemsContainer)
	deliveryItemsContainer = this.propsBlockNode.querySelector('.col-sm-12.bx-soa-delivery');

while (group = groupIterator())
{
	propsIterator =  group.getIterator();
	while (property = propsIterator())
	{
		if (property.getName()=='Адрес доставки (улица, дом, квартира)') { //Если свойство совпадает с названием поля адреса в вашей системе

			this.getPropertyRowNode(property, deliveryItemsContainer, false); //вставляем свойство в подготовленный контейнер
			deliveryNode.appendChild(deliveryItemsContainer); //контейнер вместе со свойством в нём добавляем в конце блока с описанием (deliveryInfoContainer)

		}
	}
}
```

##Переносим поле "Комментарии к заказу" в конец формы

Идем в функцию editActivePropsBlock и комментируем строчку:

``this.editPropsComment(propsNode);``

Для вывода поля ищем в шаблоне функцию последнего блока. В моем случае это вывод состава заказа. Ищем функцию editBasketItems и в самый конец дописываем:

``this.editPropsComment(basketItemsNode);``

##Переносим поле "Местоположения" в блок пользовательских свойств

По умолчанию данное поле искуственно исключено из блока пользовательских свойств. Чтобы его вернуть на место идем в функцию editPropsItems и удаляем код:

```javascript
if (
	this.deliveryLocationInfo.loc == property.getId()
	|| this.deliveryLocationInfo.zip == property.getId()
	|| this.deliveryLocationInfo.city == property.getId()
)
	continue;
```

##Запрет Битриксу выбирать доставку по умолчанию

В случае, если у пользователя есть сохраненный профиль, ему автоматически выберется последняя выбранная им доставка, но битрикс ничего не знает о том, что у нас там еще и обязательные поля. Поэтому убираем дефолтный выбор доставки в обработчике OnSaleComponentOrderJsDataHandler. Он у нас уже есть, дописываем в него:

```php
<?php

if (isset($arResult['JS_DATA']['LAST_ORDER_DATA']['DELIVERY'])
 && $arResult['JS_DATA']['LAST_ORDER_DATA']['DELIVERY']!='') {
    $arResult['JS_DATA']['LAST_ORDER_DATA']['DELIVERY'] = '';}
```

В данном случае блок с доставками всегда будет открыт, и пользователь сразу обратит внимание на необходимость заполнения полей. Но! Если пользователь в кабинете удаляет профиль, поле с местоположением будет у него незаполнено, и после его заполнения блок с доставками автоматически закроется без возможности его отредактировать (пропадет кнопка "Изменить"). Это очень трудно пофиксить, чтобы не посыпалось всё остальное, поэтому мы приняли решение убрать возможность редактирования профилей в кабинете пользователя (делается снятием галочки в настройках компонента личного кабинета)

На данный момент у меня всё. Конечно, этот код был написан для конкретного проекта и с определенными допущениями. Но надеюсь, что данная заметка оказалась вам полезной и наведет вас на путь истинный при решении вашей задачи. Ибо документации по методам класса OrderAjaxComponent нет и не будет. Если Вам есть что добавить или поправить - буду рада комментариям.

#order_ajax_ext.js

Создаём файл order_ajax_ext.js в папке с шаблоном компонента sale.order.ajax (там же, где лежит файл order_ajax.js) с содержимым:

```javascript
(function () {
    'use strict'; 
 
    var initParent = BX.Sale.OrderAjaxComponent.init,
        getBlockFooterParent = BX.Sale.OrderAjaxComponent.getBlockFooter,
        editOrderParent = BX.Sale.OrderAjaxComponent.editOrder
        ;
 
    BX.namespace('BX.Sale.OrderAjaxComponentExt');    
 
    BX.Sale.OrderAjaxComponentExt = BX.Sale.OrderAjaxComponent;
 
	//Пример перехвата стандартной функции
    BX.Sale.OrderAjaxComponentExt.init = function (parameters) {
        initParent.apply(this, arguments);
 
        var editSteps = this.orderBlockNode.querySelectorAll('.bx-soa-editstep'), i;
        for (i in editSteps) {
            if (editSteps.hasOwnProperty(i)) {
                BX.remove(editSteps[i]);
            }
        }
 
    }; 
})();
```

В отдельных переменных определяем функции-методы родительского BX.Sale.OrderAjaxComponent, чтобы их можно было вызвать в дочерних функциях и не получить ошибку Maximum call stack size exceeded.

Копируем ссылку с BX.Sale.OrderAjaxComponent в BX.Sale.OrderAjaxComponentExt.

В методе BX.Sale.OrderAjaxComponentExt.init вызываем родительский init, следом прибиваем ссылки «изменить» у всех блоков. Они нам не нужны.

В методе BX.Sale.OrderAjaxComponentExt.getBlockFooter прибиваем кнопки «Назад» и «Вперед» у блоков. Они нам тоже не понадобятся — все блоки у нас развёрнуты.

В методе BX.Sale.OrderAjaxComponentExt.editOrder ненужным блокам-секциям добавляем css-класс bx-soa-section-hide. По нему мы и будем скрывать ненужные блоки. А так же в этом методе раскрываем только нужные нам блоки: «Покупатель» и «Товары в заказе».

Метод BX.Sale.OrderAjaxComponentExt.initFirstSection оставляем просто пустым. Если этого не сделать, то у анонимов при попытке оформления будет вываливаться эксепшен, по поводу отсутствия необходимых обязательных полей.

Идем дальше.

В файле template.php нашего шаблона нового оформления добавляем подключение нашего скрипта order_ajax_ext.js

После строчки:

`$this->addExternalJs($templateFolder.'/order_ajax.js');`

добавляем:

`$this->addExternalJs($templateFolder.'/order_ajax_ext.js');`

А так же в файле template.php меняем все вызовы BX.Sale.OrderAjaxComponent на BX.Sale.OrderAjaxComponentExt

Ну и не забываем добавить в файл стилей, чтобы ненужные блоки скрылись

```css
.bx-soa-section-hide {
    display: none;
}
```

###Краткое описание функций sale.order.ajax

showValidationResult: function(inputs, errors) - функция в которой полям с ошибкой добавляется класс hasError, который помечает ошибкой(в стандартном варианте добавляет обводку красным).

showErrorTooltip: function(tooltipId, targetNode, text) - функция в которой добавляются тултипы для полей с ошибкой.

showError: function(node, msg, border) - функция в которой выводятся ошибки в «групповой контейнер»

refreshOrder: function(result) - функция в которой происходит разбор ошибок, которые приходят от сервера. Там есть ветка result.error

Первые 3 функции отвечают за валидацию на форме без перезагрузки, а четвёртая за обработку результатов от сервера.

##Выполнение кода после перезагрузки страницы
  Бывает, что нужно регулярно выполнить код после перезагрузки страницы (изменения опций заказа). Например, требуется перерисовать селект. Это просто. Откройте файл order_ajax.js и в самый конец допишите:
  
```javascript
(function ($) {
	$(function() {
		BX.addCustomEvent('onAjaxSuccess', function(){
			$(function() {
				$('select').ikSelect({
					autoWidth:false,
					ddFullWidth:false
				});
			});
		});

		$('select').ikSelect({
			autoWidth:false,
			ddFullWidth:false
		});

		window.onresize = function() {
			$('select').ikSelect('redraw');
		}
	});
})(jQuery);
```

Для стилизации селектов, которые по умолчанию выглядят ужасно лучше всего использовать замечательную библиотеку [ikSelect](https://github.com/Igor10k/ikSelect)

##Программная смена города в sale.order.ajax на javascript

Часто, по техническому заданию, требуется программная смена города на javascript с ajax перезагрузкой страницы и перерасчетом формы. 

Будем считать, что у вас уже есть code локации из местоположений Битрикс. Тогда в части кода где необходимо произвести смену локации пишем:

```javascript
//Данный код будет работать не только внутри sale.order.ajax, но и во внешних скриптах, поскольку мы обращается к его функциям через пространство имен BX.Sale.OrderAjaxComponent
var code = '0000000001';//Ваш код локации. В данном случае это Белоруссия

$('.dropdown-field').val(code);//Обновляем код в скрытом инпуте
//$('.bx-ui-sls-fake').val(code);

BX.Sale.OrderAjaxComponent.params.newCity=code; //Записываем в параметры компонента необходимый нам город
BX.Sale.OrderAjaxComponent.sendRequest();//Обновляем форму
```

Здесь мы заполнили скрытый input нужным нам кодом и записали его в переменную, которой воспользуемся при перестроении формы. Идем в функцию prepareLocations (~1557 стр.).Находим код:

`temporaryLocations.push({`

И выше него пишем:

```javascript
//Делаем двойную проверку.
if(typeof this.params!='undefined'){//В первом случае на то, что параметры вообще установлены, поскольку код выполняется первый раз до их инициализации
	if(typeof this.params.newCity!='undefined'){//Вторая проверка - установлена ли наша переменная
		locations[i].lastValue = this.params.newCity;//Если переменная установлена, то подставляем ее в локацию
		delete this.params.newCity;//Обнуляем нашу переменную
	}
}
```

В итоге у меня получилось так:

```javascript
for (k in output)
{
	if (output.hasOwnProperty(k))
	{
		//Делаем двойную проверку.
		if(typeof this.params!='undefined'){//В первом случае на то, что параметры вообще установлены, поскольку код выполняется первый раз до их инициализации
			if(typeof this.params.newCity!='undefined'){//Вторая проверка - установлена ли наша переменная
				locations[i].lastValue = this.params.newCity;//Если переменная установлена, то подставляем ее в локацию
				delete this.params.newCity;//Обнуляем нашу переменную
			}
		}


		temporaryLocations.push({
			output: BX.processHTML(output[k], false),
			showAlt: locations[i].showAlt,
			lastValue: locations[i].lastValue,
			coordinates: locations[i].coordinates || false
		});
	}
}
```

###Расчет стоимости доставки для всех служб доставки

Будем полагать, что компонент sale.order.ajax вынесен у вас в отдельную папку

Тогда идем в файл /local/components/YOUR_NAMESPACE/sale.order.ajax/class.php и находим функцию protected function calculateDeliveries(Order $order) (~4289 стр)

Находим условие if ((int)$shipment->getDeliveryId() === $deliveryId) и в области else сразу после кода:

```php
<?php

$mustBeCalculated = $this->arParams['DELIVERY_NO_AJAX'] === 'Y' || ($this->arParams['DELIVERY_NO_AJAX'] === 'H' && $deliveryObj->isCalculatePriceImmediately());
```

пишем:

```php
<?php

$mustBeCalculated = true;
$calcResult = $deliveryObj->calculate($shipment);
$calcOrder = $order;
```

Теперь после обращения к серверу в наш order_ajax.js приходят службы доставки с рассчитанными стоимостями. Остается их только обработать и вывести.

В скрипте находим функциюcreateDeliveryItem: function(item) и работаем с параметром item.PRICE или item.PRICE_FORMATED и выводим его куда нужно.

##Получение стоимости доставки для продукта после применения скидок, правил корзины и ...

За скрипт спасибо [Денису Мягкову](https://dev.1c-bitrix.ru/support/forum/messages/forum6/topic103921/message576775/#message576775)

```php
<?php

/**
 * Получение стоимости доставки для продукта после применения скидок, правил корзины и ...
 *
 * @param string|int $bitrixProductId Id битриксового продукта
 * @param string     $siteId          Id битриксового сайта, например "s1"
 * @param string|int $userId          Id битриксового пользователя
 * @param string|int $personTypeId    Id битриксового "Тип плательщика" /bitrix/admin/sale_person_type.php?lang=ru
 * @param string|int $deliveryId      Id битриксового "Службы доставки" /bitrix/admin/sale_delivery_service_list.php?lang=ru&filter_group=0
 * @param string|int $paySystemId     Id битриксового "Платежные системы" /bitrix/admin/sale_pay_system.php?lang=ru
 * @param array      $userCityId      Id битриксового города ("куда доставлять")
 *
 * @return null|float null - не удалось получить; float - стоимость (может быть 0 (после применения скидок на доставку))
 *
 * @throws \Bitrix\Main\ArgumentException
 * @throws \Bitrix\Main\ArgumentNullException
 * @throws \Bitrix\Main\ArgumentOutOfRangeException
 * @throws \Bitrix\Main\ArgumentTypeException
 * @throws \Bitrix\Main\LoaderException
 * @throws \Bitrix\Main\NotImplementedException
 * @throws \Bitrix\Main\NotSupportedException
 * @throws \Bitrix\Main\ObjectException
 * @throws \Bitrix\Main\ObjectNotFoundException
 * @throws \Bitrix\Main\SystemException
 */
function getDeliveryPriceForProduct($bitrixProductId, $siteId, $userId, $personTypeId, $deliveryId, $paySystemId, $userCityId)
{
    $result = null;

    \Bitrix\Main\Loader::includeModule('catalog');
    \Bitrix\Main\Loader::includeModule('sale');

    $products = array(
        array(
            'PRODUCT_ID' => $bitrixProductId,
            'QUANTITY'   => 1,
            // 'NAME'       => 'Товар 1', 
            // 'PRICE' => 500,
            // 'CURRENCY' => 'RUB',
        ),
    );
    /** @var \Bitrix\Sale\Basket $basket */
    $basket = \Bitrix\Sale\Basket::create($siteId);
    foreach ($products as $product) {
        $item = $basket->createItem("catalog", $product["PRODUCT_ID"]);
        unset($product["PRODUCT_ID"]);
        $item->setFields($product);
    }

    /** @var \Bitrix\Sale\Order $order */
    $order = \Bitrix\Sale\Order::create($siteId, $userId);
    $order->setPersonTypeId($personTypeId);
    $order->setBasket($basket);

    /** @var \Bitrix\Sale\PropertyValueCollection $orderProperties */
    $orderProperties = $order->getPropertyCollection();
    /** @var \Bitrix\Sale\PropertyValue $orderDeliveryLocation */
    $orderDeliveryLocation = $orderProperties->getDeliveryLocation();
    $orderDeliveryLocation->setValue($userCityId); // В какой город "доставляем" (куда доставлять).

    /** @var \Bitrix\Sale\ShipmentCollection $shipmentCollection */
    $shipmentCollection = $order->getShipmentCollection();

    $delivery = \Bitrix\Sale\Delivery\Services\Manager::getObjectById($deliveryId);
    /** @var \Bitrix\Sale\Shipment $shipment */
    $shipment = $shipmentCollection->createItem($delivery);

    /** @var \Bitrix\Sale\ShipmentItemCollection $shipmentItemCollection */
    $shipmentItemCollection = $shipment->getShipmentItemCollection();
    /** @var \Bitrix\Sale\BasketItem $basketItem */
    foreach ($basket as $basketItem) {
        $item = $shipmentItemCollection->createItem($basketItem);
        $item->setQuantity($basketItem->getQuantity());
    }

    /** @var \Bitrix\Sale\PaymentCollection $paymentCollection */
    $paymentCollection = $order->getPaymentCollection();
    /** @var \Bitrix\Sale\Payment $payment */
    $payment = $paymentCollection->createItem(
        \Bitrix\Sale\PaySystem\Manager::getObjectById($paySystemId)
    );
    $payment->setField("SUM", $order->getPrice());
    $payment->setField("CURRENCY", $order->getCurrency());

    // $result = $order->save(); // НЕ сохраняем заказ в битриксе - нам нужны только применённые "скидки" и "правила корзины" на заказ.
    // if (!$result->isSuccess()) {
    //     //$result->getErrors();
    // }

    $deliveryPrice = $order->getDeliveryPrice();
    if ($deliveryPrice === '') {
        $deliveryPrice = null;
    }
    $result = $deliveryPrice;

    return $result;
}

// Использование
$deliveryPriceForProductCourier = getDeliveryPriceForProduct(
    $bitrixProductId,
    SITE_ID,
    $USER->GetID(),
    '1', // Юридическое лицо  /bitrix/admin/sale_person_type.php?lang=ru
    '1386', // Доставка курьером до дома (в случае наличия "профиля" - указываем его id)  /bitrix/admin/sale_delivery_service_edit.php?lang=ru
    '37', // Наличными или картой при получении  /bitrix/admin/sale_pay_system.php?lang=ru
    $userCity['ID'] // Город пользователя
);
```