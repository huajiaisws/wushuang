define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'bonus/bonusdetail/index',
                    table: 'bonus',
                }
            });

            var table = $("#table");

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'id',
                columns: [
                    [
                        {field: 'periods', title: __('Periods')},
                        {field: 'user.username', title: __('Username')},
                        {field: 'money', title: __('Money')},
                        {field: 'netincome', title: __('Netincome')},
                        {field: 'f1', title: __('F1')},
                        {field: 'f2', title: __('F2')},
                        {field: 'done', title: __('Done'), formatter: Table.api.formatter.status, searchList: {0: __('Untreated'), 1: __('Processed')}},
                        {field: 'granttime', title: __('Granttime'), formatter: Table.api.formatter.datetime, operate: 'RANGE', addclass: 'datetimerange'},
                    ]
                ]
            });


            // 为表格绑定事件
            Table.api.bindevent(table);
        },

        api: {
            bindevent: function () {
                Form.api.bindevent($("form[role=form]"));
            }
        }
    };
    return Controller;
});