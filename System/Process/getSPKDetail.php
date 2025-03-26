<?php

include "../DBConnection.php";

$rows = array();
$kueri = "SELECT p.ProductionOrderID, p.Description, p.MachineCD, m.MachineName, p.ProductCD, pr.ProductName, pr.WeightPerPcs, g.GroupCD, g.GroupName, p.QtyOrder, p.QtyProduced
          FROM productionorder p, machine m, product pr, groups g
          WHERE p.Status='0'
                AND p.MachineCD=m.MachineCD
                AND p.ProductCD=pr.ProductCD
                AND p.GroupCD=g.GroupCD
                AND p.ProductionOrderID='".$_POST["spk"]."'";
$hasil = mysqli_query($conn,$kueri);
while($row = mysqli_fetch_array($hasil)){
    $rows[] = $row;
}
$result = json_encode($rows);

echo $result;
?>