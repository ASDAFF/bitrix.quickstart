/*
 * Copyright (c) 25/7/2019 Created By/Edited By ASDAFF asdaff.asad@yandex.ru
 */

document.ondragstart = noselect; 
// запрет на перетаскивание 
document.onselectstart = noselect; 
// запрет на выделение элементов страницы 
document.oncontextmenu = noselect; 
// запрет на выведение контекстного меню 
function noselect() {return false;} 