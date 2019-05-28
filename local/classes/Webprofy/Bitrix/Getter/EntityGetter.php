<?
    namespace Webprofy\Bitrix\Getter;

    use Webprofy\Bitrix\Getter;
    use Webprofy\Bitrix\Getter\Data;
    use Webprofy\Bitrix\Getter\Arguments;

    abstract class EntityGetter{
        protected
            $names,
            $data,
            $fields,
            $class,
            $args,
            $getter,
            $list,
            $modules = array('iblock'),
            $nextMethod = 'GetNext',
            $nextMethodArguments = array(),
            $getListMethod = 'GetList',
            $objectClass,
            $arguments;

        function getOutputContainer(){
            return null;
        }

        function setGetter(Getter $getter = null){
            $this->getter = $getter;
            return $this;
        }


        function setData(Data $data = null){
            $this->data = $data;
            return $this;
        }

        function getObjectClass($iblock){
            return $this->objectClass;
        }

        function setArguments(Arguments $arguments = null){
            $this->arguments = $arguments;
            return $this;
        }

        function checkData(){
            return in_array(
                $this->data->get('of'),
                $this->names
            );
        }

        function modifyArguments(){
            return $this;
        }

        function setFields($fields){
            $this->fields = $fields;
            return $this;
        }

        function getFields(){
            return $this->fields;
        }

        function getList($reset = false){
            if(!$reset && !empty($this->list)){
                return $this->list;
            }

            foreach($this->modules as $module){
                \CModule::IncludeModule($module);
            }

            if($this->data->get('debug')){
                \WP::log(array(
                    get_class($this),
                    array(
                        $this->class,
                        $this->getListMethod
                    ),
                    $this->data->getListArguments($this->args)
                ), 'all');
            }

            $this->list = call_user_func_array(
                array(
                    $this->class,
                    $this->getListMethod
                ),
                $this->data->getListArguments($this->args)
            );

            return $this->list;
        }

        function updateState(){
            if($this->arguments->ending()){
                $this->arguments->end(false);
                return false;
            }
            $this->fields = call_user_func(
                array(
                    $this->getList(),
                    $this->nextMethod
                ),
                $this->nextMethodArguments
            );
            $this->arguments->set(
                $this->fields,
                1,
                'f'
            );
            return $this->fields ? true : false;
        }

        function makeStep(){
            $data = $this->data;
            $arguments = $this->arguments;
            $result = call_user_func_array(
                $data->getStep(),
                $arguments->forStep()
            );
            $map = $data->getMap();

            switch($map){
                case 'one':
                    $data->setOutput($result);
                    $arguments->end();
                    break;

                case 'map':
                case 'each':
                    if($arguments->skipping()){
                        $arguments->skip(false);
                        return;
                    }
                    switch($map){
                        case 'map':
                            if($result !== null){
                                $data->addOutput($result);
                            }
                            break;
                            
                        case 'each':
                            if($arguments->skipping()){
                                return;
                            }
                            if($result === false){
                                $arguments->end();
                            }
                            break;
                    }
                    break;
            }
        }

        function run($reset = false){
            $list = $this->getList($reset);
            if($oc = $this->getOutputContainer()){
                $this->data->setOutputContainer($oc);
            }
            while($this->updateState()){
                $this
                    ->modifyArguments()
                    ->makeStep();
            }
            if($this->data->getMap() == 'each'){
                return $this->getter;
            }
            return $this->data->getOutput();
        }
    }