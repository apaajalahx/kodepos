<?php

$db = mysqli_connect('localhost','root','','api_prov');
if($db->connect_errno){
    echo "mysql Error";
}
//"province":"Bali","city":"Badung","district":"Kuta Selatan","urban":"Ungasan","postal_code":80361
function get_data(){
    global $db;
    $file = json_decode(file_get_contents("kodepos.json"),TRUE);
    foreach($file as $data){
        $provinsi = $data['province'];
        $kota = $data['city'];
        $distrik = $data['district'];
        $urban = $data['urban'];
        $postal_code = $data['postal_code'];
        @mysqli_query($db,"INSERT INTO wilayahkodepos VALUES ('$provinsi', '$kota', '$distrik', '$urban', '$postal_code')");
        echo $provinsi."|".$kota."|".$distrik."|".$urban."|".$postal_code."\n";
    }
}
get_data();
mysqli_close($db);