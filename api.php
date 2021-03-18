<?php
// development mode.
declare(strict_types=1);
ini_set('display_errors', 'TRUE');
error_reporting(E_ALL & ~E_NOTICE);
// production mode.
//error_reporting(0);
require_once(__DIR__.'/config.php');
require_once(__DIR__.'/apps.php');
header('content-type: application/json');
$data = new apps();
if(isset($_GET['id']) && !empty($_GET['id'])){
    echo $data->get_data($_GET['id']);
} elseif(isset($_GET['prov']) && !empty($_GET['prov'])){
    if(isset($_GET['city']) && !empty($_GET['city'])){
        if(isset($_GET['district'])){
            if(isset($_GET['urban'])){
                echo $data->postal_code($_GET['prov'],$_GET['city'],$_GET['district'],$_GET['urban']);
            } else {
                echo $data->postal_code($_GET['prov'],$_GET['city'],$_GET['district']);
            }
        } else {
            echo $data->postal_code($_GET['prov'],$_GET['city']);
        } 
    } else {
        echo $data->postal_code($_GET['prov']);
    }
} else {
    echo $data->postal_code($_GET['prov']);
}