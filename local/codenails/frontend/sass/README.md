# Архитектура "Шаблон 7-1: 7 папок, 1 файл"

 ```
sass/
|
|– abstracts/
|   |– _variables.scss    # Sass переменные
|   |– _functions.scss    # Sass функции
|   |– _mixins.scss       # Sass миксины
|   |– _placeholders.scss # Sass плейсхолдеры
|
|– base/
|   |– _reset.scss        # Reset/normalize
|   |– _typography.scss   # Типографика
|   …                     # Прочее
|
|– components/
|   |– _buttons.scss      # Кнопки
|   |– _carousel.scss     # Карусель
|   |– _cover.scss        # Обложка
|   |– _dropdown.scss     # Выпадашка
|   …                     # и так далее
|
|– layout/
|   |– _navigation.scss   # Навигация
|   |– _grid.scss         # Сетка
|   |– _header.scss       # Шапка
|   |– _footer.scss       # Футер
|   |– _sidebar.scss      # Сайдбар
|   |– _forms.scss        # Формы
|   …                     # и так далее
|
|– pages/
|   |– _home.scss         # Специфичные стили страницы Home
|   |– _contact.scss      # Специфичные стили страницы Contact
|   …                     # и так далее
|
|– themes/
|   |– _theme.scss        # Тема по умолчанию
|   |– _admin.scss        # Тема админа
|   …                     # и так далее
|
|– vendors/
|   |– _bootstrap.scss    # Bootstrap
|   |– _jquery-ui.scss    # jQuery UI
|   …                     # и так далее
|
`– main.scss              # Главный Sass файл
```
Если вы хотите использовать паттерн 7-1, вот готовый шаблон [GitHub](https://github.com/HugoGiraudel/sass-boilerplate) на Гитхабе. В нём содержится всё что нужно для начала работы по этой архитектуре.