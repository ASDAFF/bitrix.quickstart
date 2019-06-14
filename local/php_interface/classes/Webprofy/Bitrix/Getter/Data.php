<?
    namespace Webprofy\Bitrix\Getter;

    use Webprofy\Bitrix\Getter\DataParser;

    class Data{
        protected
            $parser,
            $map,
            $outputContainer,
            $step;

        function __construct(DataParser $parser = null){
            $this->parser = $parser;
        }

        function _log(){
            $this->parser->_log();
        }

        function getStep(){
            if(!empty($this->step)){
                return $this->step;
            }

            foreach(array(
                'map',
                'one',
                'each',
            ) as $i){
                $step = $this->get($i);
                if(is_callable($step) || is_string($step)){
                    $this->setStep($step, $i);
                    return $this->step;
                }
            }

            $this->setStep(function($event){
                return $event->allFields();
            }, 'map');
        }

        function setStep($step, $map = 'one'){
            if(is_callable($step)){
                $this->step = $step;
                $this->map = $map;
                return $this;
            }

            if(!is_string($step)){
                $this->step = null;
                return $this;
            }

            $this->map = $map;
            $this->step = function($event, $f, $p) use ($step){
                return $event->byNames($step);
            };
        }

        function getMap(){
            return $this->map;
        }

        function setArgumentType($argumentType){
            $this->argumentType = $argumentType;
            return $this;
        }

        function get($index){
            return $this->parser->get($index);
        }

        function checkArgumentType($argumentType){
            return $this->argumentType == $argumentType;
        }

        function hasSelect(){
            return !empty($this->select);
        }

        function getListArguments($args){
            $result = array();
            foreach($args as $arg){
                $result[] = $this->parser->get($arg);
            }
            return $result;
        }

        protected $output = null;

        function setOutputContainer($outputContainer){
            $this->outputContainer = $outputContainer;
        }

        function setOutput($output){
            $this->output = $output;
            return $this;
        }

        function addOutput($output){
            if(empty($this->outputContainer) || !$this->get('object')){
                $this->output[] = $output;
            }
            else{
                $this->outputContainer->add($output);
            }
            return $this;
        }

        function getOutput(){
            if(empty($this->outputContainer) || !$this->get('object')){
                return $this->output;
            }
            else{
                return $this->outputContainer;
            }
        }
    }