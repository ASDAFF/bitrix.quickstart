<?
    namespace Webprofy\Bitrix\Getter\IBlock;

    use Webprofy\Bitrix\Getter\EntityGetter;
    use Webprofy\Bitrix\Attribute\PropertyAttribute;
    use Webprofy\Bitrix\Attribute\PropertyAttributes;

    class PropertyGetter extends EntityGetter{
        protected
            $names = array(
                'p',
                'property',
                'properties'
            ),
            $class = 'CIBlockProperty',
            $args = array(
                'sort',
                'filter'
            );

        function getOutputContainer(){
            return new PropertyAttributes();
        }

        function modifyArguments(){
            if(!$this->data->get('object')){
                return $this;
            }

            $arguments = $this->arguments;
            
            $f = $arguments->get('f');

            $object = new PropertyAttribute($f['ID']);
            $object->setData($f);

            $arguments
                ->set($object, 1, 'o')
                ->remove(2);

            return $this;
        }
    }