/**
 * Created by Administrator on 2019/3/19.
 */
define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'wallet/wallet/index',
                    edit_url: 'wallet/wallet/edit',
                    table: 'wallet',
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
                        {field: 'name', title: __("Name")},
                        {field: 'typename', title: __("Typename")},
                        {field: 'number', title: __("Number")},
                        {field: 'credit5', title: __("Credit5")},
                        {field: 'address', title: __("Address")},
                        {field: 'createtime', title: __("Createtime"),formatter:Table.api.formatter.datetime, operate: 'RANGE', addclass: 'datetimerange'},
                        {field: 'modifytime', title: __("Modifytime"),formatter:Table.api.formatter.datetime, operate: 'RANGE', addclass: 'datetimerange'},
                        {field: 'statestr', title: __("State")},
                        {field: 'operate', title: __('Operate'), table: table,
                            events: Table.api.events.operate,
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
        api: {
            bindevent: function () {
                Form.api.bindevent($("form[role=form]"));
            }
        }
    };
    return Controller;
});