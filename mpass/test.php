<?php
require ('./Server.php');

class TestPserver implements Mpass_IExecutor {
	private function log($msg){
		error_log($msg."\n",3,"/tmp/php.server.log");
	}

	private function execLine($line){
		$tag = "GET ";
		$pos1 = strpos($line,$tag);
		$pos2 = strrpos($line," HTTP");
		$query = substr($line,$pos1+strlen($tag),$pos2-$pos1-strlen($tag));
		$ary = parse_url($query);
		parse_str($ary['query'],$data);
		$cmd = $data['path'];
		$param = $data['key'];

		$output = array();
		exec($cmd." ".$param,$output);

		$return = implode("\n", $output);

		return $return;
	}
	private function exec($content){
		$pos = strpos($content,"\n");
		$firstLine = substr($content,0,$pos);
		$this->log($firstLine);

		return $this->execLine($firstLine);
	}

	function execute(Mpass_Request $client) {

		$input = "";

		$input = $client->read(1024);
		// $this->exec($input);
		usleep(100);

		// $str = "Hello World! " . microtime(true)
		// 	            . "<pre>{$input}</pre>";

		// $response = "HTTP/1.1 200 OK\r\n"
		// 	. "Connection: close\r\n"
		// 	. "Content-Type: text/html\r\n"
		// 	. "Content-Length:" . strlen($str) . "\r\n"
		// 	. "\r\n"
		// 	. $str;

		$client->write($this->exec($input));
		//$client->write($response);
		return TRUE;
	}
}

$host = "192.168.0.136";
$port = 80;

$service = new Mpass_Server($host, $port, new TestPserver);

$service->run();

//http://192.168.0.136/?path=php%20/tmp/a.php&key=1111
