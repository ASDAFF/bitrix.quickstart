document.ondragstart = noselect; 
// запрет на перетаскивание 
document.onselectstart = noselect; 
// запрет на выделение элементов страницы 
document.oncontextmenu = noselect; 
// запрет на выведение контекстного меню 
function noselect() {return false;} 