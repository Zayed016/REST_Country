<?php
header("Access-Control-Allow-Origin: *");

if(!isset($_GET['q']) || empty($_GET['q'])) {
    header('HTTP/1.1 404 Result not found');
    header('Content-Type: application/json; charset=UTF-8');
    die(json_encode(array('message' => 'ERROR', 'code' => 404 )));
}

set_error_handler(function ($err_severity, $err_msg, $err_file, $err_line, array $err_context)
{
 throw new ErrorException( $err_msg, 0, $err_severity, $err_file, $err_line );
}, E_WARNING);

try {
    $get = file_get_contents('https://restcountries.eu/rest/v2/name/'.$_GET['q']);

} catch (Exception $e) {
    header('HTTP/1.1 404 Result not found');
    header('Content-Type: application/json; charset=UTF-8');
    die(json_encode(array('message' => 'ERROR', 'code' => 404 )));
}

restore_error_handler();

$response = json_decode($get);

usort($response, function($a, $b) {
    $c = $a->name[0] <=> $b->name[0];
    $c .= $a->population <=> $b->population;
    return $c;
});

$region = array();
$subregion = array();

echo "<table>";
echo "<tr>
<th>Name</th>
<th>Alpha Code 2</th>
<th>Alpha Code 3</th>
<th>Flag</th>
<th>Region</th>
<th>Sub Region</th>
<th>Population</th>
<th>Languages</th>
</tr>";
foreach($response as $key => $value){
    if($key > 50) break;
    echo "<tr>";
    echo "<td>";
    echo $value->name;
    echo "</td>";
    echo "<td>";
    echo $value->alpha2Code;
    echo "</td>";
    echo "<td>";
    echo $value->alpha3Code;
    echo "</td>";
    echo "<td>";
    echo "<img height='16' width='30' src='".$value->flag."'";
    echo "</td>";
    echo "<td>";
    echo $value->region;
    echo "</td>";
    echo "<td>";
    echo $value->subregion;
    echo "</td>";
    echo "<td>";
    echo number_format($value->population);
    echo "</td>";
    echo "<td>";
    foreach($value->languages as $lang){
        echo $lang->name.", ";
    };
    echo "</td>";
    echo "</tr>";
    array_push($region,$value->region);
    array_push($subregion,$value->subregion);
}
echo "</table>";

echo "<h3>Number of Country (s): ".count($response)."</h3>";

echo "<h3>Regions:</h3>";
foreach(array_count_values($region) as $key => $value){
 echo $key." - ".$value."<br>";
}
echo "<h3>Sub Regions:</h3>";
foreach(array_count_values($subregion) as $key => $value){
    echo $key." - ".$value."<br>";
}
?>