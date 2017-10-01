<?php
header("Content-type: text/html; charset=utf-8");
error_reporting(0);
include 'conn.php';
include 'libs/mysql.class.php';
include 'libs/AipFace.php';
include 'libs/distance.function.php';
include 'libs/export.function.php';
include 'libs/PHPExcel.class.php';

$M = new mysql();
$M->connect(DB_HOST,DB_USER,DB_PASS,DB_NAME);
$aipFace = new AipFace(APP_ID, API_KEY, SECRET_KEY);

switch (isset($_REQUEST['action'])?$_REQUEST['action']:null) {
    //设置签到
    case 'setSignin':
        $room = isset($_POST['room'])?$_POST['room']:null; //教室号
        $lat = isset($_POST['lat'])?$_POST['lat']:null; //纬度
        $long = isset($_POST['long'])?$_POST['long']:null; //经度
        $class = isset($_POST['class'])?$_POST['class']:null; //班级
        if($M->getRoom($room)){
            echo '{"status":"1"}';
        }else{
            $result = $M->setSignin($room,$lat,$long,$class);
            echo $result ? '{"status":"0"}':'{"status":"1"}';
        }
        break;
    //结束签到
    case 'endSignin':
        $room = isset($_POST['room'])?$_POST['room']:null; //教室号
        if($M->getRoom($room)){
            if($M->endSignin($room)){
                $result = $M->clearSignin($room);
                echo $result ? '{"status":"0"}':'{"status":"1"}';
            }else{
                echo '{"status":"1"}';
            }
        }else{
            echo '{"status":"1"}';
        }
        break;
    //获取签到信息
    case 'getSignin':
        $room = isset($_POST['room'])?$_POST['room']:null; //教室号
        if(!$M->getRoom($room)){
            echo '{"status":"1"}';
        }else{
            $result = $M->getSignin($room);
            echo $result ? json_encode($result):'{"status":"2"}';
        }
        break;
    //获取班级列表
    case 'getClass':
        $result = $M->getClass();
        echo $result ? json_encode($result,JSON_UNESCAPED_UNICODE):'{"status":"2"}';
        break;
    //获取班级学生信息
    case 'getStudent':
        $class = isset($_POST['class'])?$_POST['class']:null; //教室号
        $result = $M->getStudent($class);
        echo $result ? json_encode($result):'{"status":"1"}';
        break;
    //导出签到信息
    case 'download':
        $room = isset($_GET['room'])?$_GET['room']:null; //教室号
        if(!$M->getRoom($room)){
            echo '<h2>下载失败，当前教室不存在！</h2>';
            header('refresh:1;url=teacher.html');
        }else{
            $result = $M->getSignin($room);
            $str = iconv('UTF-8','GB2312//IGNORE',"姓名,学号,签到状态,请假理由,签到时间\n");
            foreach ($result as $key => $value) {
                $name = iconv('UTF-8','GB2312//IGNORE',$value['name']);
                $number = iconv('UTF-8','GB2312//IGNORE',$value['number']);
                $status = iconv('UTF-8','GB2312//IGNORE',$value['status']?'已请假':'已签到');
                $reason = iconv('UTF-8','GB2312//IGNORE',$value['reason']);
                $str .= $name.",\t".$number.",".$status.",".$reason.",".$value['time']."\n";
            }
            $filename = $room.'Room-'.date('Ymd').'.csv';
            export_csv($filename,$str);
        }
        break;
    //人脸注册
    case 'addFace':
        $name = isset($_POST['name'])?$_POST['name']:null; //姓名
        $number = isset($_POST['number'])?$_POST['number']:null; //学号
        $random = time().mt_rand(1111,9999);
        $face_tmp = isset($_FILES["file"]["tmp_name"][0])?$_FILES["file"]["tmp_name"][0]:null;
        $file_name = "uploads/".$random.".jpg";
        move_uploaded_file($face_tmp,$file_name);
        $result = $aipFace->addUser($number,$name,'student',file_get_contents($file_name));
        if(array_key_exists('error_code',$result)){echo '<h2>人脸注册失败，请将信息填写完整！</h2>';header('refresh:1;url=face.html');}else{echo '<h2>人脸注册成功！</h2>';header('refresh:0.5;url=face.html');}
        break;
    //班级导入
    case 'importClass':
        $random = time().mt_rand(1111,9999);
        $face_tmp = isset($_FILES["file"]["tmp_name"][0])?$_FILES["file"]["tmp_name"][0]:null;
        $file_name = "uploads/".$random.".xls";
        move_uploaded_file($face_tmp,$file_name);
        $excelReader = PHPExcel_IOFactory::createReader('Excel5');
        try {
            $excelLoad = $excelReader->load($file_name,$encode='utf-8');
        }catch(Exception $e){
            echo '<h2>导入失败！请检查Excel格式是否正确！</h2>';
            header('refresh:1;url=teacher.html');
            exit;
        }
        $sheet = $excelLoad->getSheet(0); //取得sheet(0)表
        $highestRow = $sheet->getHighestRow(); //取得总行数
        for($i=2; $i<=$highestRow; $i++) {
            $number = $excelLoad->getActiveSheet()->getCell("A".$i)->getValue(); //学号
            $name = $excelLoad->getActiveSheet()->getCell("B".$i)->getValue(); //姓名
            $class = $excelLoad->getActiveSheet()->getCell("C".$i)->getValue(); //班级
            $result = $M->importClass($number,$name,$class);
        }
        if(!$result){echo '<h2>导入失败！已有此班级或Excel格式错误！</h2>';header('refresh:1;url=teacher.html');exit;}else{echo '<h2>导入成功！</h2>';header('refresh:0.5;url=teacher.html');}
        break;
    //人脸上传
    case 'identifyFace':
        $random = time().mt_rand(1111,9999);
        $face_tmp = isset($_FILES["file"]["tmp_name"][0])?$_FILES["file"]["tmp_name"][0]:null;
        $file_name = "uploads/".$random.".jpg";
        $status = move_uploaded_file($face_tmp,$file_name);
        if($status){echo '{"status":"0","file_name":"'.$file_name.'"}';}else{echo '{"status":"1"}';}
        break;
    //提交签到
    case 'Signin':
        $room = isset($_POST['room'])?$_POST['room']:null; //教室号
        $number = isset($_POST['number'])?$_POST['number']:null; //学号
        $name = isset($_POST['name'])?$_POST['name']:null; //姓名
        $file_name = isset($_POST['file_name'])?$_POST['file_name']:null; //文件名
        $slat = isset($_POST['lat'])?$_POST['lat']:null; //纬度
        $slong = isset($_POST['long'])?$_POST['long']:null; //经度
        if($M->getRoom($room)){
            if($M->checkSign($room,$number)){
                echo '{"status":"3"}';
            }else{
                $result = $aipFace->identifyUser('student',file_get_contents($file_name));
                if(array_key_exists('error_code',$result)){
                    echo '{"status":"1"}';
                }else{
                    if($result['result'][0]['uid'] == $number){
                        $getRoom = $M->getRoom($room);
                        if($getRoom){
                            $tlat = $getRoom['lat'];
                            $tlong = $getRoom['long'];
                            $distance = getDistance($slat,$slong,$tlat,$tlong);
                            if($distance < 20) {
                                $signin = $M->Signin($room,$tlat,$tlong,$name,$number);
                                echo $signin ? '{"status":"0","scores":"'.$result['result'][0]['scores'][0].'"}':'{"status":"1"}';
                            }else{
                                echo '{"status":"2"}';
                            }
                        }else{
                            echo '{"status":"4"}';
                        }
                    }else{
                        echo '{"status":"1"}';
                    }
                }
            }
        }else{
            echo '{"status":"4"}';
        }
        break;
    //提交请假
    case 'Leave':
        $room = isset($_POST['room'])?$_POST['room']:null; //教室号
        $number = isset($_POST['number'])?$_POST['number']:null; //学号
        $name = isset($_POST['name'])?$_POST['name']:null; //姓名
        $lat = isset($_POST['lat'])?$_POST['lat']:null; //纬度
        $long = isset($_POST['long'])?$_POST['long']:null; //经度
        $reason = isset($_POST['reason'])?$_POST['reason']:null; //请假理由
        if($M->getRoom($room)){
            if($M->checkSign($room,$number)){
                echo '{"status":"3"}';
            }else{
                $leave = $M->Signin($room,$lat,$long,$name,$number,'1',$reason);
                echo $leave ? '{"status":"0"}':'{"status":"1"}';
            }
        }else{
            echo '{"status":"4"}';
        }
        break;
    default:
        echo '{"status":"1"}';
        break;
}
