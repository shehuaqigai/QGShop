(function(){
    //首页视图生成页
    var adminView=Backbone.View.extend({
        el:"#adminPage",//首页容器ID
        events:{
            'click dt':'clickSetEventHandler',//绑定设置点击展开和收缩的事件
            'click dl .switchContainer dd':'itemSettingEvent'//点击具体的选项生成设置页面事件
        },
        models:null,//模型层
        template:ADMIN.tpl,//模板
        //初始化应用
        initialize:function(){
            this.models=this.collection;
            this.render();
        },
        //渲染首页模板
        render:function(){
            this.$el.html(this.template['adminHome']());
            this.insertLeftNav();

        },
        //生成左边的导航栏
        insertLeftNav:function(){
           var self=this;
           var $ul=this.$el.find('.leftNav>ul');
           var li='';
            //遍历导航列表数据
            this.models.each(function(value,key){
            li+=self.template['navListUI'](value.attributes);
            });
            $ul.append(li);
            $ul.find('.switchContainer').hide();

        },
        /**
         * 点击设置分类按钮实现展开收缩功能
         * @param e
         */
        clickSetEventHandler:function(e){
            var ele=e.target;

            var dl=$(ele).parent();
            var container=dl.find('.switchContainer');

            if(container.is(":hidden")){
                container.show(200,'linear',function(){
                    dl.find('i').removeClass('icon-contract').addClass("icon-expand");
                });
            }else{
                container.hide(200,'linear',function(){
                    dl.find('i').removeClass('icon-expand').addClass("icon-contract");
                });

            }
        },
        /**
         *点击具体的选项生成对应的配置视图
         * 有控制器来调度
         * @param e
         */
        itemSettingEvent:function(e){
            Backbone.Events.off("changeTitle");
            var ele= e.target;
            var dl=$(ele).closest('dl');
                e.preventDefault();
            Backbone.Events.on("changeTitle",function(title,setting){
                $('#showContent .currentTitle').html($('.leftNav .'+setting+'+.switchContainer').find('.'+title).html());
            });
            var className=dl.find('dt').attr('class');
             var url=document.location.href;//获取当前页面的url
                 url=url.split("#",1);//去除#之后的url
               //这里会触发控制器，然后有控制器来调度视图分配
               window.location.href=url+"#"+className+"/"+ele.className;//跳转到指定的配置页面
        },
        destroy:function(){
            this.remove();
        }


    });
    window.ADMIN.V.adminView=adminView;
}())
