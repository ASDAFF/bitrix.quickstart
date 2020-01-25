# Bitrix_Favorites
[Bitrix] Добавление товаров в избранное

Нужно реализовать функционал избранного. 

1. Для незарегистрированного пользователя мы будем записывать ID товаров в Cookies, для зарегистрированного в пользовательское поле, которое мы создадим.

![Создание пользовательского поля](img_md/favorites.png "Создание пользовательского поля")

2. После того как мы завели пользовательское поле UF_FAVORITES, нужно создать страницу и ссылку ``<a href="favoriteButton.php">Избранное</a>`` на избранное. 
В данном примере выводится количество добавленных товаров в избранное.

![Пример](img_md/favorite3.png "Пример")

3. Теперь нужно подготовить AJAX функцию, которая будет отправлять запрос с нужным нам ID товара, и добавлять его в избранное. Функция проверяет, есть ли товар в избранном или нет, если есть, то мы даём понять, что нам нужно удалить товар из избранного. Если операция прошла успешно, пересчитываем количество товаров в избранном и выводим нужое нам число.

Файл script.js

Пример
```javascript
$(document).ready(function() {
    /* Favorites */
    $('.favor').on('click', function(e) {
        var favorID = $(this).attr('data-item');
        if($(this).hasClass('active'))
            var doAction = 'delete';
        else
            var doAction = 'add';

        addFavorite(favorID, doAction);
    });
    /* Favorites */
});
/* Избранное */
    function addFavorite(id, action)
    {
        var param = 'id='+id+"&action="+action;
        $.ajax({
            url:     '/local/ajax/favorites.php', // URL отправки запроса
            type:     "GET",
            dataType: "html",
            data: param,
            success: function(response) { // Если Данные отправлены успешно
                var result = $.parseJSON(response);
                if(result == 1){ // Если всё чётко, то выполняем действия, которые показывают, что данные отправлены :)
                     $('.favor[data-item="'+id+'"]').addClass('active');
                     var wishCount = parseInt($('#want .col').html()) + 1;
                     $('#want .col').html(wishCount); // Визуально меняем количество у иконки
                }
                if(result == 2){
                     $('.favor[data-item="'+id+'"]').removeClass('active');
                     var wishCount = parseInt($('#want .col').html()) - 1;
                     $('#want .col').html(wishCount); // Визуально меняем количество у иконки
                }
            },
            error: function(jqXHR, textStatus, errorThrown){ // Если ошибка, то выкладываем печаль в консоль
                console.log('Error: '+ errorThrown);
            }
         });
    }
/* Избранное */
```

4. AJAX функция обращается к favorites.php, где идёт проверка, авторизован ли пользователь. Если да, то делаем запись в пользовательское поле, если не авторизован, то записываем ID товара в Cookies. 
Перед тем, как сделать запись, мы проверяем есть ли уже записи в данном массиве, если есть, нужно добавить запись к этому массиву.
Присутствует проверка на дублирование, чтобы не было повторений.

5. Мы сформировали функцию добавление товаров в избранное, добавили пару товаров в избранное, они записались в Cookies или в Пользовательское поле UF_FAVORITES. Если мы выведем массив '$_COOKIE', то должны обнаружить там добавленные ID товара.

Осталось правильным образом представить эти товары пользователю. Я визуализировал избранные товары, подобию каталога. И вызвал компонент <i>catalog.section</i>

![Пример](img_md/favorite2.png "Пример")

Перед вызовом компонента формируем фильтр с соответствующим условием. Если не авторизованы достаём Cookies методом get_cookie. Если авторизованы, возвращаем пользовательское поле UF_FAVORITES. И записываем данные $favorites в массив arrFilter, для фильтрации вывода товаров избранного.

```php
<? if(!$USER->IsAuthorized()) // Для неавторизованного
{
    global $APPLICATION;
	$favorites = unserialize(Application::getInstance()->getContext()->getRequest()->getCookie("favorites"));
}
else {
     $idUser = $USER->GetID();
     $rsUser = CUser::GetByID($idUser);
     $arUser = $rsUser->Fetch();
     $favorites = $arUser['UF_FAVORITES'];
    
}

$GLOBALS['arrFilter']=Array("ID" => $favorites);
if(count($favorites) > 0 && is_array($favorites)): ?>
```

Товары выводятся, добавляются и удаляются из избранного. Осталось сделать, чтобы значок избранного (например сердечко), показывал нам, добавлен ли товар в избранное или нет. Для этого обратимся к файлу <a href="component_epilog.php">component_epilog.php</a>, в котором будем добавлять соответствующий класс active к иконки избранного (середчко), который добавлен в избранное

```php
<? global $APPLICATION;

if(!$USER->IsAuthorized())
{
    $arFavorites = unserialize($APPLICATION->get_cookie("favorites"));
    //print_r($arFavorites);
}
else {
    $idUser = $USER->GetID();
    $rsUser = CUser::GetByID($idUser);
    $arUser = $rsUser->Fetch();
    $arFavorites = $arUser['UF_FAVORITES'];  // Достаём избранное пользователя
}
count($arFavorites);
/* Меняем отображение сердечка товара */
foreach($arFavorites as $k => $favoriteItem):?>
    <script>
        if($('a.favor[data-item="<?=$favoriteItem?>"]'))
            $('a.favor[data-item="<?=$favoriteItem?>"]').addClass('active');
    </script>
<?endforeach;?>
```

Готово !





