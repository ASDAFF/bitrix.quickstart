https://mattweb.ru/moj-blog/bitriks/item/43-spisok-elementov-infobloka-s-nazvaniyami-razdelov-v-kachestve-zagolovkov

## Список элементов инфоблока с названиями разделов в качестве заголовков

_Как известно в «1С-Битрикс» нет стандартного компонента, который бы выводил элементы инфоблока, расположенные в разделах, таким образом, чтобы название раздела было заголовком списка элементов из этого раздела. Однажды мне понадобилось вывести список проектов одной компании на страницу сайта описанным выше образом. Я нашел 2 способа решения этой задачи, о которых расскажу в этой статье._

Решать эту задачу я буду на демо-сайте CMS «1С-Битрикс. Управление сайтом» редакция «Стандарт». При установке демо-версии сайта и выбора типа решения для демонстрации, нужно выбрать версию для разработчиков. В версии для разработчиков существует инфоблок «Книги». Его элементы распределены по разделам. С ним я и буду работать.

Все файлы, созданные мной в процессе работы над задачей, я помещу в архив.

Небольшое ограничение: при указании раздела первого уровня, элементы, находящиеся в нем, в списке отображаться не будут, будут отображаться только элементы из подразделов раздела первого уровня. Я немного расширил скрипт так, что элементы внутри раздела первого уровня тоже отображаются в списке. Расширенная версия находится в файле list-1f.php.