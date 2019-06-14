<?php
	
	namespace Webprofy\General;

	class Container implements \Iterator{
		protected
			$all;

		function __construct(){
			$this->all = array();
		}

		function add($one){
			$this->all[] = $one;
			return $this;
		}

		function eachRemove($callback){
			foreach($this->all as $i => $one){
				if($callback($one, $i)){
					unset($this->all[$i]);
					$i--;
				}
			}
		}

		function count(){
			return count($this->all);
		}

		function map($callback){
			return array_map($callback, $this->all);
		}

		function each($callback){
			foreach($this->all as $one){
				if($callback($one) === false){
					break;
				}
			}
			return $this;
		}

		function first(){
			return $this->all[0];
		}

		function extend(Container $elements){
			$me = $this;
			$elements->each(function($one) use ($me){
				$me->add($one);
			});
			return $this;
		}

		function all(){
			return $this->all;
		}

		function byIndex($i){
			return $this->all[$i];
		}

		function removeSame($getter){
			$values = array();
			$this->eachRemove(function($one) use ($getter, &$values){
				$value = $one->{$getter}();
				if(in_array($value, $values)){
					return true;
				}
				$values[] = $value;
			});
			return $this;
		}

		function filter($getter, $value, $first = false){
			$result = new self();
			$this->each(function($one) use ($getter, $value, $result, $first){
				if($one->{$getter}() == $value){
					$result->add($one);
					if($first){
						return false;
					}
				}
			});
			if($first){
				return $result->first();
			}
			return $result;
		}

		function current(){
			return current($this->all);
		}
	    function rewind(){
	    	reset($this->all);
	    }

	    function key(){
	    	return key($this->all);
	    }
	    function next(){
	    	return next($this->all);
	    }
	    function valid(){
	   		return !is_null(key($this->all));;
	    }

	}