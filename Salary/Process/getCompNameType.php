<?php

include "../DBConnection.php";

$rows = array();
$kueri = "SELECT ComponentName, ComponentType FROM salarycomponent WHERE ComponentCode='" . $_POST["code"] . "'";
$hasil = mysqli_query($conn, $kueri);
while ($row = mysqli_fetch_array($hasil)) {
    $rows[] = $row;
}
$result = json_encode($rows);

echo $result;
?>