<?
    namespace Webprofy\Bitrix;

    use Webprofy\Bitrix\Getter\Data;
    use Webprofy\Bitrix\Getter\DataParser;
    use Webprofy\Bitrix\Getter\Arguments;

    use Webprofy\Bitrix\Getter\IBlock\ElementNoSelectGetter;
    use Webprofy\Bitrix\Getter\IBlock\ElementSelectGetter;
    use Webprofy\Bitrix\Getter\IBlock\PropertyGetter;
    use Webprofy\Bitrix\Getter\IBlock\SectionGetter;
    use Webprofy\Bitrix\Getter\IBlock\SectionTreeGetter;
    use Webprofy\Bitrix\Getter\IBlock\SectionMixGetter;
    use Webprofy\Bitrix\Getter\IBlock\IBlockGetter;
    use Webprofy\Bitrix\Getter\IBlock\IBlockTypeGetter;
    use Webprofy\Bitrix\Getter\IBlock\ElementSectionsGetter;

    class Getter{
    	// static
	        static function get($a){
	        	$dp = new DataParser($a);
	        	$data = new Data($dp);
	            return new Getter($data);
	        }

	        static function go($a){
	        	$g = self::get($a);
	        	return $g->run();
	        }

	    // dynamic
	        // protected
		        protected
		            $eg, // EntityGetter
		            $data;

		        protected function getList(){
		            return $this->getEntityGetter()->getList();
		        }

	        // public

		        function __construct(Data $data = null){
		            $this->data = $data;
		        }

		        function getGetters(){
		        	return array(
		                new ElementSelectGetter(),
		                new ElementNoSelectGetter(),
		                new PropertyGetter(),
		                new SectionGetter(),
		                new SectionTreeGetter(),
		                new SectionMixGetter(),
		                new IBlockGetter(),
		                new IBlockTypeGetter(),
		                new ElementSectionsGetter(),
		            );
		        }

		        function getPagesCount(){
		            return $this->getList()->NavPageCount;
		        }

		        function getPageNumber(){
		            return $this->getList()->NavPageNomer;
		        }

		        function getItemsCount(){
		            return $this->getList()->SelectedRowsCount();
		        }
		        
		        function getEntityGetter(){
		            if(!empty($this->eg)){
		                return $this->eg;
		            }

		            foreach($this->getGetters() as $eg){
		                if($eg
		                	->setData($this->data)
		                	->checkData()
		                ){
		                    $this->eg = $eg;
		                    return $eg;
		                }
		            }
		            return null;
		        }

		        function run($reset = false){
		        	$eg = $this->getEntityGetter();
		        	if(!$eg){
		        		throw new \Exception('Bit of "'.$this->data->get('of').'" not found.');
		        	}
		            return $eg
		        		->setArguments(new Arguments())
		        		->setGetter($this)
		        		->run($reset);
		        }

		        function one($callback){
		        	$this->data->setStep($callback, 'one');
		        	return $this->run(true);
		        }

		        function each($callback){
		        	$this->data->setStep($callback, 'each');
		        	return $this->run(true);
		        }

		        function map($callback){
		        	$this->data->setStep($callback, 'map');
		        	return $this->run(true);
		        }
    }