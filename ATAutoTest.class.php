<?php
require_once "simple_html_dom.php";
require_once "Snoopy.class.php";
require_once "ATGenCode.class.php";

class ATAutoTest{
	private $snoopy;
	private $homepage;

	public function __construct($homepage){
		$this->homepage = $homepage;
	}

	/**
	 * 中文
	 * @return ATPage
	 */
	public function access($url,$query=""){
		//gen code
		list($con,$act) = explode("/", $url);
		$con = empty($con) ? "default" : $con;		
		$act = empty($act) ? "default" : $act;		
		ATGenCode::getInstance($con)->genConFile();
		

		//
		$data = array();
		if(empty($query) === false){
			parse_str($query,$data);
		}
		if(isset($this->snoopy) == false){
			$this->snoopy = new Snoopy();
		}
		$files = array();
		$remarkMap = array();
		foreach($data as $key=>$val){

			if($key[0] == "@"){
				$k = substr($key,1);
				$files[$k]=$val;
				unset($data[$key]);
				$remarkMap["@param ".$k] = "file";
			}else{
				$remarkMap["@param ".$key] = "todo";
			}
		}
		$remarkMap[""] = $act;

		ATGenCode::getInstance($con)->genActFunc($act,$remarkMap);
		$query = http_build_query($data);
		$url = strpos($url,"?") === false ? $url."?".$query : $url.$query;
		
		$this->snoopy->_submit_type = "multipart/form-data";
		$this->snoopy->submit($this->homepage.$url,$data,$files);

		$atPage = new ATPage($this,$this->snoopy->results);
		$atPage->setConAct($con,$act);
		return $atPage;
	}
	
}

class ATPage{
	private $con;
	private $act;
	private $htmlSource;
	private $dom;
	private $current;
	private $atAutoTest;
	public function __construct($atAutoTest,$source){
		$this->atAutoTest = $atAutoTest;
		$this->htmlSource = $source;
		$this->dom = str_get_html($source);
		
	}
	public function setConAct($con,$act){
		$this->con = $con;
		$this->act = $act;
	}

	public function find($find){
		foreach($this->dom->find($find) as $o){
			break;
		}
		//echo $o->innertext."\n";
		$this->current = $o;
		return $this;
	}

	public function assertInclude($text){
		$objText = $this->current->innertext;
		$flag = strpos($objText,$text) !== false;

		$lines = "";
		$lines.= "[source]:\n".$objText."\n";
		$lines.= "[dest]:".$text."\n";
		//echo $lines;
		if($flag == false){
			throw new Exception("fail", 1);
		}

		return $this;
	}

	public function jsIncludeLocation($loc){
		$objText = $this->current->innertext;
		if(strpos($objText,"window.location.href=") === false){
			throw new Exception("fail", 1);
		}
		return $this->jsInclude($msg);
	}

	public function jsIncludeClose(){
		$text = "window.close();";
		return $this->jsInclude($text);		
	}

	public function jsIncludeAlert($msg){
		$objText = $this->current->innertext;
		if(strpos($objText,"alert(") === false){
			throw new Exception("fail", 1);
		}
		return $this->jsInclude($msg);
	}

	public function jsInclude($text){
		//todo @elianlin get all javascript text
		$objText = $this->current->innertext;
		$flag = strpos($objText,$text) !== false;

		$lines = "";
		$lines.= "[source]:\n".$objText."\n";
		$lines.= "[dest]:".$text."\n";
		//echo $lines;
		if($flag == false){
			throw new Exception("fail", 1);
		}		
	}

	public function assertEqauls($text){
		$objText = $this->current->innertext;
		$flag = $text == $objText;
		return $this;
	}

	public function access($url,$query){
		return $this->atAutoTest->access($url,$query);
	}
	
}

/*
$case = new ATAutoTest();
$case
->access("http://yiliysr.qq.com/","id=1&page=a")
	->find(".dv_number2")->assertInclude("span")
->access("http://yiliysr.qq.com/","id=1&page=a")
	->find(".dv_number2")->assertInclude("<span>");
*/

$homepage = "http://localhost";
$case = new ATAutoTest($homepage);
$case
->access("/","id=1&page=a&@file=/var/www/log.txt")



?>
