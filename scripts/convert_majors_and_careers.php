<?php

/* 
 * run in console in this dir
 * 
 * convert the html college major list and echo the json format string
 * for now, it only echo the name of the major, no category information is included
 * 
 * also conver the careers to json and php objects
*/

function readFileAndEcho($file_name) {
    $list_string = file_get_contents($file_name);

    $dom = new DOMDocument;
    $dom->loadHTML($list_string);

    $all_a = $dom->getElementsByTagName("a");

    $major_names = array("Other");

    foreach ( $all_a as $one_ele )
    {
            if ($one_ele->getAttribute("class") == "gwt-Anchor") {
                    $major_names[] = $one_ele->textContent;
            }
    }

    return array("json"=>json_encode($major_names), "php"=>serialize($major_names));
}

$fd = fopen("output.txt","w");

$result = readFileAndEcho("./majors.html");

fwrite($fd, "major:\n\n".$result["json"]."\n\n".$result["php"]."\n\n");

$result = readFileAndEcho("./careers.html");

fwrite($fd, "career:\n\n".$result["json"]."\n\n".$result["php"]."\n\n");

fclose($fd);

?>