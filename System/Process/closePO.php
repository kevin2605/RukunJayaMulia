<?php

include "../DBConnection.php";

//set timezone
date_default_timezone_set("Asia/Jakarta");

if(isset($_GET["id"])){
    $query = "UPDATE purchaseorderheader SET Finish=2 WHERE PurchaseOrderID='".$_GET["id"]."'";
    echo $query;
    $result = mysqli_query($conn,$query);
}

$querys = "SELECT CategoryCD FROM purchaseorderheader WHERE PurchaseOrderID='".$_GET["id"]."'";
$results = mysqli_query($conn,$querys);
$row = mysqli_fetch_assoc($results);

if($result == 1 && $row["CategoryCD"] == "BB"){
    header("Location:../Local-Purchasing/viewPOMaterial.php?status=so-close&id=".$_GET["id"]);
}else if($result == 1 && ($row["CategoryCD"] == "BPP" || $row["CategoryCD"] == "SPR")){
    header("Location:../Local-Purchasing/viewPOOther.php?status=so-close&id=".$_GET["id"]);
}else{
    header("Location:../Local-Purchasing/viewPurchaseOrder.php?status=error&id=".$_GET["id"]);
}

?>