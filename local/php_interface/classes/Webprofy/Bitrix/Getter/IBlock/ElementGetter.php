<?
    namespace Webprofy\Bitrix\Getter\IBlock;

    use Webprofy\Bitrix\Getter\EntityGetter;

    class ElementGetter extends EntityGetter{
        protected
            $names = array(
                'e',
                'element',
                'elements'
            ),
            $class = 'CIBlockElement',
            $objectClass = 'Webprofy\Bitrix\IBlock\Element',
            $args = array(
                'sort',
                'filter',
                'group',
                'nav',
                'select'
            );

        function getArguments(){

        }

        function modifyArguments(){
            if(!$this->data->get('object')){
                return $this;
            }

            $arguments = $this->arguments;
            
            $f = $arguments->get('f');
            $p = $arguments->get('p');

            $class = $this->objectClass;
            $object = new $class($f['ID']);
            $object->setData(array(
                'f' => $f,
                'p' => $p
            ));

            $arguments
                ->set($object, 1, 'o')
                ->remove(2);

            return $this;
        }
    }