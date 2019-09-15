define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'order/renew/index',
                    edit_url: 'order/renew/edit',
                    del_url: 'order/renew/del',
                    multi_url: 'order/renew/multi',
                    table: 'renew',
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
                        {field: 'ordersn', title: __('订单编号')},
                        {field: 'username', title: __('用户编号')},
                        {field: 'createtime', title: __('创建时间'),formatter:Table.api.formatter.datetime, operate: 'RANGE', addclass: 'datetimerange'},
                        {field: 'updatetime', title: __('更新时间'),formatter:Table.api.formatter.datetime, operate: 'RANGE', addclass: 'datetimerange'},
                        {field: 'status', title: __('状态'), formatter: Table.api.formatter.status, searchList: {0: __('申请中'), 1: __('同意'),2:__('拒绝')}},
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