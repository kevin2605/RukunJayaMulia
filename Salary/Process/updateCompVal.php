<?php

include "../DBConnection.php";

$kueri = "UPDATE employeecomponent SET ComponentValue=".$_POST["value"]." WHERE NIK='".$_POST["nik"]."' AND ComponentCode='".$_POST["code"]."'";
$hasil = mysqli_query($conn,$kueri);

echo $hasil;
?>