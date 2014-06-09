(function(){
    //首页视图生成页
    var wechatSet=Backbone.View.extend({
        el:"#wechatSet",
        events:{

        },
        template:ADMIN.tpl,//模板
        button:null,
        modelData:null,
        //初始化应用
        initialize:function(){
            this.data=this.modelData;
        },
        init:function(button){
            this.$el.first().empty();
            this.button=button;
            this.renderSwitch(button);

        },
        renderSwitch:function(button){
                this[button]();
        },
        //页面渲染
        interfaceSet:function(){
            this.$el.prepend(this.template[this.button]());
            var url=document.location.href;
                url=url.split("index.php",1)+'index.php?m=wechatapi&c=Index&a=index(复制到开发者配置中)';
            this.$el.find('p .configUrl').html(url);
        },
        indexPage:function(){},
        massSendMesg:function(){},
        mesgSet:function(){},
        fllowPush:function(){},
        sceneQRcode:function(){},
        ajaxAction:function(data){
        },
        destroy:function(){
            this.remove();
        }

    });
    window.ADMIN.V.wechatSetView=wechatSet;
}())