<?php

include "../DBConnection.php";

$rows = array();
$kueri = "SELECT StockQty FROM material WHERE MaterialCD='".$_POST["cd"]."'";
$hasil = mysqli_query($conn,$kueri);
while($row = mysqli_fetch_array($hasil)){
    $rows[] = $row;
}
$result = json_encode($rows);

echo $result;
?>