<?php
	require 'simplecacher.php';

	class gitAPI extends simplecacher
	{
		public $API = 'https://api.github.com/';

		public $RETRIEVE_TYPE_USER = 0;
		public $RETRIEVE_TYPE_REPO = 1;

		private $Config = array(
			'user' 		=> 'whocodes',
			'cache' 	=> true,
			'cache_dir' => '/../cache',
			'cache_exp' => 3600,
			'useragent' => 'whocodes_gitAPI_PHP'
		);

		public function iserror($content){
			return (strpos($content, '(!)') >= 1);
		}

		public function retrieve($_url){
			$url = $this->API . $_url;
			$headers = array();

			$headers[] = 'Accept: application/vnd.github.v3+json';
			$headers[] = 'User-Agent: ' . $this->Config['useragent'];

			$result = array('result' => null, 'cache' => null);

			$this->setfilefromurl($url);

			if($this->cache_isvalid()){
				$result['result'] = $this->cache_retrieve();
				$result['cache'] = true;
			}else{
				$curl = curl_init($url);
				curl_setopt_array($curl, array(
					CURLOPT_RETURNTRANSFER => 1,
					CURLOPT_HTTPHEADER => $headers
				));

				$contents = curl_exec($curl);

				if(!$contents){
					return ('(!)' . curl_errno($curl) . ': '. curl_error($curl));
				}

				curl_close($curl);

				$this->cache_save($contents);

				$result['result'] = $contents;
				$result['cache'] = false;
			}

			return $result;
		}
	}


	$git = new gitAPI;
	$content = $git->retrieve('users/whocodes/repos');

	var_dump(json_decode($content, true));
?>
