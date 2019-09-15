/**
 * Created by admin on 2019/4/12.
 */
define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'order/sell/index',
                    edit_url: 'order/sell/edit',
                    del_url: 'order/sell/del',
                    multi_url: 'order/sell/multi',
                    table: 'sell',
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
                        {field: 'periods', title: __('期数')},
                        {field: 'username', title: __('用户编号')},
                        {field: 'num', title: __('出售数量')},
                        {field: 'level', title: __('等级'),searchList:$.getJSON('blockore/booking/getLvName')},
                        {field: 'createtime', title: __('创建时间'),formatter:Table.api.formatter.datetime, operate: 'RANGE', addclass: 'datetimerange'},
                        {field: 'audit_time', title: __('审核时间'),formatter:Table.api.formatter.datetime, operate: 'RANGE', addclass: 'datetimerange'},
                        {field: 'status', title: __('状态'), formatter: Table.api.formatter.status, searchList: {0: __('审核中'), 1: __('同意'),2:__('拒绝')}},
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