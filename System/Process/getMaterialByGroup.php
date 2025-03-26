<?php

include "../DBConnection.php";

$rows = array();
$kueri = "SELECT MaterialCD, MaterialName FROM material WHERE GroupCD='".$_POST["groupcd"]."'";
$hasil = mysqli_query($conn,$kueri);
while($row = mysqli_fetch_array($hasil)){
    $rows[] = $row;
}
$result = json_encode($rows);

echo $result;
?>