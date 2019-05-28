<?
    namespace Webprofy\Bitrix\Getter\IBlock;

    use Webprofy\Bitrix\Getter\EntityGetter;
    use Webprofy\Bitrix\IBlock\IBlock;

    class IBlockGetter extends EntityGetter{
        protected
            $names = array(
                'ib',
                'iblock',
                'iblocks'
            ),
            $class = 'CIBlock',
            $args = array(
                'sort',
                'filter',
                'group',
                'nac',
                'select'
            );

        function modifyArguments(){
            $arguments = $this->arguments;

            $f = $arguments->get('f');

            if(!$this->data->get('object')){
                return $this;
            }

            $object = new IBlock($f['ID']);
            $object->setData($f);

            $arguments
                ->set($object, 1, 'o')
                ->remove(2);

            return $this;
        }
    }