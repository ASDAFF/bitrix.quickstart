/**
 * @category	
 * @link		http://.ru
 * @revision	$Revision$
 * @date		$Date$
 */
 
/**
* Примеры реализации структурно-логических блоков страницы.
*/

/**
 * Общий функционал шаблона
 * Тут должен находиться весь функционал, используемый на любой странице сайта:
 * инициализация плагинов (placeholder, fancybox, и т.п.)
 * Также можно реализовать какую-то общую БЛ шаблона.
 */
application.blocks.common = function()
{
	/**
	 * Приватное свойство, ширина документа
	 *
	 * @var integer
	 */
	var docWidth = 0;
	 
	/**
	 * Приватное свойство, высота документа
	 *
	 * @var integer
	 */
	var docHeight = 0;
	
	/**
	 * Приватное свойство, ширина окна
	 *
	 * @var integer
	 */
	var windowWidth = 0;
	 
	/**
	 * Приватное свойство, высота окна
	 *
	 * @var integer
	 */
	var windowHeight = 0;
	
	/**
	 * Приватный метод, обработчик события изменения размеров окна браузера
	 *
	 * @return void
	 */
	var onWindowResize = function()
	{
		docWidth = $(document).width();
		docHeight = $(document).height();
		
		windowWidth = $(window).width();
		windowHeight = $(window).height();
		
		//Допустим, CSS нам не хватило, и возникла необходимость сделать так.
		//Почему нет?
		$('#half-life').height(Math.round(windowHeight / 2));
	};
	
	/**
	 * Публичный метод, возвращает ширину документа (реазизуем инкаспусляцию)
	 * Вызов из других контекстов: application.blocks.common.getDocWidth()
	 *
	 * @return integer
	 */
	this.getDocWidth = function()
	{
		return docWidth;
	};
	
	/**
	 * Публичный метод, возвращает высоту документа (реазизуем инкаспусляцию)
	 * Вызов из других контекстов: application.blocks.common.getDocHeight()
	 *
	 * @return integer
	 */
	this.getDocHeight = function()
	{
		return docHeight;
	};
	
	/**
	 * Публичный метод, возвращает ширину окна (реазизуем инкаспусляцию)
	 * Вызов из других контекстов: application.blocks.common.getWindowWidth()
	 *
	 * @return integer
	 */
	this.getWindowWidth = function()
	{
		return docWidth;
	};
	
	/**
	 * Публичный метод, возвращает высоту окна (реазизуем инкаспусляцию)
	 * Вызов из других контекстов: application.blocks.common.getWindowHeight()
	 *
	 * @return integer
	 */
	this.getWindowHeight = function()
	{
		return docHeight;
	};
	
	/**
	 * Инициализация блока.
	 * Вешаем плагины, обработчики и т.п.
	 *
	 * @return void
	 */
	this.init = function()
	{
		//Подключаем jQuery плагин placeholder
		$('input[placeholder], textarea[placeholder]').placeholder();
		
		//Вешаем обработчик на событие изменения размеров окна браузера
		$(window).resize(function()
		{
			onWindowResize();
		});
		//Вызываем обработчик для установки начального состояния
		onWindowResize();
	};
};




/**
 * Блок шаблона: шапка
 * Допустим она у нас хитрая и умеет показываться и прятаться. С анимацией.
 * И нужно, например, по клику на ссылку где-то в документе уметь ее
 * показать/спрятать.
 * Для этого пишем интерфейс блоку, а в обработчиках вызываем метод:
 * application.blocks.header.toggle()
 */
application.blocks.header = function()
{
	/**
	 * Приватное свойство, конфигурация блока
	 *
	 * @var object
	 */
	var config = {
		animationTime: 400,
		visible: true
	};
	
	/**
	 * Приватное свойство, ссылка на контекст.
	 * Если в каких-либо методах теряется контекст блока, то можно добавить
	 * ссылку на него, которая будет видна во всех методах блока.
	 *
	 * @var object
	 */
	var instance = this;
	
	/**
	 * Публичный метод, показывает шапку
	 * Вызов из других контекстов: application.blocks.header.show()
	 *
	 * @return void
	 */
	this.show = function()
	{
		$('#header')
			.stop()
			.slideDown(config.animationTime, function()
			{
				$(this).removeClass('hidden');
			});
		
		config.visible = true;
	};
	
	/**
	 * Публичный метод, прячет шапку
	 * Вызов из других контекстов: application.blocks.header.hide()
	 *
	 * @return void
	 */
	this.hide = function()
	{
		$('#header')
			.stop()
			.slideUp(config.animationTime, function()
			{
				$(this).addClass('hidden');
			});
		
		config.visible = false;
	};
	
	/**
	 * Публичный метод, показывает/прячет шапку
	 * Вызов из других контекстов: application.blocks.header.toggle()
	 *
	 * @return void
	 */
	this.toggle = function()
	{
		config.visible ? this.hide() : this.show();
	};
	
	/**
	 * Инициализация
	 *
	 * return void
	 */
	this.init = function()
	{
		//Допустим, при скролле документа нужно прятать/показывать шапку
		//при определенных условиях
		$(window).scroll(function()
		{
			var scrollTop = $(this).scrollTop();
			//Обращаемся к интерфейсу другого блока:
			var windowHeight = application.blocks.common.getWindowHeight();
			
			//instance - это и есть application.blocks.header
			if(scrollTop > windowHeight && config.visible)
				instance.hide();
			if(scrollTop < windowHeight && !config.visible)
				instance.show();
		}
	};
};




/**
 * Блок шаблона: слайдшоу
 * Допустим этот блок не всегда присутствует по странице и
 * управляется это через св-во раздела/страницы Bitrix.
 * Инициализуем такой блок только при необходимости.
 */
application.blocks.slideshow = function()
{
	/**
	 * Проверяет наличие блока
	 *
	 * @return boolean
	 */
	this.exists = function()
	{
		return $('#slideshow').length > 0;
	};
	
	/**
	 * Инициализация
	 *
	 * @return void
	 */
	this.init = function()
	{
		$('#slideshow').superPuperSlideShowPlugin({
			superPuperParam: 'superPuperValue'
		});
	};
};