<?php

include "../DBConnection.php";

$prodcd = $_GET["product"];


echo '<h3>Histori Barang</h3>';
echo '<table class="table">
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>Stok Awal</th>
                <th>Masuk</th>
                <th>Keluar</th>
                <th>Stok Akhir</th>
                <th>Nomor</th>
                <th>Pelanggan</th>
            </tr>
        </thead>
        <tbody>';

$stokawal = 0;
$stokakhir = 0;

$query = "SELECT * FROM productflowhistory WHERE ProductCD='".$prodcd."';";
$result=mysqli_query($conn,$query);
while($row=mysqli_fetch_array($result))
{
    echo ' <tr> 
                <td>'.substr($row["Date"],0,10).' </td>
                <td>'.number_format($stokawal,0,'.',',').' </td>
                <td>'.number_format($row["FlowIn"],0,'.',',').'</td>
                <td>'.number_format($row["FlowOut"],0,'.',',').'</td>';

                $stokakhir = $stokawal + $row["FlowIn"] - $row["FlowOut"];

    echo '      <td>'.number_format($stokakhir,0,'.',',').' </td>
                <td>'.$row["ReferenceKey"].'</td>';
                if(substr($row["ReferenceKey"],0,3) == "MUT")
                {
                    echo '<td></td>';
                }else if(substr($row["ReferenceKey"],0,3) == "SIN"){
                    $queryi = "SELECT c.CustName FROM (invoiceheader ih JOIN customer c ON ih.CustID=c.CustID) WHERE InvoiceID='".$row["ReferenceKey"]."'";
                    $resulti=mysqli_query($conn,$queryi);
                    $rowi=mysqli_fetch_assoc($resulti);

                    echo '<td>'.$rowi["CustName"].'</td>';
                }else if(substr($row["ReferenceKey"],0,3) == "SPK"){
                    $queryi = "SELECT Description
                                FROM productionorder
                                WHERE ProductionOrderID ='".$row["ReferenceKey"]."'";
                    $resulti=mysqli_query($conn,$queryi);
                    $rowi=mysqli_fetch_assoc($resulti);

                    echo '<td>'.$rowi["Description"].'</td>';
                }
    echo '  </tr>';

                $stokawal = $stokakhir;
}

echo '  </tbody>
      </table>';
?>