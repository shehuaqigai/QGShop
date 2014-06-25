
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
        var router=new ADMIN.C.AdminRouter();//创建路由
        Backbone.history.start();//开启路由追踪

   // }

}());


