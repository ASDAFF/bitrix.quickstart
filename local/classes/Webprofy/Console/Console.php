<?
	namespace Webprofy\Console;

	use Symfony\Component\Console\Application;

	class Console{
		private $commands = array(
			'Webprofy\Console\Command\CreateModuleCommand',
			'Webprofy\Console\Command\DeleteModuleCommand',
			'Webprofy\Console\Command\CreateComponentCommand'
		);

		function run(){
			$app = new Application();
			foreach($this->commands as $command){
				$app->add(new $command());
			}
			$app->run();
		}
	}
?>