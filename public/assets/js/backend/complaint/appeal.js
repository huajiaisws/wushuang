define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'complaint/appeal/index',
                    add_url: 'complaint/appeal/add',
                    edit_url: 'complaint/appeal/edit',
                    del_url: 'complaint/appeal/del',
                    multi_url: 'complaint/appeal/multi',
                    table: 'appeal'
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
                        {field: 'ordersn', title: __("申诉人订单编号")},
                        {field: 'ordersn2', title: __("被申诉人订单编号")},
                        {field: 'type', title: __("投诉人类型"), formatter: Table.api.formatter.status, searchList: {buy: __('买家'), sell: __('卖家')}},
                        {field: 'des', title: __("投诉理由")},
                        {field: 'image', title: __("凭证"), formatter: Table.api.formatter.image},
                        {field: 'status', title: __("status"), formatter: Table.api.formatter.status, searchList: {0: __('申诉中'), 1: __('通过申诉'), 2: __('驳回申诉'), 3: __('取消申诉')}},
                        {field: 'createtime', title: __("创建时间"), formatter: Table.api.formatter.datetime, operate: 'RANGE', addclass: 'datetimerange'},
                        {field: 'updatetime', title: __("更新时间"), formatter: Table.api.formatter.datetime, operate: 'RANGE', addclass: 'datetimerange'},
                        {field: 'operate', title:__("操作"), table: table,
                            buttons: [
                                //{name: 'detail', text:__('details'), title:__('details'), icon: 'fa fa-list', classname: 'btn btn-xs btn-primary btn-dialog', url: 'complaint/complaint/detail'}
                            ],
                            events: Table.api.events.operate, formatter: Table.api.formatter.operate}
                    ]
                ]
            });

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