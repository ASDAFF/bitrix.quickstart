<?
// Подключаем языковые константы
IncludeModuleLangFile( __FILE__ );

/**
 * Добавление счетчиков в шаблоны Битрикс
 */
class Nimax_Stat_Option
{
    public $template_id = null; // ИД шаблона
    private $tpl_path = null; // Путь до выбранного шаблона
    // Контент шаблона
    private $tpl_content = array(
        'header' => null,
        'footer' => null
    );
    // Массив кодов
    public $code_array = array(
        'GA' => array(
            'place' => 'header',
            'cur_code' => '',
            'group' => 'counter'
        ),
        'YA' => array(
            'place' => 'footer',
            'cur_code' => '',
            'group' => 'counter'
        ),
        'LI' => array(
            'place' => 'footer',
            'cur_code' => '',
            'group' => 'counter'
        ),
        'GW' => array(
            'place' => 'header',
            'cur_code' => '',
            'group' => 'meta'
        ),
        'YW' => array(
            'place' => 'header',
            'cur_code' => '',
            'group' => 'meta'
        ),
    );

    /**
     * Проверка файлов шаблона на совместимость
     * @param $file_name
     * @throws Exception
     */
    private function checkFile($file_name)
    {
        $file_path = $this->tpl_path.'/'.$file_name.'.php';

        if(!file_exists($file_path))
            throw new Exception(GetMessage('FILE_NOT_FOUND', array('#FILENAME#' => $file_name, '#TEMPLATE_ID#' => $this->template_id)));

        if(!is_readable($file_path) || !is_writable($file_path))
            throw new Exception(GetMessage('FILE_NOT_WR', array('#FILENAME#' => $file_name, '#TEMPLATE_ID#' => $this->template_id)));

        $content = file_get_contents($file_path);
        $tag = ($file_name == 'header' ? 'head' : 'body');
        if(!(bool)preg_match("/(<\/{$tag}>)/is",$content))
            throw new Exception(GetMessage('FILE_NOT_TAG', array('#TAG#' => $tag, '#FILENAME#' => $file_name, '#TEMPLATE_ID#' => $this->template_id)));

        $this->tpl_content[$file_name] = $content;
    }

    /**
     * Инициализация шаблона
     * @param $tpl_id
     * @throws Exception
     */
    public function templateInit($tpl_id)
    {
        if(empty($tpl_id))
            throw new Exception(GetMessage('TPL_NOT_SELECT'));

        $tpl_path = $_SERVER["DOCUMENT_ROOT"]."/bitrix/templates/".$tpl_id;
        if(!is_dir($tpl_path))
            throw new Exception(GetMessage('TPL_NOT_FOUND', array('#TPL_PATH#' => $tpl_path)));

        $this->template_id = $tpl_id;
        $this->tpl_path = $tpl_path;

        $this->checkFile('header');
        $this->checkFile('footer');
    }

    /**
     * Список доступных шаблонов сайта
     * @return array
     */
    public static function getTemplateList()
    {
        $array = array();
        $res = CSiteTemplate::GetList();
        while($arTpl = $res->GetNext())
            $array[$arTpl['ID']] = $arTpl['NAME'];

        return $array;
    }

    /**
     * Получение текущего код счетчиков
     * @param $code_id
     * @return string
     */
    public function getCurCode($code_id)
    {
        $code = '';
        if(isset($this->code_array[$code_id]) && !is_null($this->tpl_content[$this->code_array[$code_id]['place']]))
        {
            $code_array = $this->code_array[$code_id];
            if(empty($code_array['cur_code']))
            {
                preg_match("/<!--{$code_id}_start-->(.*)<!--{$code_id}_end-->/is",$this->tpl_content[$code_array['place']],$matchCode);
                if(!empty($matchCode[1])) $code_array['cur_code'] = $matchCode[1];
            }
            $code = $code_array['cur_code'];
        }
        return $code;
    }

    /**
     * Генерация хеша кода счетчика
     * @param $code
     * @return string
     */
    private function getCodeHash($code)
    {
        return md5(preg_replace("/[\t\r\n\s]+/iUs",'',$code));
    }

    /**
     * Сохраняем ИД шаблона в массиве всех использованных шалонов
     * @param $tpl_id
     */
    private function saveTemplate($tpl_id)
    {
        $tpls = unserialize(COption::GetOptionString('nimax_stat', 'templates'));
        if(!$tpls) $tpls = array();
        if(!in_array($tpl_id,$tpls))
        {
            $tpls[] = $tpl_id;
            COption::SetOptionString('nimax_stat', 'templates', serialize($tpls));
        }
    }

    /**
     * Сохранение кодов счетчиков
     * @param $data
     * @throws Exception
     */
    public function saveOption($data)
    {
        if(!isset($data['Update']) && !check_bitrix_sessid())
            throw new Exception(GetMessage('DATA_NOT_SAVE'));

        // Сохраняем шаблон в используемых
        $this->saveTemplate($this->template_id);

        foreach($this->code_array as $codeId => $codeVal)
        {
            if(!isset($data[$codeId][$this->template_id])) continue;
            $cur_code = trim($data[$codeId][$this->template_id]);

            // Удаляем старый код
            $content = preg_replace("/(<!--{$codeId}_start-->.*<!--{$codeId}_end-->?\r\n)/iUs",'',$this->tpl_content[$codeVal['place']]);

            if(!empty($cur_code))
            {
                // Добавление кода
                $cur_code_new = "<!--{$codeId}_start-->\r\n{$cur_code}\r\n<!--{$codeId}_end-->\r\n";
                $tag = ($codeVal['place'] == 'header' ? 'head' : 'body');

                if(!$content = preg_replace("/(<\/{$tag}>)/is",$cur_code_new.'</'.$tag.'>',$content))
                    throw new Exception(GetMessage('CODE_NOT_SAVE', array('#CODE_ID#' => $codeId)));
            }

            if(!file_put_contents($this->tpl_path.'/'.$codeVal['place'].'.php',$content))
                throw new Exception(GetMessage('CODE_NOT_SAVE', array('#CODE_ID#' => $codeId)));

            $this->code_array[$codeId]['cur_code'] = $cur_code;
            $this->tpl_content[$codeVal['place']] = $content;
            COption::SetOptionString('nimax_stat', $codeId.'_'.$this->template_id.'_hash', $this->getCodeHash($cur_code));
        }
    }

    /**
     * Удаление кодов счетчиков
     */
    public function deleteOption()
    {
        // Получаем все шаблоны
        $tpls = unserialize(COption::GetOptionString('nimax_stat', 'templates'));
        if($tpls)
        {
            foreach($tpls as $tpl_id)
            {
                // Инициализируем шаблон
                $this->templateInit($tpl_id);
                foreach($this->code_array as $codeId => $codeVal)
                {
                    // Получаем код из шаблона
                    $cur_code = $this->getCurCode($codeId);
                    $hash_name = $codeId.'_'.$tpl_id.'_hash';
                    if(!empty($cur_code) && $this->getCodeHash($cur_code) == COption::GetOptionString('nimax_stat', $hash_name))
                    {
                        // Удаляем код из шаблонов
                        $this->tpl_content[$codeVal['place']] = preg_replace("/(<!--{$codeId}_start-->.*<!--{$codeId}_end-->?\r\n)/iUs",'',$this->tpl_content[$codeVal['place']]);
                    }
                    COption::RemoveOption('nimax_stat', $hash_name);
                }
                @file_put_contents($this->tpl_path.'/header.php',$this->tpl_content['header']);
                @file_put_contents($this->tpl_path.'/footer.php',$this->tpl_content['footer']);
            }
        }
        COption::RemoveOption('nimax_stat', 'templates');
    }
}