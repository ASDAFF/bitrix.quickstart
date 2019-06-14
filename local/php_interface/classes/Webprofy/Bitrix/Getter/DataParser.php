<?
    namespace Webprofy\Bitrix\Getter;

    use Webprofy\Bitrix\Attribute\GeneralAttributes;

    class DataParser{
        // static
            // public
                static function namesToArray($names){
                    if(is_array($names)){
                        return $names;
                    }

                    $result = array();
                    foreach(array_map('trim', explode(',', $names)) as $name){
                        @list($ns, $name) = array_map('trim', explode('.', $name));
                        if(!$name){
                            $result[] = $ns;
                            continue;
                        }
                        $result[$ns][] = $name;
                    }
                    return $result;
                }

            // protected
                protected static
                    $shorts = array(
                        'sel, s, where = select',
                        'nav.page, page = nav.iNumPage',
                        'max = nav.nTopCount',
                        'per-page, per, nav.per-page, nav.per = nav.nPageSize',
                        'order = sort'
                    ),
                    $signs = array(
                        '\>\=',
                        '\<\=',
                        '\=',
                        '\>',
                        '\<',
                        '\>\<',
                        '\!'
                    ),
                    $shorts_ = null;


                protected static function getShorts(){
                    if(self::$shorts_ !== null){
                        return self::$shorts_;
                    }

                    $result = array();
                    foreach(self::$shorts as $string){
                        list($froms, $to) = array_map('trim', explode('=', $string));
                        if(strpos($to, '.') > 0){
                            $to = explode('.', $to);
                        }
                        
                        $froms_ = array();
                        foreach(explode(',', $froms) as $from){
                            $from = trim($from);
                            if(strpos($from, '.') > 0){
                                $from = array_map('trim', explode('.', $from));
                            }
                            $froms_[] = $from;
                        }

                        $result[] = array(
                            $froms_,
                            $to
                        );
                    }
                    self::$shorts_ = $result;
                    return $result;
                }


                protected static function replaceShortenIndeces(&$a){
                    foreach(array(
                        'iblock' => 'IBLOCK_ID',
                        'section' => 'SECTION_ID',
                        'id' => 'ID',
                        'nm' => 'NAME',
                        'name' => 'NAME',
                        'act' => 'ACTIVE'
                    ) as $before => $after){
                        if(isset($a[$before])){
                            $a[$after] = $a[$before];
                            unset($a[$before]);
                        }
                    }
                }


                protected static function getRelationStringsToArray($s){
                    $result = array();
                    foreach(explode(';', $s) as $expression){
                        $expression = trim($expression);
                        preg_match(
                            '/^(.*?)('.implode('|', self::$signs).')(.*)$/',
                            $expression,
                            $m
                        );

                        list($noneed, $key, $func, $value) = array_map('trim', $m);

                        if(strpos($value, ',') > 0){
                            $value = array_map('trim', explode(',', $value));
                        }

                        if($func == '='){
                            $func = '';
                        }
                        $result[$func.$key] = $value;
                    }
                    return $result;
                }

        // dynamic
            // public
                function __construct($a){
                    if(!is_array($a)){
                        return;
                    }
                    
                    $this->a = $a;

                    $this
                        ->replaceShort()
                        ->replaceFilter()
                        ->setSelect()
                        ->setSort()
                        ->setCount();
                }

                function get($index){
                    return @$this->a[$index];
                }

                function _log(){
                    \WP::log($this->a, 'pre clear');
                }

            // protected
                protected
                    $a;

                protected function setSelect(){
                    $select = $this->a['select'];

                    if(is_string($select)){
                        $select = self::namesToArray($select);
                    }


                    if(!is_array($select) || empty($select)){
                        $this->a['select'] = null;
                        return $this;
                    }

                    $this->a['select'] = array();

                    foreach($select as $i => $v){
                        if(!is_array($v)){
                            $this->a['select'][] = $v;
                        }

                        foreach($v as $v_){
                            switch($i){
                                case 'p':
                                    $v_ = 'PROPERTY_'.$v_;
                                    break;

                                case 'u':
                                    $v_ = 'UF_'.$v_;
                                    break;
                            }
                            $this->a['select'][] = $v_;
                        }
                    }

                    return $this;
                }

                protected function setSort(){
                    $sort = $this->a['sort'];
                    if(!$sort){
                        $this->a['sort'] = array(
                            'SORT' => 'ASC'
                        );
                        return $this;
                    }

                    if(is_string($sort)){
                        $name = strtoupper($sort);
                        $sort = array(
                            $name => 'ASC'
                        );
                    }

                    $this->a['sort'] = $sort;

                    return $this;
                }

                protected function setCount(){
                    $count = $this->a['count'];
                    if($count['active']){
                        $this->a['count'] = CNT_ACTIVE;
                    }
                    return $this;
                }

                protected function replaceShort(){
                    $d = &$this->a;

                    foreach(self::getShorts() as $a){
                        list($froms, $to) = $a;
                        if(is_string($froms)){
                            $froms = array(array($froms));
                        }
                        if(is_string($to)){
                            $to = array($to);
                        }
                        foreach($froms as $from){
                            if(is_string($from)){
                                $from = array($from);
                            }
                            $b = $d;
                            $set = true;
                            foreach($from as $i){
                                if(!isset($b[$i])){
                                    $set = false;
                                    break;
                                }
                                $b = $b[$i];
                            }

                            if(!$set){
                                continue;
                            }

                            $c = &$d;
                            foreach($to as $i){
                                $c = &$c[$i];
                            }
                            $c = $b;
                        }
                    }

                    return $this;
                }


                protected function replaceFilter(){
                    $d = &$this->a;

                    if(!isset($d['filter'])){
                        $d['filter'] = array();
                    }

                    if(
                        $d['filter'] instanceof GeneralAttributes
                    ){
                        $d['filter'] = $d['filter']->getSelectFields();
                    }

                    if(isset($d['f'])){
                        if(is_string($d['f'])){
                            $d['f'] = self::getRelationStringsToArray($d['f']);
                        }
                        $d['filter'] = array_merge($d['filter'], $d['f']);
                    }

                    if(isset($d['p'])){
                        if(is_string($d['p'])){
                            $d['p'] = self::getRelationStringsToArray($d['p']);
                        }
                        foreach($d['p'] as $i => $v){

                            preg_match(
                                '/^\s*('.implode('|', self::$signs).'|)(.*)$/',
                                $i,
                                $m
                            );
                            list($all, $func, $code) = $m;
                            $d['filter'][$func.'PROPERTY_'.$code] = $v;
                        }
                    }

                    if(empty($d['filter']) && !$d['nac']['nTopCount']){
                        $d['nav']['nTopCount'] = 10;
                    }

                    if(is_string($d['filter'])){
                        $d['filter'] = self::getRelationStringsToArray($d['filter']);
                    }

                    self::replaceShortenIndeces($d['filter']);

                    return $this;
                }
    }