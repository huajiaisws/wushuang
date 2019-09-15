/**
 * Created by Administrator on 2019/3/18.
 */
define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    $('#system_init').click(function(){
        layer.confirm(__("are you sure you want to initialize")+'?',{icon:1,btn:[__('yes'),__('no')],btnAlign:'c',title:__('title')},function(index){
            //调用Ajax请求方法
            /*Fast.api.ajax({
                url: 'system/initialization/index',
                type:'POST',
                dataType: 'json'
            }, function (data,ret) {
                //成功
                alert('222');
                return false;
            },function(data, ret){
                //失败的回调
                alert('111');
                return false;
            });*/

            $.ajax({
                url: 'system/initialization/index',
                type:'POST',
                dataType: 'json',
                success: function(data){
                    //layer.alert(data.msg);
                    layer.msg(data.msg);
                },
                error:function(err){
                    console.log(err);
                }
            });

            layer.close(index);
        },function(){
            //取消
        });

    });


    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'system/initialization/index',
                    add_url: 'system/initialization/add',
                    edit_url: 'system/initialization/edit',
                    del_url: 'system/initialization/del',
                    multi_url: 'system/initialization/multi'
                }
            });
        },
        add: function () {
            Controller.api.bindevent();
        },
        edit: function () {
            Controller.api.bindevent();
        },
        detail: function (){
            Controller.api.bindevent();
        },
        test: function(){
            Controller.api.bindevent();
        },

        api: {
            bindevent: function () {
                Form.api.bindevent($("form[role=form]"));
            }
        }
    };
    return Controller;
});