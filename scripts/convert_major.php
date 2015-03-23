<?php

/* convert the html college major list and echo the json format string
for now, it only echo the name of the major, no category information is included
*/


$list_string = file_get_contents("./majors-careers.html");

$dom = new DOMDocument;
$dom->loadHTML($list_string);

$all_a = $dom->getElementsByTagName("a");

$major_names = array();

foreach ( $all_a as $one_ele )
{
	if ($one_ele->getAttribute("class") == "gwt-Anchor") {
		$major_names[] = $one_ele->textContent;
	}
}

echo json_encode($major_names);



?>