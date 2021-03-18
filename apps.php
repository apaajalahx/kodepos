<?php
declare(strict_types=1);
ini_set('display_errors', 'TRUE');
error_reporting(E_ALL & ~E_NOTICE);
class apps{

    private $db;

    private $return;

    public function __construct(){
        global $config;
        $this->db = new mysqli($config['host'],$config['username'],$config['password'],$config['database']);
        if($this->db->errno){
            echo "error mysql ".$this->db->errno;
        }
    }

    public function postal_code($prov = Null,$city = Null, $district = Null, $urban = Null,$type='Provinsi'){
        if(!is_null($prov) && !is_null($city) && !is_null($district) && !is_null($urban)){
            $prov = $this->db->real_escape_string($prov);
            $city = $this->db->real_escape_string($city);
            $district = $this->db->real_escape_string($district);
            $urban = $this->db->real_escape_string($urban);
            $stmt = "SELECT DISTINCT * FROM wilayahkodepos WHERE province = '$prov' AND city = '$city' AND district = '$district' AND urban = '$urban'";
            $type = 'Kode Pos';
            $key = 'postal_code';
        } else
        if(!is_null($prov) && !is_null($city) && !is_null($district)){
            $prov = $this->db->real_escape_string($prov);
            $city = $this->db->real_escape_string($city);
            $district = $this->db->real_escape_string($district);
            $stmt = "SELECT DISTINCT * FROM wilayahkodepos WHERE province = '$prov' AND city = '$city' AND district = '$district' ORDER BY urban";
            $type = 'Kelurahan';
            $key = 'urban';
        } else 
        if(!is_null($prov) && !is_null($city)){
            $prov = $this->db->real_escape_string($prov);
            $city = $this->db->real_escape_string($city);
            $stmt = "SELECT DISTINCT district FROM wilayahkodepos WHERE province = '$prov' AND city = '$city' ORDER BY district";
            $type = 'Kecamatan';
            $key = 'district';
        } else
        if(!is_null($prov)){
            $prov = $this->db->real_escape_string($prov);
            $stmt = "SELECT DISTINCT city FROM wilayahkodepos WHERE province = '$prov' ORDER BY city";
            $type = 'Kabupaten';
            $key = 'city';
        } else
        if(is_null($prov) && is_null($city) && is_null($district) && is_null($urban)){
            $stmt = "SELECT DISTINCT province FROM wilayahkodepos";
            $key = 'province';
        } else {
            $stmt = "SELECT DISTINCT province FROM wilayahkodepos";
            $key = 'province';
        }
        if($result = $this->db->query($stmt)){
            return $this->ret_v2_data($result,$type,$key);
        }
    }

    private function ret_v2_data($row,$wilayah,$key){
        $data = [];
        while($f = $row->fetch_object()){
            $data[$f->$key] = $f->$key;
        }
        return json_encode(array(
                    'wilayah' => $wilayah,
                    'data' => $data
                            ));
    }

    public function get_data($id){
        $d = strlen($id);
        $wil = $this->wil($id)['0'];
        $p = $this->db->prepare("SELECT * FROM wilayah_2020 WHERE LEFT(kode,?)=? AND CHAR_LENGTH(kode)=? ORDER BY nama");
        $p->bind_param("sss",$d,$id,$wil);
        $p->execute();
        $result = $p->get_result();
        $p->close();
        return $this->ret_data($result,$id,null);
    }

    public function get_prov(){
        $p = $this->db->prepare("SELECT kode,nama FROM wilayah_2020 WHERE CHAR_LENGTH(kode)=2 ORDER BY nama");
        $p->execute();
        $result = $p->get_result();
        $p->close();
        return $this->ret_data($result,0,'Provinsi');
    }
    private function ret_data($row,$id,$wilayah=null){
        $data = [];
        while($f = $row->fetch_object()){
            $data[$f->kode] = $f->nama;
        }
        if($wilayah==null){
            $wilayah = $this->wil($id)[1];
        }
        return json_encode(array(
                    'wilayah' => $wilayah,
                    'data' => $data
                            ));
    }

    private function wil($id){
        $wilayah = array(
                    2 => array(5,'Kota/Kabupaten','Kab'),
                    5 => array(8,'Kecamatan','Kec'),
                    8 => array(13,'Kelurahan','Kel'),
                        );
        return $wilayah[strlen($id)];
    }
}