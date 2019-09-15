/**
 * Created by Administrator on 2019/3/27.
 */
define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'blockore/blockore/index',
                    add_url: 'blockore/blockore/add',
                    edit_url: 'blockore/blockore/edit',
                    del_url: 'blockore/blockore/del',
                    multi_url: 'blockore/blockore/multi',
                    detail_url: 'blockore/blockore/detail',
                    table: 'blockore'
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
                        {field: 'id', title: __('id'),visible:false},
                        {field: 'orecode', title: __('orecode')},
                        {field: 'price', title: __('price')},
                        {field: 'level', title: __('level')},
                        {field: 'levelname', title: __('levelname')},
                        {field: 'ap_ordersn', title: __('归属订单编号')},
                        {field: 'ap_username', title: __('归属用户编号')},
                        {field: 'username', title: __('username')},
                        {field: 'status', title: __('Status'), formatter: Table.api.formatter.status, searchList: {1: __('open'), 0: __('close'),2:__('split')}},
                        {field: 'status2', title: __('Status2'), formatter: Table.api.formatter.status, searchList: {0: __('Reservations'), 1: __('Grabbing mine'), 2: __('Mining')}},
                        {field: 'createtime', title: __('createtime'), formatter: Table.api.formatter.datetime, operate: 'RANGE', addclass: 'datetimerange',visible:false},
                        {field: 'updatetime', title: __('updatetime'), formatter: Table.api.formatter.datetime, operate: 'RANGE', addclass: 'datetimerange',visible:false},
                        {field: 'operate', title: __('Operate'), table: table,
                            events: Table.api.events.operate,
                            buttons: [/*{
                             name: 'detail',
                             text: __('Detail'),
                             icon: 'fa fa-list',
                             classname: 'btn btn-info btn-xs btn-detail btn-dialog',
                             url: 'machine/blockore/detail'
                             }*/],
                            formatter: Table.api.formatter.operate
                        }
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
