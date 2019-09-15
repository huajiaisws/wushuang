/**
 * Created by Administrator on 2019/3/27.
 */
define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'user/level/index',
                    add_url: 'user/level/add',
                    edit_url: 'user/level/edit',
                    multi_url: 'user/level/multi',
                    table: 'user_level'
                }
            });

            var table = $("#table");

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'id',
                sortName: 'level',
                // escape: false,
                columns: [
                    [
                        {checkbox: true},
                        {field: 'id', title: __('Id')},
                        {field: 'level', title: __('Level')},
                        {field: 'levelname', title: __('Level name'), align: 'left'},
                        {field: 'enabled', title: __('Open/close'), formatter: Table.api.formatter.status, searchList: {1: __('open'), 0: __('close')}},
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
        add: function (){
            Controller.api.bindevent();
        },
        edit: function (){
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