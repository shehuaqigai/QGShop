<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, target-densitydpi=device-dpi, initial-scale=1, user-scalable=0, maximum-scale=1">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black">
    <title>微商城后台管理</title>
    <link rel="shortcut icon" href="/QGShop/Public/imgs/favicon.ico" type="image/x-icon">
    <link href="/QGShop/Public/css/lib/reset.css" rel="stylesheet"/>
    <link href="/QGShop/Public/css/lib/iconmoon.css" rel="stylesheet"/>





<link href="/QGShop/Public/css/custom/adminLogin.css" rel="stylesheet"/>
</head>
<body>
<div id="loginForm">
     <table border="0">
     <tr>
         <td class="icon-user3">  用户名</td>
         <td><input type="text" class="userName" placeholder="用户名"/></td>
     </tr>
     <tr>
         <td class="icon-unlocked">  密  码</td>
         <td><input type="password" class="password" placeholder="密码"/></td>
     </tr>
     <tr>
         <td>验证码:</td>
         <td>
             <div class="verifyCode">
                 <a href="javascript:void(0);" class="refreshCode">看不清</a>
                 <img src="/QGShop/index.php/admin/Index/generatorVerifyCode" alt="看不清"/>
                 <input type="text" class="checkCode"/>
             </div>
         </td>
     </tr>
     <tr>
         <td colspan="2"><button>登录</button></td>
     </tr>
     <tr>
         <td colspan="2" class="errorMesg"></td>
     </tr>
     </table>
</div>
<script>
    (function(){
        function $(selector){
            return document.querySelector(selector);
        }
        var refreshCodeLink=$(".refreshCode");
        var img=$('img');
        var button=$('button');
        var userName=$('.userName');
        var password=$('.password');
        var checkCode=$('.checkCode');
        var errcodeMesg=$('.errorMesg');
        refreshCodeLink.addEventListener('click',function(e){
            img.src="/QGShop/index.php/admin/Index/generatorVerifyCode";
        });
        button.addEventListener('click',function(e){
            var user=userName.value;
            var passwd=password.value;
            var code=checkCode.value;
            if(!user){
                errcodeMesg.innerHTML="用户名不能为空!";
                return;
            }
            if(!passwd){
                errcodeMesg.innerHTML="请输入正确的密码!";
                return;
            }
            if(!code){
                errcodeMesg.innerHTML="请输入验证码!";
                return;
            }
            var url="/QGShop/index.php/Admin/Index/verifyCode";
            ajax(url,'code='+code,function(data){
                if(!data){
                    errcodeMesg.innerHTML="验证码错误!";
                }else{

                    verifyUser('user='+user+"&passwd="+passwd);
                }

            });

        });

        function verifyUser(postData){
            var url="/QGShop/index.php/Admin/Index/verifyUser";
            ajax(url,postData,function(data){
                if(data == 'ok'){
                    location.href="/QGShop/index.php/Admin/Index/adminHome";
                }else{
                    errcodeMesg.innerHTML="用户名或密码错误!";
                }
            });

        }
        function ajax(url,data,callback){
            var $ajax=new XMLHttpRequest();

            $ajax.open("POST",url,true);

            if($ajax.overrideMimeType){
                $ajax.overrideMimeType("text/xml");
            }

            $ajax.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

            $ajax.onreadystatechange=function(e){
                if($ajax.readyState==4 && $ajax.status==200){
                }
            }
            $ajax.onload=function(){
                callback && callback($ajax.responseText);
            }
            $ajax.onerror=function(err){console.log(err);}
            $ajax.send(data);

        }

    }());
</script>
</body>
</html>