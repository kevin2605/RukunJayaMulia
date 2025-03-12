<?php

include "../DBConnection.php";

//set timezone
date_default_timezone_set("Asia/Jakarta");

$ctr = 0;
$ctrDB = 0;
$arrdb = [];
$arrcodes = [];
$diffDelete = [];
$diffNew = [];

//FETCH COMPONENTS IN DB
$query = "SELECT ComponentCode FROM employeecomponent WHERE NIK='".$_POST["NIK"]."'";
$result = mysqli_query($conn, $query);
while ($row = mysqli_fetch_array($result)) {
    $arrdb[$ctrDB] = $row["ComponentCode"];
    $ctrDB++;
}
echo "arrDB<br>";
print_r ($arrdb);
echo "<br>";

//INSERT KE EMPLOYEECOMPONENT
$creator = $_COOKIE["UserID"];
$NIK = $_POST["NIK"];
$arrcodes = $_POST["codes"];
$arramount = $_POST["amount"];
echo "arrCode<br>";
print_r ($arrcodes);
echo "<br>";

//SECTION UNTUK DELETE COMPONENT DI DB
$diffDelete = array_diff($arrdb,$arrcodes); // artinya arrdb - arrcodes (ini untuk delete component)
echo "delete<br>";
print_r ($diffDelete);
echo "<br>";
if(count($diffDelete) >= 0){
    foreach ($diffDelete as $index => $val) {
        echo $index;
        echo $val;

        $query = "DELETE FROM `employeecomponent` WHERE NIK='".$NIK."' AND ComponentCode='".$val."'";
        $result = mysqli_query($conn, $query);
    }
}

$diffNew = array_diff($arrcodes,$arrdb); // artinya arrcodes - arrdb (ini untuk new component)
echo "insert<br>";
print_r ($diffNew);
echo "<br>";
if(count($diffNew) > 0){
    foreach ($diffNew as $index => $val) {
        echo $index;
        echo $val;
        $amount = $arramount[$index];

        $query = "INSERT INTO `employeecomponent`(`NIK`, `ComponentCode`, `ComponentValue`) VALUES ('$NIK','$val','$amount')";
        $result = mysqli_query($conn, $query);
    }
}


logAction($conn, $creator, 'Create', 'berhasil menyimpan daftar komponen pada karyawan', 0, $NIK);
header("Location:../Employee/Edit-Employee.php?status=success-component&NIK=".$NIK);


function logAction($conn, $userID, $actionDone, $actionMSG, $actionStatus, $recordID)
{
    $timestamp = date('Y-m-d H:i:s');
    $stmt = $conn->prepare("INSERT INTO systemlog (Timestamp, UserID, ActionDone, ActionMSG, ActionStatus, RecordID) VALUES (?, ?, ?, ?, ?, ?)");
    if ($stmt) {
        $stmt->bind_param("ssssss", $timestamp, $userID, $actionDone, $actionMSG, $actionStatus, $recordID);
        $stmt->execute();
        $stmt->close();
    } else {
        error_log("Prepare failed: (" . $conn->errno . ") " . $conn->error);
    }
}

?>