<?
if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true)
	die();

$MESS['QUICKSERVICES']        = "Набор сервисов";
$MESS['QUICKSERVICES_VALUES'] = array(
	 'collections'   => 'Яндекс.Коллекции',
	 'vkontakte'     => 'ВКонтакте',
	 'facebook'      => 'Facebook',
	 'odnoklassniki' => 'Одноклассники',
	 'moimir'        => 'Мой Мир',
	 'gplus'         => 'Google+',
	 'twitter'       => 'Twitter',
	 'telegram'      => 'Telegram',
	 'viber'         => 'Viber',
	 'whatsapp'      => 'WhatsApp',
	 'skype'         => 'Skype',
	 'blogger'       => 'Blogger',
	 'delicious'     => 'Delicious',
	 'digg'          => 'Digg',
	 'evernote'      => 'Evernote',
	 'linkedin'      => 'LinkedIn',
	 'lj'            => 'Livejournal',
	 'pinterest'     => 'Pinterest',
	 'pocket'        => 'Pocket',
	 'qzone'         => 'Qzone',
	 'reddit'        => 'Reddit',
	 'renren'        => 'Renren',
	 'sinaWeibo'     => 'Sina Weibo',
	 'surfingbird'   => 'Surfingbird',
	 'tencentWeibo'  => 'Tencent Weibo',
	 'tumblr'        => 'Tumblr',
);

$MESS['LANG']        = 'Язык блока';
$MESS['LANG_VALUES'] = array(
	 'ru' => 'Русский',
	 'en' => 'Английский',
	 'az' => 'Азербайджанский',
	 'be' => 'Белорусский',
	 'hy' => 'Армянский',
	 'ka' => 'Грузинский',
	 'kk' => 'Казахский',
	 'ro' => 'Румынский',
	 'tr' => 'Турецкий',
	 'tt' => 'Татарский',
	 'uk' => 'Украинский',
);

$MESS['TYPE']        = 'Внешний вид блока';
$MESS['TYPE_VALUES'] = array(
	 'icon'    => 'Только иконки',
	 'counter' => 'Счетчики',
	 'limit'   => 'Иконки и меню',
);

$MESS['SIZE']        = 'Размер кнопок соцсетей';
$MESS['SIZE_VALUES'] = array(
	 'm' => 'Большой',
	 's' => 'Маленький',
);

$MESS['LIMIT']         = 'Количество соцсетей до меню';
$MESS['LIMIT_DEFAULT'] = '5';

$MESS['COPY']        = 'Позиция кнопки "Скопировать ссылку"';
$MESS['COPY_VALUES'] = array(
	 'first'  => 'Кнопка вверху списка',
	 'last'   => 'Кнопка внизу списка',
	 'hidden' => 'Кнопка не отображается',
);

$MESS['POPUP_DIRECTION']        = 'Направление открытия pop-up';
$MESS['POPUP_DIRECTION_VALUES'] = array(
	 'bottom' => 'bottom — вниз',
	 'top'    => 'top — вверх',
);

$MESS['POPUP_POSITION']        = 'Расположение pop-up относительно контейнера блока';
$MESS['POPUP_POSITION_VALUES'] = array(
	 'inner' => 'inner — внутри контейнера',
	 'outer' => 'outer — снаружи контейнера',
);


$MESS['UNUSED_CSS'] = 'Не использовать стили';

//GENERAL_SHARE
$MESS['GROUP_GENERAL_SHARE'] = 'Чем поделиться (общее)';
$MESS['DATA_TITLE']          = 'Заголовок, которым  поделиться [data-title]';
$MESS['DATA_URL']            = 'Ссылка, которой  поделиться [data-url]';
$MESS['DATA_IMAGE']          = 'Изображение, которым  поделиться [data-image]';
$MESS['DATA_DESCRIPTION']    = 'Текст, которым  поделиться [data-description]';

//SEPARATE_SHARE
$MESS['GROUP_SEPARATE_SHARE'] = 'Чем поделиться (отдельно)';
$MESS['SHARE_SERVICES']       = 'Набор сервисов';


//GROUP_TWITTER
$MESS['GROUP_TWITTER']    = 'twitter';
$MESS['twitter_hashtags'] = 'Хэштеги для твиттера';

//TIP
$MESS['POPUP_POSITION_TIP']   = 'Значение outer может понадобиться в том случае, если из-за специфики верстки вашего сайта pop-up обрезается соседними элементами страницы.';
$MESS['DATA_TITLE_TIP']       = 'Часто соцсети игнорируют параметры title и description и берут значения из семантической разметки страницы.';
$MESS['DATA_DESCRIPTION_TIP'] = 'Часто соцсети игнорируют параметры title и description и берут значения из семантической разметки страницы.';
$MESS['twitter_hashtags_TIP'] = 'Несколько хэштегов указываются через запятую, без пробела и знака #, например: yandex,share';
