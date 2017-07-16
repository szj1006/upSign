<?php
class mysql{
    private $conn;
    //连接数据库
    public function connect($DB_HOST,$DB_USER,$DB_PASS,$DB_NAME){
        $this->conn = mysqli_connect($DB_HOST,$DB_USER,$DB_PASS,$DB_NAME)or die('Mysql Error');
    }
    //执行SQL语句
    public function query($sql){
        return mysqli_query($this->conn,$sql);
    }
    //获取单条数据
    public function getOne($sql){
        $result = $this->query($sql);
        if($result){
            $data = mysqli_fetch_assoc($result);
            return $data;
        }
        return false;
    }
    //获取多条数据
    public function getAll($sql){
        $result = $this->query($sql);
        if($result){
            $num = mysqli_num_rows($result);
            for($i = 0;$i <= $num;$i++){
                $arr[] = mysqli_fetch_assoc($result);
            }
            array_pop($arr);
            return $arr;
        }
        return false;
    }
    //字符串编码
    public function deStr($str){
        if(get_magic_quotes_gpc()){
            return $str;
        }else{
            return addslashes($str);
        }
    }
    //检查学生是否存在
    public function checkSign($room,$number){
        if(empty($room) || empty($number)){
            return false;
        }
        return $this->getOne("select * from `sign_student` where `room` = '".$this->deStr($room)."' AND `number` = '".$this->deStr($number)."'");
    }
    //调取教室信息
    public function getRoom($room,$status='0'){
        if(empty($room)){
            return false;
        }
        return $this->getOne("select * from `sign_teacher` where `room` = '".$this->deStr($room)."' AND `status` = '".$this->deStr($status)."'");
    }
    //调取学生签到情况
    public function getSignin($room){
        if(empty($room)){
            return false;
        }
        return $this->getAll("select * from `sign_student` where `room` = '".$this->deStr($room)."'");
    }
    //设置教师端信息
    public function setSignin($room,$lat,$long,$status='0'){
        if(empty($room) || empty($lat) || empty($long)){
            return false;
        }
        return $this->query("insert into `".DB_NAME."`.`sign_teacher` (`room`, `lat`, `long`, `status`) values ('".$this->deStr($room)."', '".$this->deStr($lat)."', '".$this->deStr($long)."', '".$this->deStr($status)."')");
    }
    //写入学生签到信息
    public function Signin($room,$lat,$long,$name,$number,$status='0',$reason=''){
        if(empty($room) || empty($lat) || empty($long) || empty($name) || empty($number)){
            return false;
        }
        return $this->query("insert into `".DB_NAME."`.`sign_student` (`room`, `lat`, `long`, `name`, `number`, `status`, `reason`) values ('".$this->deStr($room)."', '".$this->deStr($lat)."', '".$this->deStr($long)."', '".$this->deStr($name)."', '".$this->deStr($number)."', '".$this->deStr($status)."', '".$this->deStr($reason)."')");
    }
}
