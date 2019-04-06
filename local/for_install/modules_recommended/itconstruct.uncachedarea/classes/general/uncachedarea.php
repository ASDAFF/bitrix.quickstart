<?php
/**
 * <h1>Некешируемые области.</h1>
 *
 * Модуль позволяет добавить в любое место сайта (в т.ч. кешируемый компонент) некешируемую область.
 * Данные можно выводить как с помощью назначения контента области, так и с помощью функций обратного вызова, передавая для каждой области свои параметры.
 */

namespace itc;

IncludeModuleLangFile(__FILE__);

/**
 * API
 */

class CUncachedArea
{
    /**
     * Назначенный через itc\CUncachedArea::setContent() контент для размеченных областей
     *
     * @var array
     */
    static protected $_contents = array();

    /**
     * Зарегистрированные обработчики
     *
     * @var array
     */
    static protected $_callbacks = array();

    /**
     * Ключ области для захватываемого контента
     *
     * @var string
     */
    static protected $_captureKey;

    /**
     * Индекс сортировки захватываемого контента для области.
     *
     * @var int
     */
    static protected $_captureSort;

    /**
     * Экранирование служебных символов в ключе (коде) области
     * 
     * @param string $key
     */
    static protected function _escapeKey($key)
    {
        $key = str_replace(':', '\\:', $key);
        return $key;
    }

    /**
     * Помечает область для вставки некешируемых данных
     *
     * @param string $key Ключ (код) для области.
     * @param int|string|array|null $data Данные. Опционально.
     * @return bool
     */
    static public function show($key, $data = null)
    {
        if (empty($key)) {
            return false;
        }

        $key = self::_escapeKey($key);
        print '<!-- uncachedArea:' . $key . ($data !== null ? ':' . serialize($data) : '') . ' -->';

        return true;
    }

    /**
     * Назначает контент для определённого ключа.
     * 
     * @param string $key Ключ (код) области
     * @param string $content
     * @param string $sort Индекс сортировки.
     */
    static public function setContent($key, $content, $sort = 100)
    {
        self::$_contents[$key][] = array(
            'sort' => $sort,
            'content' => $content
        );
    }

    /**
     * Старт захвата (capturing) контента. Вся выводимая информация будет удалена после окончания захвата.
     * Если ключ не передан, endCapture() вернёт контент. Если передан - назначит его для $key.
     *
     * @param string $key
     * @param int $sort
     */
    static public function startCapture($key = null, $sort = 100)
    {
        ob_start();
        self::$_captureKey = $key;
        self::$_captureSort = $sort;
    }

    /**
     * Завершение захвата контента.
     *
     * @return null|string Если в startCapture() не передавался ключ, функция вернёт контент.
     */
    static public function endCapture()
    {
        $content = ob_get_clean();

        if (self::$_captureKey != null) {
            self::setContent(self::$_captureKey, $content, self::$_captureSort);
        } else {
            return $content;
        }
    }

    /**
     * Регистрация функции обратного вызова для генерации некешируемого контента
     *
     * @param int $key
     * @param Closure|string $callback
     * @param string $sort Индекс сортировки.
     * @return int Индекс колбэка (для удаления).
     */
    static public function registerCallback($key, $callback, $sort = 100) {
        self::$_callbacks[$key][] = array(
            'sort' => $sort,
            'callback' => $callback,
            'index' => (int) count(self::$_callbacks[$key]),
        );

        return count(self::$_callbacks[$key]) - 1;
    }

    /**
     * Удаление функции обратного вызова.
     *
     * @param int $key
     * @param int index Индекс колбэка.
     * @return bool
     */
    static public function unregisterCallback($key, $index) {
        if (!is_array(self::$_callbacks[$key]) || !isset(self::$_callbacks[$key][$index])) {
            return false;
        }

        unset(self::$_callbacks[$key][$index]);
        return true;
    }

    /**
     * Сортировка по полю sort.
     *
     * @param array $a
     * @param array $b
     */
    static protected function _cmpSort($a, $b)
    {
        if ($a['sort'] == $b['sort']) {
            if (isset($a['index'])) {
                if ($a['index'] == $b['index']) {
                    return 0;
                }

                return $a['index'] < $b['index'] ? -1 : 1;
            }
        }

        return $a['sort'] < $b['sort'] ? -1 : 1;
    }

    /**
     * Возвращает второстепенный ключ по данным некешируемой области. Используется в пользовательских колбэках.
     *
     * @param string $data Данные.
     * @return string|array Если данные пусты, возвращается ключ в виде пустой строки.
     */
    static public function getSubkey($data)
    {
        return empty($data) ? '' : serialize($data);
    }

    /**
     * Десереализация данных.
     *
     * @param string $data
     */
    static protected function _parseAreaData(&$data)
    {
        if ($data == '') {
            return null;
        }

        $data = unserialize($data);
    }

    /**
     * Вставка контента в размеченные области. Обработчик для OnEndBufferContent.
     *
     * @param string $pageContent
     */
    static public function processAreas(&$pageContent)
    {
        if (self::_allowAddToDebugPanel()) {
            $debugProcess = new \CDebugInfo();
            $debugProcess->Start();
        }

        $replaces = array();
        $callbackReplaces = array();

        // Выполнение обработчиков и получение их контента.
        foreach (self::$_callbacks as $key => $callbacks) {
            // Сортируем колбэки по "sort"
            uasort($callbacks, 'itc\CUncachedArea::_cmpSort');
            $key = self::_escapeKey($key);

            // Находим размеченные области и извлекаем данные. Один ключ может иметь несколько вхождений (областей)
            preg_match_all('#<!-- uncachedArea:' . preg_quote($key) . '(?::(.*))? -->#siU' . BX_UTF_PCRE_MODIFIER, $pageContent, $matches);

            if (count($matches[1]) > 0) {
                $areasData = $matches[1];
                array_walk($areasData, 'itc\CUncachedArea::_parseAreaData');

                foreach ($callbacks as $callbackIndex => $callbackItem) {
                    // Колбэк должен вернуть массив. Ключ массива - результат itc\CUncachedArea::getSubkey
                    // (ключ представляет собой сериализацию, но необходимо исопльзовать апи!)
                    if (self::_allowAddToDebugPanel()) {
                        $debug = new \CDebugInfo();
                        $debug->Start();
                    }

                    $results = call_user_func_array($callbackItem['callback'], array($key, $areasData));

                    if (self::_allowAddToDebugPanel()) {
                        $handlerName = '';

                        if (class_exists('Closure') && $callbackItem['callback'] instanceof \Closure) {
                            $handlerName = '(anonymous)';
                        } else {
                            if (is_array($callbackItem['callback'])) {
                                $handlerName = $callbackItem['callback'][0] . '::' . $callbackItem['callback'][1] . '()';
                            } else {
                                $handlerName = $callbackItem['callback'] . '()';
                            }
                        }

                        $debug->Stop('Обработчик ' . $handlerName . ' ключа ' . $key, '(unknown)', 'N');
                    }

                    if (is_array($results)) {
                        $callbackReplaces[$key][] = array(
                            'sort' => $callbackItem['sort'],
                            'data' => $results,
                        );
                    } else {
                        trigger_error('Result of callback must by array', E_USER_WARNING);
                    }
                }
            }
        }

        $replaces = array();

        // Замена размеченных областей контентом
        foreach ($callbackReplaces as $key => $callbacksData) {
            $simpleContents = self::$_contents[$key];

            if (!is_array($simpleContents)) {
                $simpleContents = array();
            }

            uasort($simpleContents, 'itc\CUncachedArea::_cmpSort');

            foreach ($callbacksData as $callbackDataIndex => $callbackData) {
                $isLast = count($callbacksData) - 1 == $callbackDataIndex;
                $sortCallback = $callbackData['sort'];
                $simpleContentInserted = false;

                $simpleContentBefore = '';
                $simpleContentAfter = '';

                // Поиск простого контента, который надо вставить до колбэчного (в соответствии с индексом сортировки)
                foreach ($simpleContents as $i => $item) {
                    if ($item['sort'] <= $sortCallback) {
                        $simpleContentBefore .= $item['content'];
                        unset($simpleContents[$i]);
                    } else {
                        break;
                    }
                }

                // Если колбэк для текущего ключа последний, добавляем простой контент в конец (по индексу сортировки)
                if ($isLast) {
                    foreach ($simpleContents as $i => $item) {
                        if ($item['sort'] > $sortCallback) {
                            $simpleContentAfter .= $item['content'];
                            unset($simpleContents[$i]);
                        }
                    }
                }

                foreach ($callbackData['data'] as $subkey => $content) {
                    $replaceKey = '<!-- uncachedArea:' . self::_escapeKey($key) . ($subkey == '' ? '' : ':' . $subkey) . ' -->';
                    $replaces[$replaceKey] .= $simpleContentBefore . $content . $simpleContentAfter;
                }

                foreach ($simpleContents as $i => $item) {
                    if ($item['sort'] <= $sortCallback) {
                        $simpleContent .= $item['content'];
                        unset($simpleContents[$i]);
                    } else {
                        break;
                    }
                }
            }
        }

        if (self::_allowHighlightUncachedArea()) {
            foreach ($replaces as $replaceKey => &$value) {
                $value = self::_wrapContentToAttribute($replaceKey, $value);
            }
        }

        $pageContent = str_replace(array_keys($replaces), array_values($replaces), $pageContent);

        // Подстановка оставшегося простого контента
        foreach (self::$_contents as $key => $data) {
            usort($data, 'itc\CUncachedArea::_cmpSort');
            $str = '';

            for ($i = 0; $i < count($data); $i++) {
                $str .= $data[$i]['content'];
            }

            $replaceKey = '<!-- uncachedArea:' . self::_escapeKey($key) . ' -->';

            if (self::_allowHighlightUncachedArea()) {
                $str = self::_wrapContentToAttribute($replaceKey, $str);
            }

            $pageContent = str_replace($replaceKey, $str, $pageContent);
        }

        if (self::_allowAddToDebugPanel()) {
            $debugProcess->Stop('Обработка некешируемых областей', __FILE__, 'N');
        }

        return true;
    }

    /**
     * Проверка прав для обозначения областей.
     *
     * @return bool
     */
    static protected function _allowHighlightUncachedArea()
    {
        return ($_SESSION['SHOW_UNCACHED_AREAS'] === 'Y' && ($GLOBALS['USER']->IsAdmin() || !empty($_REQUEST['show_uncached_areas_' . $GLOBALS['LICENSE_KEY']])));
    }

    /**
     * Проверка прав на добавление информации в панель дебага.
     *
     * @return bool
     */
    static protected function _allowAddToDebugPanel()
    {
        return ($_SESSION["SESS_SHOW_INCLUDE_TIME_EXEC"] == "Y" && ($GLOBALS['USER']->CanDoOperation('edit_php') || $_SESSION["SHOW_SQL_STAT"] == "Y"));
    }

    /**
     * Оборачивает контент в блок div, содержащий служебные данные.
     *
     * @param string $key
     * @param string $value
     * @return string
     */
    static protected function _wrapContentToAttribute($key, $value)
    {
        preg_match('#^.*?:(.*?)(?<!\\\\)(?::(.*))? --#si', $key, $matches);
        $value = '<div class="itc-uncached-area-container" title="key = ' . htmlspecialchars($matches[1]) . (!empty($matches[2]) ? '&#xA;params = ' . htmlspecialchars($matches[2]) : '') . '">' . $value . '</div>';
        return $value;
    }
    
    /**
     * Обработчик для OnBeforeProlog.
     * Добавляет кнопку в верхнюю панель.
     * Добавляет стиль, если включена подсветка областей.
     */
    static public function onBeforeProlog()
    {
        global $APPLICATION, $USER;

        if ($_GET['show_uncached_areas'] == 'Y') {
            $_SESSION['SHOW_UNCACHED_AREAS'] = 'Y';
        } elseif($_GET['show_uncached_areas'] == 'N') {
            $_SESSION['SHOW_UNCACHED_AREAS'] = 'N';
        }

        if ($_SESSION['SHOW_UNCACHED_AREAS'] == 'Y') {
            $APPLICATION->SetAdditionalCSS('/bitrix/js/itconstruct.uncachedarea/style.css');
        }

        // Add button to top panel
        if (defined('ITCONSTRUCT_UNCACHED_AREA_SHOW_PANEL_BUTTON') && ITCONSTRUCT_UNCACHED_AREA_SHOW_PANEL_BUTTON === true && $USER->CanDoOperation('edit_php')) {
            $APPLICATION->AddPanelButton(array(
                "SRC" => "/bitrix/themes/.default/public/panel_new/menus/page_cache.gif", // картинка на кнопке
                "ALT"       => "",
                "TEXT" => GetMessage('ITCONSTRUCT_UNCACHED_AREA_PANEL_BUTTON_TEXT'),
                "MAIN_SORT" => 500,
                "SORT" => 100,
                "MENU" => array(
                    array(
                        "TEXT" => GetMessage('ITCONSTRUCT_UNCACHED_AREA_SHOW_AREAS_TEXT'),
                        "TITLE" => GetMessage('ITCONSTRUCT_UNCACHED_AREA_SHOW_AREAS_TITLE'),
                        "ICON" => $_SESSION['SHOW_UNCACHED_AREAS'] == 'Y' ? 'checked' : '',
                        "ACTION" => "jsUtils.Redirect([], '" . \CUtil::JSEscape($APPLICATION->GetCurPageParam('show_uncached_areas=' . ($_SESSION['SHOW_UNCACHED_AREAS'] == 'Y' ? 'N' : 'Y'), array("show_uncached_areas")))."')",
                    ),
                ),
                "HINT" => array(
                    "TITLE" => GetMessage('ITCONSTRUCT_UNCACHED_AREA_PANEL_BUTTON_HINT_TITLE'),
                    "TEXT" => GetMessage('ITCONSTRUCT_UNCACHED_AREA_PANEL_BUTTON_HINT_TEXT'),
                )
            ));
        }
    }
}

/**
 * Alias
 */
class UncachedArea extends CUncachedArea
{
}

?>
