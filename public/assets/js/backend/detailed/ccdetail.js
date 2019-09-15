/**
 * Created by Administrator on 2019/3/19.
 */
define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'detailed/ccdetail/index',
                   /* add_url: 'detailed/ccdetail/add',
                    edit_url: 'detailed/ccdetail/edit',
                    del_url: 'detailed/ccdetail/del',*/
                    multi_url: 'detailed/ccdetail/multi',
                    table: 'cc_detail_log'
                }
            });

            var table = $("#table");

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'id',
                sortName: 'id',
                columns: [
                    [
                        {checkbox: true},
                        {field: 'id', title: 'ID', operate: 'LIKE'},
                        {field: 'username', title: __("username")},
                        {field: 'type', title: __("type")},
                        {field: 'num', title: __("num")},
                        {field: 'remark', title: __("remark")},
                        {field: 'createtime', title: __("Create time"),formatter:Table.api.formatter.datetime, operate: 'RANGE', addclass: 'datetimerange'},
                        {field: 'operate', title:__("operate"), table: table,
                            buttons: [
                                {name: 'detail', text:__('Detail'), title:__('Detail'), icon: 'fa fa-list', classname: 'btn btn-xs btn-primary btn-dialog', url: 'detailed/ccdetail/detail'}
                            ],
                            events: Table.api.events.operate, formatter: Table.api.formatter.operate}
                    ]
                ]
            });
            console.log(Fast.api.query('tradesn'));
            // 为表格绑定事件
            Table.api.bindevent(table);
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