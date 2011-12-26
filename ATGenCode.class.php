<?php

class ATGenCode{
	private static $instance;
	private $con;
	private $baseTplDir = "./codetpl/";
	private $baseOutDir = "./codeout/";
	private $conFilePath;
	private $conName;

	private function __construct($con){
		$this->con = $con;
		$this->conName = $con."Controller"; 
		$this->conFilePath = $this->baseOutDir.$this->conName.".php";
	}

	public static function getInstance($con){
		if(isset(self::$instance[$con])){
			return self::$instance[$con];
		}else{
			$o = new ATGenCode($con);

			self::$instance[$con]  = $o;
			return self::$instance[$con] ;
		}
	}

	public function genConFile(){
		$conFilePath = $this->conFilePath;
		//check file exists
		if(file_exists($conFilePath) === false){
			touch($conFilePath);
		}
		//include file
		require $conFilePath;
		//check class exists
		if(class_exists($this->conName) === false){
			$conName = $this->con;
			$code = include($this->baseTplDir."con.tpl.php");
			file_put_contents($conFilePath, $code);
		}
		//gen class
	}

	private function loadConClass(){
		if(class_exists($this->conName) === false){
			require $this->conFilePath;
		}
	}

	private function mixRemark($remarkMap,$origin=array()){
		$begin = "/** \n";
		$end = " */\n";
		$desc = " * ".$remarkMap[""]."\n";
		$atText = "";

		foreach($remarkMap as $key=>$val){
			if(empty($key) || ($key[0] != "@")) continue;
			$atText .= " * {$key} {$val}\n";
		}

		$all = $begin.$desc.$atText.$end;
		return $all;
	}

	public function genActFunc($act,$remarkMap=array()){
		$this->loadConClass();
		$methodName = $act."Action";
		if(method_exists($this->conName,$methodName)){
			return;
		}
		$rc = new ReflectionClass($this->conName);
		$code = include($this->baseTplDir."act.tpl.php");

		$remark = $this->mixRemark($remarkMap);

		$conFilePath = $this->conFilePath;
		$conFileArray = file($conFilePath);
		$endLineNO = $rc->getEndLine();

		$conFileArray[$endLineNO-1] = "\n".$remark.$code."\n".$conFileArray[$endLineNO-1];

		$allCode = implode("\n",$conFileArray);
		
		file_put_contents($conFilePath, $allCode);
	}

	public function genActRemark($act,$remarkMap){
		$this->loadConClass();
		$methodName = $act."Action";
		if(method_exists($this->conName,$methodName) === false){
			return;
		}

		
	}

	public function genContRemark(){
		
	}

	public function genOutJson(){
		
	}

	public function genOutTemplate(){
		
	}
}