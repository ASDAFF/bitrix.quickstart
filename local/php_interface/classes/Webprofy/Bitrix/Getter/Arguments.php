<?
    namespace Webprofy\Bitrix\Getter;

    use Webprofy\Bitrix\Getter\DataParser;

    class Arguments{
        protected
            $all = array(),
            $end = false,
            $skip = false;

        function set($value, $index, $name){
            $this->all[$name] = array(
                'value' => $value,
                'index' => $index,
                'name' => $name
            );
            return $this;
        }

        function remove($index){
            foreach($this->all as $i => $v){
                if($v['index'] == $index){
                    unset($this->all[$i]);
                    return;
                }
            }
        }

        function forStep(){
            $result = array($this);
            foreach($this->all as $one){
                $result[$one['index']] = $one['value'];
            }
        return $result;
        }

        function get($i, $j = null){
            $o = @$this->all[$i]['value'];
            if(!$j){
                return $o;
            }
            return @$o[$j];
        }

        function byNames($names){
            $values = array();
            foreach(DataParser::namesToArray($names) as $i => $v){
                if(!is_array($v)){
                    $values[] = $this->get($v);
                    continue;
                }

                foreach($v as $v_){
                    $values[] = $this->get($i, $v_);
                }
            }

            switch(count($values)){
                case 0:
                    return null;

                case 1:
                    return $values[0];

                default:
                    return $values;
            }
        }

        function allFields(){
            $result = array();
            foreach($this->all as $arg){
                $result[$arg['name']] = &$arg['value'];
            }
            return $result;
        }


        function end($end = true){
            $this->end = $end;
            return $this;
        }

        function skip($skip = true){
            $this->skip = $skip;
            return $this;
        }

        function skipping(){
            return $this->skip;
        }

        function ending(){
            return $this->end;
        }

    }