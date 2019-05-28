<?

	namespace Webprofy\Console\Command;

	use Symfony\Component\Console\Command\Command;
	use Symfony\Component\Console\Input\InputArgument;
	use Symfony\Component\Console\Input\InputInterface;
	use Symfony\Component\Console\Input\InputOption;
	use Symfony\Component\Console\Output\OutputInterface;
	use Symfony\Component\Console\Question\Question;
	use Symfony\Component\Console\Question\ConfirmationQuestion;


	class DeleteModuleCommand extends Command{
	    protected function configure(){
	        $this
	            ->setName('module:delete')
	            ->addArgument(
	            	'name',
	            	InputArgument::REQUIRED,
	            	'Module name in "namespace.name" format'
	            )
	            ->addArgument(
	            	'force',
	            	InputArgument::OPTIONAL,
	            	'If "force" then removes without notification'
	            )
	            ->setDescription('Removes Bitrix module.');
	    }

	    protected function execute(InputInterface $input, OutputInterface $output){
	    	$name = $input->getArgument('name');
	    	if(!$name){
	    		$output->writeln('<error>No module name specified.</error>');
	    		return;
	    	}

	    	$path = $_SERVER['DOCUMENT_ROOT'].'/local/modules/'.$name;
	    	if(!is_dir($path)){
	    		$output->writeln('<error>Module "'.$name.'" not found.</error>');
	    		return;
	    	}

	    	$accept = false;
	    	if($input->getArgument('force') == 'force'){
	    		$accept = true;
	    	}
	    	else{
		    	$helper = $this->getHelper('question');
		    	$accept = $helper->ask(
		    		$input,
		    		$output,
		    		new ConfirmationQuestion(
		    			'Remove module "'.$name.'"? (y|n)',
		    			false
		    		)
		    	);
	    	}
	    	
	    	if($accept){
	    		// $path = strtr($path, array('/' => '\\'));
			    // exec("rmdir /s /q {$path}");
			    exec("rm -rf {$path}");
	    		$output->writeln('<info>Module "'.$name.'" was removed (from '.$path.').</info>');
	    		return;
	    	}
    		$output->writeln('Cancelled');
	    }
	}