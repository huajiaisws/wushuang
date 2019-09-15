/**
 * Created by Administrator on 2019/3/27.
 */
define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'user/show/index',
                    add_url: 'user/show/add',
                    edit_url: 'user/show/edit',
                    del_url: 'user/show/del',
                    multi_url: 'user/show/multi',
                    table: 'user_show'
                }
            });

            var table = $("#table");

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'id',
                sortName: 'id',
                // escape: false,
                columns: [
                    [
                        {checkbox: true},
                        {field: 'id', title: __('Id')},
                        {field: 'mid', title: __('Mid')},
                        {field: 'img', title: __('img'), operate: false, formatter: Table.api.formatter.image},
                        {field: 'status', title: __('Open/close'), formatter: Table.api.formatter.status, searchList: {1: __('open'), 0: __('close')}},
                        {field: 'operate', title:__("operate"), table: table,
                            // buttons: [
                            //     // {name: 'detail', text:__('detail'), title:__('detail'), icon: 'fa fa-list', classname: 'btn btn-xs btn-primary btn-dialog', url: 'user/level/detail'}
                            // ],
                            events: Table.api.events.operate,
                            formatter: Table.api.formatter.operate
                        }
                    ]
                ],
                pagination: true,
                search: true,
                commonSearch: true
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
        upgrade: function (){
            Controller.api.bindevent();
        },
        api: {
            bindevent: function () {
                Form.api.bindevent($("form[role=form]"));
            }
        }
    };
    return Controller;
});/**
 * Created by Administrator on 2019/3/31 0031.
 */
