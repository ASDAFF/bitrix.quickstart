<?
	namespace Webprofy\Console;

	use Symfony\Component\Console\Application;
	use Symfony\Component\Console\Question\Question;

	class ConsoleGeneral{

		function ask($input, $output, $helper, $definition, $options){
			$values = array();

	    	foreach($options as $option){
	    		$optionName = $option->getName();
	    		$value = $input->getOption($optionName);
	    		while(empty($value)){
		    		$option = $definition->getOption($optionName);
			    	$value = $helper->ask($input, $output, new Question(
			    		$option->getDescription().': ',
			    		$option->getDefault()
			    	));
			    	if($option->isValueOptional()){
			    		break;
			    	}
	    		}
	    		$values[$optionName] = $value;
	    	}

	    	return $values;
		}

		function copyFiles($from, $to, $replaces, $output){
	    	if(!is_dir($to)){
				$output->writeln('d: '.$to);
	    		mkdir($to, 0777, true);
	    	}

	    	if(!is_dir($from)){
				$output->writeln('<error>Folder not found: '.$from.'</error>');
	    		return false;
	    	}

	    	foreach(scandir($from) as $fileName){
	    		if(in_array($fileName, array(
	    			'.',
	    			'..'
	    		))){
	    			continue;
	    		}

	    		$filePath = $from.$fileName;
	    		$toReplaced = $to.strtr($fileName, $replaces);

	    		if(is_dir($filePath)){
					if(!$this->copyFiles(
						$from.$fileName.'/',
						$toReplaced.'/',
						$replaces,
						$output
					)){
						return false;
					}
					continue;
	    		}

				$output->writeln('f: '.$toReplaced);
	    		file_put_contents(
	    			$toReplaced,
	    			strtr(
	    				file_get_contents($filePath),
	    				$replaces
	    			)
	    		);
	    	}

	    	return true;
	    }
	}

?>