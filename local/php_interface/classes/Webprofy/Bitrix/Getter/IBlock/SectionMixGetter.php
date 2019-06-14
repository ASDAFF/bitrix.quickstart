<?
    namespace Webprofy\Bitrix\Getter\IBlock;

    use Webprofy\Bitrix\Getter\IBlock\SectionGetter;
    use Webprofy\Bitrix\Getter\EntityGetter;

    use Webprofy\Bitrix\IBlock\Section;
    use Webprofy\Bitrix\IBlock\Element;

    class SectionMixGetter extends SectionGetter{
        protected
            $names = array(
                'sm',
                'section-mix',
                'sections-mix',
            ),
            $getListMethod = 'GetMixedList',
            $args = array(
                'sort',
                'filter',
                'count',
                'select',
            );

        function modifyArguments(){
            $f = $this->fields;

            if(!$this->data->get('object')){
                return $this;
            }

            $object = null;
            $type = null;

            switch($f['TYPE']){
                case 'S':
                    $type = 's';
                    $object = new Section($f['ID']);
                    $object->setData(array(
                        'f' => $f,
                        'u' => null
                    ));
                    break;

                case 'E':
                    $type = 'e';
                    $object = new Element($f['ID']);
                    $object->setData(array(
                        'f' => $f,
                        'p' => null
                    ));
                    break;
            }

            if($object){
                $this->arguments
                    ->set($object, 1, 'o')
                    ->set($type, 2, 't');
            }

            return $this;
        }
    }