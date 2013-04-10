<?php session_start();?>
<html>
<head>

<?php


//Takes an input string and capitalizes the beginning of words and adds spaces according to generally used naming conventions.
function makeName($originalname){
$filename = $originalname;
// $filename = substr($filename, 0, count($filename) - 5);
//echo $filename . " <br/>";
$splitName = preg_replace('~[A-Z]~', ' $0', $filename);
//echo $splitName . " <br/>";
$newname1 = strtoupper($splitName[0]) . substr($splitName, 1);
//echo $newname1 . "<br/>";
$newname2 = preg_replace('/\..*/', "", $newname1);
//echo $newname2 . "<br/>";
$newname3 = preg_replace('/_/', " ", $newname2);
//echo $newname3 . "<br/>";
$newname4 = preg_split('/\s/', $newname3);
//var_dump($newname4);
//echo "<br/>";
foreach($newname4 as &$var1){
$var1 = strtoupper($var1[0]) . substr($var1, 1);
}
unset($var1);
//var_dump($newname4);
//echo "<br/>";
$newname5 = "";
foreach($newname4 as $var1){
$newname5 .= $var1 . " ";
}
unset($var1);
$newname5 = trim($newname5);
return $newname5;
}

?>
<title><?php if(isset($title)){echo $title;}else{echo makeName(basename(__FILE__));} ?>: Adam Cutler's Website</title>
<meta name="author" content="Adam Cutler" />

<link rel="icon" type="image/png" href="http://adno.mooo.com/Head.png">
<link rel="stylesheet" type="text/css" href="/includes/CSS.css" />

</head>

<body>

<?php

include 'header.php'

?>

</body>

</html>