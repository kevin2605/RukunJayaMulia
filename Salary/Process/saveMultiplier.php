<?php
include "../DBConnection.php";

//set timezone
date_default_timezone_set("Asia/Jakarta");


$slipnum = $_POST["slipnum"];
$code = $_POST["code"];
$multiplier = $_POST["multiplier"];


$query = "UPDATE `empsalarydetail` SET `Multiplier`='$multiplier' WHERE `SlipNum`='$slipnum' AND `ComponentCode`='$code'";
$result = mysqli_query($conn, $query);

if ($result) {
    header("Location:../Tools/view-salary-slip.php?status=success&SlipNum=".$slipnum);
} else {
    header("Location:../Tools/view-salary-slip.php?status=error&SlipNum=".$slipnum);
}

?>