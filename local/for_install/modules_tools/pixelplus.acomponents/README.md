# Полезные компоненты

**Описание решения**  
<div class="feed-post-text-block-inner-inner" id="blog_post_body_4890">**Компонент pixelplus.acomponents:catalog.section.detail**  
Иногда требуется вывести информацию о каком-либо разделе информационного блока.  Без знания программирования это можно сделать, например, с помощью компонента catalog.section. Но при использовании catalog.section мы столкнемся с рядом проблем: во-первых, компонент предназначен для вывода элементов – а, значит,  делает ненужные (в нашем случае) запросы к базе, а, во-вторых, не форматирует пользовательские поля. Т.е. если у нас есть пользовательское поле типа список, в шаблоне мы получим мы ид варианта списка, но не его значение.  
Для решения этих проблем был создан компонент pixelplus.acomponents: catalog.section.detail  
Особенности компонента:  

*   Выводит информацию о разделе  

*   Поддерживает ЧПУ  

*   В параметрах компонента разделены выбираемые поля из базы поля и поля, которые форматируются для вывода, а затем выводятся в шаблоне  

![](https://opt-99999999.ssl.1c-bitrix-cdn.ru/blog/6be/image001.png)  

*   Реализована возможность вывода пользовательских полей в отформатированном виде. Для них так же присутствует принцип разделения  

![](https://opt-99999999.ssl.1c-bitrix-cdn.ru/blog/050/image003.png)  

*   Галочка «Показывать только активные разделы» в параметрах компонента, позволяет определить, будет ли применяться фильтр по активности  

*   Галочка «Найти путь секции» появляется, если не проставлена галочка «Включать раздел в цепочку навигации». Используется для принудительного нахождения пути секции в дереве разделов.  

*   7. При выбранных на форматирования полях для «Изображения» и «Детальной картинки», имеется возможность настроить масштабирование изображений.  

![](https://opt-99999999.ssl.1c-bitrix-cdn.ru/blog/218/image005.png)  

*   Описание раздела можно разбить по страницам с помощью разделителя <BREAK /> Подобно тому, как это делает компонент news.detail (при условии что описание выбирается в форматируемых полях)  

Стандартный шаблон компонента на данный момент содержит минимальную верстку и выглядит следующим образом:  
![](https://opt-99999999.ssl.1c-bitrix-cdn.ru/blog/f30/image007.png)  
**Компонент pixelplus. acomponents: user. list**  
Данный компонент выводит пользователей сайта. Особенности:  

*   Возможность настроить в параметрах компонента вывод пользователей только из определенных групп  

*   Фильтр из $GLOBALS (аналогичный фильтру, реализованному в catalog.section)  

*   Форматируемые пользовательские поля UF_*  

*   Разделение выбираемых и выводимых (форматируемых) полей (имеет смысл с версии 11.0.13 главного модуля), в т.ч. пользовательских  

*   Возможность сортировки пользователей из параметров компонента по двум полям (до версии 11.0.13 главного модуля – по одному)  

*   Возможность вывести  дополнительные данные из модуля "Форум"  

В планах добавить резайз для фотографии пользователя.  
**  
API**  
Для реализации компонентов было написано несколько небольших классов, которые позволяют форматировать пользовательские поля. Поддерживаются все доступные на данный момент поля. Создано специальное событие, с помощью которого можно отформатировать собственное поле, или переопределить формат для стандартного.  
Пример использования:  

<div class="blog-post-code" title="Код">

<table class="blogcode">

<tbody>

<tr>

<td>

<pre class=" hljs php"><span class="hljs-variable">$obpxformatuf</span> = <span class="hljs-keyword">new</span> CPixelPlusFormatUF;
<span class="hljs-variable">$obpxformatuf</span>->Init(<span class="hljs-string">"IBLOCK_"</span>.<span class="hljs-variable">$arResult</span>[<span class="hljs-string">"IBLOCK_ID"</span>].<span class="hljs-string">"_SECTION"</span>);    
<span class="hljs-variable">$obpxformatuf</span>->SetFormatted(<span class="hljs-variable">$arParams</span>[<span class="hljs-string">"SECTION_F_PROPERTIES"</span>]); 
<span class="hljs-variable">$arResult</span>[<span class="hljs-string">'USER_FIELDS'</span>] = <span class="hljs-variable">$obpxformatuf</span>->GetEntityMeta(); 
<span class="hljs-variable">$obpxformatuf</span>->GetDispayFields(<span class="hljs-variable">$arResult</span>); </pre>

</td>

</tr>

</tbody>

</table>

</div>

$arResult – результат выборки CIBlockSection::GetList->GetNext() с фильтром по ID  
Методу Init передается пользовательский тип (метод выбирает все пользовательские поля для данного пользовательского типа и текущего языка [$GLOBALS["USER_FIELD_MANAGER"] ->GetUserFields])  
Методу SetFormatted передается массив пользовательских полей, которые нужно отформатировать для вывода, например Array("UF_FILE","UF_CITY"![;)](//opt-560835.ssl.1c-bitrix-cdn.ru/bitrix/images/main/smiles/3/bx_smile_wink.png?14416995737007 "Шутливо"). Для каждого значения типа enum получаем варианты списка.  
Метаданные пользовательского типа вместе с вариантами списков выбираем так:  
$obpxformatuf->GetEntityMeta();  
В итоге форматируем:  
$obpxformatuf->GetDispayFields($arResult); (Отформатированные значения появятся в ключе DISPLAY_PROPERTIES массива $arResult);  
Перед вызовом GetDispayFields возможно задать сепаратор, который будет использоваться для разделения множественных свойств при выводе. По умолчанию это  
" / ". Задается следующим образом $obpxformatuf->mseparator = ", ";  
Для каждого свойства при форматировании можно определить свой формат. Для этого нужно использовать событие OnFormatUF модуля pixelplus.acomponents  
$arEventRes = ExecuteModuleEventEx($arEvent, array($arValue,$arUFProps,$pid,$arParams));  
if (is_array($arEventRes)) return $arEventRes;  
Первый параметр - $arValue['VALUE'] / $arValue['~VALUE'] = $arElement["~".$pid]; - значения элемента.  
Второй параметр – метаданные пользовательского поля  
Третий параметр – код свойства (например, UF_CITY)  
Четвертый параметр – пользовательские параметры для форматирования свойства, задаются через класс CPixelPlusFormatParamsC ($this->paramformatclass->SetParams(массив) и т.д.).  

Модуль бесплатный. Ссылка для установки:  
<noindex>[http://marketplace.1c-bitrix.ru/solut...omponents/](http://marketplace.1c-bitrix.ru/solutions/pixelplus.acomponents/)</noindex></div>