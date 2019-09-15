/**
 * Created by Administrator on 2019/3/21.
 */
/**
 * Created by Administrator on 2019/3/19.
 */
define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'system/user/tjnet',
                    add_url: 'system/notice/add',
                    edit_url: 'system/notice/edit',
                    del_url: 'system/notice/del',
                    multi_url: 'system/notice/multi',
                    table: 'user'
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
        api: {
            bindevent: function () {
                Form.api.bindevent($("form[role=form]"));
            }
        }
    };
    return Controller;
});