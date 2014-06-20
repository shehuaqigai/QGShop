(function(){
    function unitTest(){
          if(!(this instanceof unitTest)){
             return new unitTest();
          }
         this.start();
    }

   var p=unitTest.prototype;
       p.start=function(){
            // 开始测试
            //定义测试模块（可选）
            module("类型判断");
            // test开启一个测试
            // 参数一，随便写，用于生成报告标识
            // 参数二，匿名函数，里边写测试代码
           test("一个基本测试例子", function() {
               ok( true, "测试布尔类型" );
               var value = "hello";
               // equal API：判断返回值与假定的值是否相等
               // 第一个，设定的值，第二个调用函数返回的值，第三个参数说明 （可选）
               equal( "hello", value, "我们期望得到hello");
           });



       }


    ADMIN.unit=unitTest;

}).call(this);