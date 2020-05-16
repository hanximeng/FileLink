<?php
if( $_SERVER['HTTP_REFERER'] == "" ){echo "error";exit;}//不允许直接访问该文件

//配置区
$name = '';//Github的昵称
$username = '';//Github的用户名
$email = '';//Github绑定的邮箱
$token = '';//获取的Token
$repo = '';//库名称

//各种函数
class commonFunction{
    function callInterfaceCommon($URL,$type,$params,$headers){
        $ch = curl_init($URL);
        $timeout = 5;
        if($headers!=""){
            curl_setopt ($ch, CURLOPT_HTTPHEADER, $headers);
        }else {
            curl_setopt ($ch, CURLOPT_HTTPHEADER, array('Content-type: application/json'));
        }
        curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        switch ($type){
            case "GET" : curl_setopt($ch, CURLOPT_HTTPGET, true);break;
            case "POST": curl_setopt($ch, CURLOPT_POST,true);
                curl_setopt($ch, CURLOPT_POSTFIELDS,$params);break;
            case "PUT" : curl_setopt ($ch, CURLOPT_CUSTOMREQUEST, "PUT");
                curl_setopt($ch, CURLOPT_POSTFIELDS,$params);break;
            case "PATCH": curl_setopt($ch, CULROPT_CUSTOMREQUEST, 'PATCH');
                curl_setopt($ch, CURLOPT_POSTFIELDS, $params);break;
            case "DELETE":curl_setopt ($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
                curl_setopt($ch, CURLOPT_POSTFIELDS,$params);break;
        }
        $file_contents = curl_exec($ch);
		if(curl_errno($ch)) {
			echo 'Curl error: ' . curl_error($ch);
		}
        curl_close($ch);
        return $file_contents;
    }
}

function foo($fileEx, $content, $name, $email, $username, $token, $repo) {
	$params="{\"message\": \"init\",\"branch\": \"master\",\"committer\": {\"name\": \"".$name."\",\"email\": \"".$email."\"},\"content\": \"".$content."\"}";
	date_default_timezone_set('PRC');
	//利用时间+Md5避免文件名冲突
	$filename = date('Ymdhis', time()).md5($content).$fileEx;
	$url='https://api.github.com/repos/'.$username.'/'.$repo.'/contents/'.$filename;
	$cf = new commonFunction();
	$headers=array('User-Agent: '.$username, 'Authorization:token '.$token);
	$action="PUT";
	$strResult = $cf->callInterfaceCommon($url, $action, $params, $headers);
	return "https://cdn.jsdelivr.net/gh/".$username."/".$repo."@master/".$filename." "."https://raw.githubusercontent.com/".$username."/".$repo."/master/".$filename;
}

$data = file_get_contents("php://input");
$index = strpos($data,",");
$indexExs = strpos($data,".");
$indexExe = strpos($data," ");
$fileEx = substr($data,$indexExs,$indexExe-$indexExs);
$dataPure = substr($data, $index+1);

$rv = foo($fileEx, $dataPure, $name, $email, $username, $token, $repo);
echo $rv;