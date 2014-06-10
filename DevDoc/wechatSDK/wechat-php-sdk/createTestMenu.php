<?php
define("TOKEN", "weixinEcshop");
define("APPID","wxdd108acd984a2fe1");
define("SECRET","e8206cbe90c67555609fa57f633f8c78");
$data=<<<EOF
{
     "button":[
     {
          "type":"click",
          "name":"空白占位",
          "key":"V1001_TODAY_MUSIC"
      },
      {
           "type":"view",
           "name":"进入商城",
           "url":"http://nat.nat123.net:12749/wxscwz_v1.0/"
      },
      {
           "name":"菜单",
           "sub_button":[
           {
               "type":"view",
               "name":"搜索",
               "url":"http://www.soso.com/"
            },
            {
               "type":"view",
               "name":"视频",
               "url":"http://v.qq.com/"
            },
            {
               "type":"click",
               "name":"赞一下我们",
               "key":"V1001_GOOD"
            }]
       }]
 }
EOF;
$access_token=get_Access_Token();
$url="https://api.weixin.qq.com/cgi-bin/menu/create?access_token=".$access_token;
$vpost=vpost($url,$data);
var_dump($vpost);
function get_Access_Token(){
    $appid=APPID;
    $secret=SECRET;
    $url="https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".$appid."&secret=".$secret;
    $json=file_get_contents($url);
    $result=json_decode($json);
    $access_toke=$result->access_token;
    if($access_toke){
        return $access_toke;
    }else{
        echo '获取access_token失败';
        exit(0);
    }
}

function vpost($url,$data){
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 5.01; Windows NT 5.0)');
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_AUTOREFERER, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $Info = curl_exec($ch);
    if (curl_errno($ch)) {
        return curl_error($ch);
    }
    curl_close($ch);
    return $Info;
}


?>