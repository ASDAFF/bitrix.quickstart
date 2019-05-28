<?

	namespace Webprofy\Console\Command;

	use Symfony\Component\Console\Command\Command;
	use Symfony\Component\Console\Input\InputArgument;
	use Symfony\Component\Console\Input\InputInterface;
	use Symfony\Component\Console\Input\InputOption;
	use Symfony\Component\Console\Output\OutputInterface;
	use Webprofy\Console\ConsoleGeneral;


	class CreateComponentCommand extends Command{
		protected $options = null;
	    protected function configure(){
	    	$this->options = array(
	            new InputOption(
	            	'namespace',
	            	'',
	            	InputOption::VALUE_REQUIRED,
	            	'Namespace of component'
	            ),

	            new InputOption(
	            	'name',
	            	'',
	            	InputOption::VALUE_REQUIRED,
	            	'Name of component'
	            ),

	            new InputOption(
	            	'runame',
	            	'',
	            	InputOption::VALUE_REQUIRED,
	            	'Russian name of component'
	            ),

	            new InputOption(
	            	'rudescription',
	            	'',
	            	InputOption::VALUE_REQUIRED,
	            	'Russian name description of module'
	            ),

	            new InputOption(
	            	'cachetime',
	            	'',
	            	InputOption::VALUE_REQUIRED,
	            	'Cache time (5s / 10m / 24h for instance)'
	            ),

	            new InputOption(
	            	'iblock',
	            	'',
	            	InputOption::VALUE_OPTIONAL,
	            	'IBlock ID for example'
	            )
	   		);

	        $this
	            ->setName('component:create')
	            ->setDescription('Creates Bitrix component.')
	            ->setDefinition($this->options);
	    }

	    private function tpl($type, $data){
	    	ob_start();

	    	switch($type){


	    		case 'last-update':
	    			?>,
			WP::lastUpdate($data)<?
	    			break;


	    		case 'example-template-iblock':
	    			echo '<?
	foreach($arResult[\'ELEMENTS\'] as $element){
		?>
			<div>
				<?=$element[\'id\']?>.
				<b><?=$element[\'name\']?></b>
			</div>
		<?
	}
?>';
	    			break;


	    		case 'example-template-default':
					echo 'Hello <?=$arResult[\'NAME\']?>';
	    			break;



	    		case 'example-component-iblock':
	    			?>
			return array(
				'ELEMENTS' => WP::bit(
					array(
						'of' => 'elements',
						'f' => 'IBLOCK_ID=<?=$data?>; ACTIVE=Y',
						'sel' => array(
							'f' => 'ID, NAME',
							'p' => array(

							)
						)
						'map' => function($d, $f, $p){
							return array(
								'name' => $f['NAME'],
								'id' => $f['ID']
							);
						}
					)
				)
			);
	    			<?
	    			break;



	    		case 'example-component-default':
	    			?>
			return array(
				'NAME' => 'ZORED'
			);
	    			<?
	    			break;


	    	}

	    	return ob_get_clean();
	    }

	    protected function execute(InputInterface $input, OutputInterface $output){
	    	$cg = new ConsoleGeneral();

	    	$values = $cg->ask(
	    		$input,
	    		$output,
	    		$this->getHelper('question'),
	    		$this->getDefinition(),
	    		$this->options
	    	);

	    	preg_match('#(\d+)\s*([a-zа-я]+)#i', $values['cachetime'], $m);
	    	$cacheTime = $m[1].", '".$m[2]."'";
	    	$iblock = @intval($values['iblock']);

	    	if($cg->copyFiles(
	    		__DIR__.'/data/component/',
	    		$_SERVER['DOCUMENT_ROOT'].'/local/components/'.$values['namespace'].'/'.$values['name'].'/',
	    		array(
		    		'%%TWODOTS%%' => 			$values['namespace'].':'.$values['name'],
		    		'%UNDER%' => 				$values['namespace'].'_'.$values['name'],
		    		'%DOT%' => 					$values['namespace'].'.'.$values['name'],
		    		'%RU_NAME%' => 				$values['runame'],
		    		'%RU_DESC%' => 				$values['rudescription'],
		    		'%CACHE_TIME%' => 			$cacheTime,
		    		'%LAST_UPDATE%' =>  		$iblock ? $this->tpl('last-update', $iblock) : '',
		    		'%EXAMPLE_COMPONENT%' =>	$this->tpl('example-component-'.($iblock ? 'iblock' : 'default'), $iblock),
		    		'%EXAMPLE_TEMPLATE%' =>		$this->tpl('example-template-'.($iblock ? 'iblock' : 'default')),
		    	),
		    	$output
	    	)){
		        $output->writeln('<info>Completed</info>');
	    	}

	    }
	}