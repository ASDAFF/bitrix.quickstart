<?

	namespace Webprofy\Console\Command;

	use Symfony\Component\Console\Command\Command;
	use Symfony\Component\Console\Input\InputArgument;
	use Symfony\Component\Console\Input\InputInterface;
	use Symfony\Component\Console\Input\InputOption;
	use Symfony\Component\Console\Output\OutputInterface;
	use Symfony\Component\Console\Question\Question;


	class CreateModuleCommand extends Command{
		protected $options = null;
	    protected function configure(){
	    	$this->options = array(
	            new InputOption(
	            	'namespace',
	            	'',
	            	InputOption::VALUE_OPTIONAL,
	            	'Namespace of module'
	            ),

	            new InputOption(
	            	'name',
	            	'',
	            	InputOption::VALUE_OPTIONAL,
	            	'Name of module'
	            ),

	            new InputOption(
	            	'runame',
	            	'',
	            	InputOption::VALUE_OPTIONAL,
	            	'Russian name of module'
	            ),

	            

	            new InputOption(
	            	'rudescription',
	            	'',
	            	InputOption::VALUE_OPTIONAL,
	            	'Russian name description of module'
	            ),

	            new InputOption(
	            	'vers',
	            	'',
	            	InputOption::VALUE_OPTIONAL,
	            	'Version of module'
	            ),
	   		);

	        $this
	            ->setName('module:create')
	            ->setDescription('Creates Bitrix module.')
	            ->setDefinition($this->options);
	    }

	    protected function execute(InputInterface $input, OutputInterface $output){
	    	$helper = $this->getHelper('question');

	    	$definition = $this->getDefinition();

	    	$inputValues = array();

	    	foreach($this->options as $option){
	    		$optionName = $option->getName();
	    		$value = $input->getOption($optionName);
	    		while(empty($value)){
		    		$option = $definition->getOption($optionName);
			    	$value = $helper->ask($input, $output, new Question(
			    		$option->getDescription().': ',
			    		$option->getDefault()
			    	));
	    		}
	    		//$value = iconv('cp1251', 'utf8', $value);
	    		$inputValues[$optionName] = $value;
	    	}

	    	if($this->copyFiles(
	    		__DIR__.'/data/module/',
	    		$_SERVER['DOCUMENT_ROOT'].'/local/modules/',
	    		array(
		    		'%UNDER%' => 		$inputValues['namespace'].'_'.$inputValues['name'],
		    		'%DOT%' => 			$inputValues['namespace'].'.'.$inputValues['name'],
		    		'%UNDER_CAPS%' => 	strtoupper($inputValues['namespace'].'_'.$inputValues['name']),
		    		'%RU_NAME%' => 		$inputValues['runame'],
		    		'%RU_DESC%' => 		$inputValues['rudescription'],
		    		'%CLASS%' => 		ucfirst($inputValues['namespace']).ucfirst($inputValues['name']),
		    		'%VERSION%' => 		$inputValues['vers']
		    	),
		    	$output
	    	)){
		        $output->writeln('<info>Completed</info>');
	    	}

	    }

	    protected function copyFiles($from, $to, $replaces, $output){
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