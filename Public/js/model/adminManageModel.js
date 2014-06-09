(function(){
//分类表中的一行
    var Data=Backbone.Model.extend({});
    var collection=Backbone.Collection.extend({
        model:Data,
        initialize:function(){
            var mode='',data;
            var self=this;
            var curmodel=new this.model();
            /*
            _.each(data,function(value){
                mode=curmodel.clone();
                mode.set(value)
                self.push(mode);
            })
*/
        }
    });
    //创建管理员管理设置数据
    window.ADMIN.M.adminManage=collection;

})()





