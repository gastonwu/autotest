<?php
function tidyHTML($buffer) {
    // load our document into a DOM object
    $dom = new DOMDocument();
    // we want nice output
    $dom->preserveWhiteSpace = false;
    $dom->loadHTML($buffer);
    $dom->formatOutput = true;
    return($dom->saveHTML());
}

// start output buffering, using our nice
// callback funtion to format the output.
ob_start("tidyHTML");

?>
<html>
    <head>
    <title>foo bar</title><meta name="bar" value="foo"><body><h1>bar foo</h1><p>It's like comparing apples to oranges.</p></body></html>
<?php
// this will be called implicitly, but we'll
// call it manually to illustrate the point.
ob_end_flush();
?>