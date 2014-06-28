
(function(){
 // window.onload=function(){
        //文档加载完成后1秒删除所有Js脚本引用保持页面干净
        //百度以及其他一些自动嵌入的脚本非常可恶
    /*
        setTimeout(function(){
            var scripts=document.scripts;
            var count=scripts.length;
            while(count>0){
                count--;
                scripts[count].remove();
            }
        },1000);
        */  
        module("adminTest.js->",{setup:function(){
            ok(true,"开始初始化后台admin,创建路由");
        },teardown:function(){
            ok(true,"admin运行成功!");
        }});

        test("创建router对象",function(){
             var router=new ADMIN.C.AdminRouter();//创建路由
             ok(!!router,"创建router开启路由追踪");
             Backbone.history.start();//开启路由追踪
        })         
       

   // }

}());


