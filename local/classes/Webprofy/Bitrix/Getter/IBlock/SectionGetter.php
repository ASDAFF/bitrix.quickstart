<?
    namespace Webprofy\Bitrix\Getter\IBlock;

    use Webprofy\Bitrix\Getter\EntityGetter;

    class SectionGetter extends EntityGetter{
        protected
            $names = array(
                's',
                'section',
                'sections'
            ),
            $class = 'CIBlockSection',
            $args = array(
                'sort',
                'filter',
                'count',
                'select',
                'nav'
            ),
            $objectClass = 'Webprofy\Bitrix\IBlock\Section';

        function modifyArguments(){
            $arguments = $this->arguments;

            $f = $arguments->get('f');
            $u = array();
            foreach($f as $i => $v){
                if(preg_match('/^(~?)UF_/', $i, $m)){
                    $j = $m[1].substr($i, 3);
                    $u[$j] = $v;
                    unset($f[$i]);
                }
            }

            if(!$this->data->get('object')){
                return $this;
            }

            $class = $this->objectClass;
            $object = new $class($f['ID']);
            $object->setData(array(
                'f' => $f,
                'u' => $u
            ));

            $arguments
                ->set($object, 1, 'o')
                ->remove(2);

            return $this;
        }
    }