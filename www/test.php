<?php
//ini_set("display_errors", 1);
$source = "https://www.w3schools.com/html/html_tables.asp";

$c = curl_init($source);
$c  = curl_init();
curl_setopt($c, CURLOPT_URL, $source);
curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
curl_setopt($c, CURLOPT_HEADER, false);
$data = curl_exec($c);
curl_close($c);

$doc = new DOMDocument();
$doc->loadHTML($data);
$table = $doc->getElementById("customers");
$rows = $table->getElementsByTagName("tr");
$result = array();
foreach($rows as $row) {
    if($row->hasChildNodes()) {
        foreach($row->childNodes as $cell) {
            $line = array();
            if($cell->nodeName == "th" || $cell->nodeName == "td") {
                $line[] = $cell->nodeValue;
                $result[] = $line;
            }
        }
    }
}

echo json_encode($result);
