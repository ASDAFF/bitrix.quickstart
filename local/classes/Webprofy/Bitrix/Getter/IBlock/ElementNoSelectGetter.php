<?
    namespace Webprofy\Bitrix\Getter\IBlock;

    use Webprofy\Bitrix\Getter\IBlock\ElementGetter;

    class ElementNoSelectGetter extends ElementGetter{
        protected
            $nextMethod = 'GetNextElement';

        function checkData(){
            return (
                parent::checkData() &&
                !count($this->data->get('select'))
            );
        }

        function modifyArguments(){
            $this
                ->arguments
                    ->set($this->fields->GetFields(), 1, 'f')
                    ->set($this->fields->GetProperties(), 2, 'p');

            return parent::modifyArguments();
        }
    }