<?php

include "../DBConnection.php";

$tableName = "";

if(isset($_POST["groupcd"])){
    if($_POST["groupcd"] == "BPPK"){
        $tableName = "mat_ppk_ledger";
    }else if($_POST["groupcd"] == "BPPH"){
        $tableName = "mat_pph_ledger";
    }else if($_POST["groupcd"] == "BPETK"){
        $tableName = "mat_petk_ledger";
    }
}

$rows = array();
$kueri = "SELECT RemainingAmount, UnitCD FROM ".$tableName." ORDER BY 1 DESC LIMIT 1";
$hasil = mysqli_query($conn,$kueri);
while($row = mysqli_fetch_array($hasil)){
    $rows[] = $row;
}
$result = json_encode($rows);

echo $result;
?>