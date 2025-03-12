<?php
include "../DBConnection.php";

// Set timezone
date_default_timezone_set("Asia/Jakarta");

$gjid = $_POST["gjid"];
$arrno = $_POST["no"];
$arrakun = $_POST["akun"];
$arrnamaakun = $_POST["namaakun"];
$arrdebit = $_POST["debit"];
$arrcredit = $_POST["credit"];

for ($i = 0; $i < count($arrno); $i++) {
    $no = $arrno[$i];
    $akun = $arrakun[$i];
    $namaakun = $arrnamaakun[$i];
    $debit = !empty($arrdebit[$i]) ? intval($arrdebit[$i]) : 0;
    $credit = !empty($arrcredit[$i]) ? intval($arrcredit[$i]) : 0;
    echo $akun . $namaakun . $debit . $credit . "<br>";

    $queryd = "UPDATE `genjournaldetail` SET `AccountCD`='$akun', `AccountName`='$namaakun', `Debit`='$debit', `Credit`='$credit' WHERE `No`='$no'";
    $resultd = mysqli_query($conn, $queryd);
}

//DELETE GJID FROM JOURNALDATA
$querydel = "DELETE FROM journaldata WHERE Notes='".$gjid."'";
$resultdel = mysqli_query($conn, $querydel);

//ADD TO JOURNALDATA
$query = "SELECT * FROM genjournaldetail WHERE GenJourID='".$gjid."'";
$result = mysqli_query($conn, $query);
while ($row = mysqli_fetch_array($result)) {
    $date = $row["JournalDate"];
    $acccd = $row["AccountCD"];
    $accname = $row["AccountName"];
    $db = $row["Debit"];
    $cr = $row["Credit"];
    $queryj = "INSERT INTO `journaldata`(`JournalDate`, `AccountCD`, `AccountName`, `Debit`, `Credit`, `Notes`)
                   VALUES ('$date','$acccd','$accname','$db','$cr','$gjid')";
    $resultj = mysqli_query($conn, $queryj);
}


if ($resultj) {
    header("Location:../Financing/view-general-journal.php?status=success-edit&id=".$gjid);
} else {
    header("Location:../Financing/view-general-journal.php?status=error-edit&id=".$gjid);
}
$conn->close();

?>