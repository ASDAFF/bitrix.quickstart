<?
    namespace Webprofy\Bitrix;

    use Webprofy\Bitrix\DataHolder;
    use Webprofy\Bitrix\IBlock\IBlock;
    use \CModule;

    class IBlockEntity extends DataHolder{
        protected
            $iblock;

        function __construct($id, IBlock $iblock = null){
            CModule::IncludeModule('iblock');
            parent::__construct($id);
            $this->iblock = $iblock;
        }

        function setIBlock(IBlock $iblock = null){
            $this->iblock = $iblock;
            return $this;
        }
        function getIBlock(){
            return $this->iblock;
        }
    }