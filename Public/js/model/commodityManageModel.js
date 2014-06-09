(function(){
    var collection={};
    var mixData=ADMIN.global.mixData;
        mixData.cateTitle=['ID','分类名称','分类图片','分类类型','管理员操作'];
        mixData.productTitle=["",'ID','商品名称','卖出数量','分类','价格','是否上架','发布时间'];
    var addProduct=Backbone.Model.extend({});
    var brandManage=Backbone.Model.extend({});
    var add_cate=Backbone.Model.extend({});
    var product_manage=Backbone.Collection.extend({
                url:ADMIN.global.ADMINPATH+'CommodityM/productManage',//定义url同步或者获取服务器数据
                initialize:function(callback){
                this.fetch({success:callback,error:function(collection,data,options){
                }});
        },
        parse:function(data){
         return data;
        }

    });
    var cateTable=Backbone.Collection.extend({
        url:ADMIN.global.ADMINPATH+'CommodityM/',//定义url同步或者获取服务器数据
        initialize:function(callback){
            this.fetch({url:this.url+'get_Cate',success:callback,error:function(collection,data,options){
            }});
        },
        /**
         * 默认情况如果对服务器返回的数据不需要进行处理就不需要重写这个parse方法
         * 如果一但重写了这个方法一定要return结果，否则不会自动插入到集合中
         * 这个方法执行后，再执行fetch的成功回调
         * @param data
         * @returns {*}
         */
        parse:function(data){
            return data;
        }
    });

    collection.brandMangageTable=product_manage;
    collection.cateTable=cateTable;


    //创建商品管理模块数据表
    window.ADMIN.M.commodityManage=collection;

})()





