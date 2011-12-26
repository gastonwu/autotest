<?php
require_once "simple_html_dom.php";
require_once "Snoopy.class.php";
require_once "ATGenCode.class.php";


$dom = str_get_html(file_get_contents("./codetpl/template.tpl.php"));
$codeDom = null;
foreach($dom->find(".code") as $codeDom){
	break;
}
//echo $codeDom;exit;
$codeDom->innertext = "<div class=\"abc\"></div>";
//echo $dom->innertext;exit;
$dom->save("./codeout/template.php");

echo tidyHTML(file_get_contents("./codeout/template.php"));exit;

file_put_contents("./codeout/template.php", tidyHTML(
	file_get_contents("./codeout/template.php")
));


function tidyHTML($buffer) {
    // load our document into a DOM object
    $dom = new DOMDocument();
    // we want nice output
    $dom->preserveWhiteSpace = false;
    $dom->loadHTML($buffer);
    $dom->formatOutput = true;
    return($dom->saveXML());
}