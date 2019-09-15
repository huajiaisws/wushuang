/**
 * Created by Administrator on 2019/3/19.
 */
define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'system/notice/index',
                    add_url: 'system/notice/add',
                    edit_url: 'system/notice/edit',
                    del_url: 'system/notice/del',
                    multi_url: 'system/notice/multi',
                    table: 'notice'
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
                        {field: 'title', title: __("title")},
                        {field: 'type_text', title: __("Announcement type")},
                        //{field: 'contents', title: __("contents")},
                        {field: 'createtime', title: __("createtime"),formatter: Table.api.formatter.datetime, operate: 'RANGE', addclass: 'datetimerange'},
                        {field: 'updatetime', title: __("updatetime"),formatter: Table.api.formatter.datetime, operate: 'RANGE', addclass: 'datetimerange'},
                        {field: 'operate', title:__("operate"), table: table,
                            buttons: [
                                {name: 'detail', text:__('detail'), title:__('detail'), icon: 'fa fa-list', classname: 'btn btn-xs btn-primary btn-dialog', url: 'system/notice/detail'}
                            ],
                            events: Table.api.events.operate, formatter: Table.api.formatter.operate}
                    ]
                ]
            });
            console.log(Fast.api.query('tradesn'));
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