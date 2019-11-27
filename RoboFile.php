<?php

use \Robo\Tasks;

/**
 * Class RoboFile
 */
class RoboFile extends Tasks
{
	/**
	 * @var array
	 */
	const config = 'config';

	/**
	 * @var string
	 */
	const server = 'server.json';

	/**
	 * @var string
	 */
	const webhint = 'webhint.json';

	/**
	 * @var string
	 */
	const reports = 'reports';

	/**
	 * @var string
	 */
	public $date = '';

	/**
	 * @var array
	 */
	public $config = [];

	/**
	 * RoboFile constructor.
	 */
	public function __construct()
	{
		$this->date = date('d-m-y-H-i');
	}

	/**
	 * @return array
	 */
	public function loadServerConfig(): array
	{
		$filename = self::config . '/' . self::server;
		$file = file_get_contents($filename);
		return json_decode($file, JSON_FORCE_OBJECT);
	}

	/**
	 * @return array
	 */
	public function loadWebhintConfig(): array
	{
		$filename = self::config . '/' . self::webhint;
		$file = file_get_contents($filename);
		return json_decode($file, JSON_FORCE_OBJECT);
	}

	/**
	 * @return void
	 */
	public function downloadConfig(): void
	{
		// Todo: replace plain string with config
		$this->taskRsync()
			->toPath('.')
			->fromHost('195.201.38.163')
			->fromUser('root')
			->fromPath('/var/www/performance.jmartz.de/shared/' . self::config)
			->recursive()
			->progress()
			->run();
	}

	/**
	 * @return void
	 */
	public function npmInstall():void{
		$this->stopOnFail(false);
		$this->taskNpmInstall()->run();
		$this->stopOnFail(true);
	}

	/**
	 * @return void
	 */
	public function composerInstall():void{
		$this->taskComposerInstall()->run();
	}

	/**
	 * @return void
	 */
	public function execute():void
	{
		$this->config['webhint'] = $this->loadWebhintConfig();

		$folder = self::reports.'/'.$this->date.'/';

		if(!file_exists(self::reports)){
			$this->_exec('mkdir '.self::reports);
		}

		if(!file_exists($folder)){
			$this->_exec('mkdir '.$folder);
		}

		if(count($this->config['webhint']) > 0){
			foreach($this->config['webhint'] as $page){
				foreach($page['urls'] as $url){
					$filename = $folder.'hint-'.str_replace(['https://', 'http://','/'],['','','-'],$url['url'].'.json');
					$this->_exec('./node_modules/hint/dist/src/bin/hint.js '.$url['url'].' --tracking=on -f json >> '.$filename);
				}
			}
		}
	}

	/**
	 * @return void
	 */
	public function copy():void{
		$this->config['server'] = $this->loadServerConfig();

		$this->taskRsync()
			 ->fromPath('reports')
			 ->toHost($this->config['server']['ip'])
			 ->toUser($this->config['server']['user'])
			 ->toPath($this->config['server']['folder'])
			 ->recursive()
			 ->progress()
			 ->run();
	}
}

?>
