# Реализация кнопки ПОКАЗАТЬ ЕЩЁ вместо пагинации

Общий принцип сводится к следующему: при клике на "Показать ещё" мы посылаем на сервер запрос с параметрами такими же, как и обычная AJAX пагинация у компонента. Результат запроса мы не заменяем на существующий блок, а добавляем в него.

1. В вызове компонента прописываем `AJAX_MODE => Y`. При этом битрикс обрамляет блок компонента в div с айди компонента
2. В шаблоне компонента генерируем такой же айди:

```php 
$bxajaxid = CAjax::GetComponentID($component->__name, $component->__template->__name, $component->arParams['AJAX_OPTION_ADDITIONAL']);
```

3. В шаблоне отрисовываем кнопку "Показать ещё". Передаем в неё необходимые параметры:

```php 
<?if($arResult["NAV_RESULT"]->nEndPage > 1 && $arResult["NAV_RESULT"]->NavPageNomer<$arResult["NAV_RESULT"]->nEndPage):?>
	<div id="btn_<?=$bxajaxid?>">
		<a data-ajax-id="<?=$bxajaxid?>" href="javascript:void(0)" data-show-more="<?=$arResult["NAV_RESULT"]->NavNum?>" data-next-page="<?=($arResult["NAV_RESULT"]->NavPageNomer + 1)?>" data-max-page="<?=$arResult["NAV_RESULT"]->nEndPage?>">Показать еще комментарии</a>
	</div>
<?endif?>
```

4. Обрабатываем клик по кнопке, при этом сама кнопка удаляется:

```js
     $(document).on('click', '[data-show-more]', function(){
        var btn = $(this);
        var page = btn.attr('data-next-page');
        var id = btn.attr('data-show-more');
        var bx_ajax_id = btn.attr('data-ajax-id');
        var block_id = "#comp_"+bx_ajax_id;
        
        var data = {
            bxajaxid:bx_ajax_id
        };
        data['PAGEN_'+id] = page;

        $.ajax({
                type: "GET",
                url: window.location.href,
                data: data,
                timeout: 3000,
                success: function(data) {
                        $("#btn_"+bx_ajax_id).remove();
		        $(block_id).append(data);
                }
        });
    });
```
5. В шаблоне прячем некоторые элементы, которые не нужны в ответе на запрос "Показать ещё":

```php
    <?if(!$_GET["bxajaxid"]):?>
        {{something}}
    <?endif?>
```