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
		$this->config['webhint'] = $this->loadWebhintConfig();
		$this->config['server'] = $this->loadServerConfig();
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
		$this->taskRsync()
			->toPath('.')
			->fromHost($this->config['server']['ip'])
			->fromUser($this->config['server']['user'])
			->fromPath($this->config['server']['folder'] . self::config)
			->recursive()
			->progress()
			->run();
	}

	/**
	 * @return void
	 */
	public function npmInstall():void{
		$this->taskNpmInstall()->run();
	}

	/**
	 * @return void
	 */
	public function loadConfig():void{
        $this->taskRsync()
            ->toPath('.')
            ->fromHost($this->config['server']['ip'])
            ->fromUser($this->config['server']['user'])
            ->fromPath($this->config['server']['folder'].self::config)
            ->recursive()
            ->progress()
            ->run();
    }

	/**
	 * @return void
	 */
	public function execute():void
	{
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
					$this->_exec('./node_modules/hint/dist/src/bin/hint.js '.$url['url'].' -f json >> '.$filename);
				}
			}
		}
	}

	/**
	 * @return void
	 */
	public function copy():void{
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
