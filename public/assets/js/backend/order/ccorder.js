define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'order/ccorder/index',
                    edit_url: 'order/ccorder/edit',
                    del_url: 'order/ccorder/del',
                    multi_url: 'order/ccorder/multi',
                    table: 'cc_order',
                }
            });


            var table = $("#table");

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'id',
                columns: [
                    [
                        {checkbox: true},
                        {field: 'id', title: __('Id')},
                        {field: 'tradesn', title: __('Tradesn')},
                        {field: 'uname', title: __('Uname')},
                        {field: 'unitprice', title: __('Unitprice')},
                        {field: 'number', title: __('Number')},
                        {field: 'servicecharge', title: __('Servicecharge')},
                        {field: 'user.nickname', title: __('User.name')},
                        {field: 'statestr', title: __('State')},
                        {field: 'typestr', title: __('Type')},
                        {field: 'createtime', title: __('Createtime')},
                        {field: 'tradetime', title: __('Tradetime')},
                        {field: 'status', title: __('Status'), formatter: Table.api.formatter.status, searchList: {normal: __('Normal'), hidden: __('Hidden')}},
                        {field: 'operate', title: __('Operate'), table: table,
                            events: Table.api.events.operate,
                            formatter: Table.api.formatter.operate}
                    ]
                ]
            });


            // 为表格绑定事件
            Table.api.bindevent(table);
        },

        edit: function () {
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