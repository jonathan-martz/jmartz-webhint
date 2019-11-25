<?php

class RoboFile extends \Robo\Tasks
{
	public function npmInstall(){
		$this->taskNpmInstall()->run();
	}

	public function loadConfig(){
        $ip = '195.201.38.163';
        $user = 'root';

        $this->taskRsync()
            ->toPath('.')
            ->fromHost($ip)
            ->fromUser($user)
            ->toPath('/var/www/performance.jmartz.de/config')
            ->recursive()
            ->progress()
            ->run();
    }

	public function execute($name)
	{
		$filename = 'page.json';
		$file = file_get_contents($filename);

		$folder = 'reports/'.date('d-m-y-H').'/';

		if(!file_exists('reports')){
			$this->_exec('mkdir reports');
		}

		if(!file_exists('mkdir '.$folder)){
			$this->_exec('mkdir '.$folder);
		}

		if(strlen($file) > 0){
			$pages = json_decode($file, JSON_FORCE_OBJECT);
			foreach($pages as $page){
				if($page['name'] == $name){
					foreach($page['urls'] as $url){
						$filename = $folder.'hint-'.str_replace(['https://', 'http://','/'],['','','-'],$url['url'].'.json');

						$this->_exec('./node_modules/hint/dist/src/bin/hint.js '.$url['url'].' -f json >> '.$filename);
					}
				}
			}
		}
	}

	public function copy(){
		$this->taskRsync()
			 ->fromPath('reports')
			 ->toHost('195.201.38.163')
			 ->toUser('root')
			 ->toPath('/var/www/performance.jmartz.de/shared')
			 ->recursive()
			 ->progress()
			 ->run();
	}
}

?>
