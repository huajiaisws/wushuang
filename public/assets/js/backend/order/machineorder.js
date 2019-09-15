define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'order/machineorder/index',
                    del_url: 'order/machineorder/del',
                    multi_url: 'order/machineorder/multi',
                    edit_url: 'order/machineorder/edit',
                    table: 'machine_order',
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
                        {field: 'ordersn', title: __('Ordersn')},
                        {field: 'user_detail.realname', title: __('User.username')},
                        {field: 'machine.name', title: __('Machine.name')},
                        {field: 'price', title: __('Price')},
                        {field: 'machine.image', title: __('Machine.image'), operate: false, formatter: Table.api.formatter.image},
                        {field: 'createtime', title: __('Createtime')},
                        {field: 'expiretime', title: __('Expiretime')},
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
        del: function (){
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