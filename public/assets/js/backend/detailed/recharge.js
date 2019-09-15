/**
 * Created by Administrator on 2019/3/19.
 */
define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'detailed/recharge/index',
                   /* add_url: 'detailed/ccdetail/add',*/
                    edit_url: 'detailed/recharge/edit',
                    // del_url: 'detailed/ccdetail/del',
                    multi_url: 'detailed/recharge/multi',
                    table: 'user_recharge_log'
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
                        {field: 'user_detail.realname', title: __('User.username')},
                        {field: 'hkimg', title: __('Hkimg'), operate: false, formatter: Table.api.formatter.image},
                        {field: 'paytype', title: __("Paytype")},
                        {field: 'cointype', title: __("Cointype")},
                        {field: 'hkmoney', title: __("Hkmoney")},
                        {field: 'feemoney', title: __("Feemoney")},
                        {field: 'dzmoney', title: __("Dzmoney")},
                        {field: 'status', title: __("Status")},
                        {field: 'createtime', title: __("Create time"),formatter:Table.api.formatter.datetime, operate: 'RANGE', addclass: 'datetimerange'},
                        {field: 'operate', title:__("operate"), table: table,
                            buttons: [
                                {name: 'detail', text:__('Detail'), title:__('Detail'), icon: 'fa fa-list', classname: 'btn btn-xs btn-primary btn-dialog', url: 'detailed/recharge/detail'}
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