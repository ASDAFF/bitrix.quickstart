<?
    namespace Webprofy\Bitrix\Getter\IBlock;

    use Webprofy\Bitrix\Getter\IBlock\ElementGetter;

    class ElementSelectGetter extends ElementGetter{
        protected
            $nextMethod = 'GetNext';

        function checkData(){
            return (
                parent::checkData() &&
                count($this->data->get('select'))
            );
        }

        function modifyArguments(){
            $f = $this->fields;
            $p = array();

            if(isset($f['PROPERTIES'])){
                $p = $f['PROPERTIES'];
            }

            foreach($f as $i => $v){
                foreach(array(
                    array('/^PROPERTY_(.*?)_VALUE$/', '%NAME'),
                    array('/^~PROPERTY_(.*?)_VALUE$/', '~%NAME'),
                ) as $a){
                    list($pattern, $template) = $a;
                    if(preg_match($pattern, $i, $m)){
                        $i_ = $m[1];
                        $p[strtr($template, array(
                            '%NAME' => $i_
                        ))]['VALUE'] = $v;
                        unset($f[$i]);
                    }
                }
            }

            $this
                ->arguments
                    ->set($f, 1, 'f')
                    ->set($p, 2, 'p');

            return parent::modifyArguments();
        }
    }