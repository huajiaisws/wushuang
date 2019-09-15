define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'complaint/complaint/index',
                    add_url: 'complaint/complaint/add',
                    edit_url: 'complaint/complaint/edit',
                    del_url: 'complaint/complaint/del',
                    multi_url: 'complaint/complaint/multi',
                    table: 'complaint'
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
                        {field: 'respondent', title: __("respondent")},
                        {field: 'complainant', title: __("complainant")},
                        {field: 'tradesn', title: __("tradesn"),formatter: function(value, row, index){return '<a href="../order/ccorder/index?tradesn='+value+'">'+value+'</a>';}},
                        {field: 'contents', title: __("contents")},
                        {field: 'images', title: __("images"), formatter: Table.api.formatter.image},
                        {field: 'createtime', title: __("createtime")},
                        {field: 'status', title: __("status"), formatter: Table.api.formatter.status, searchList: {0: __('untreated'), 1: __('processed')}},
                        {field: 'operate', title:__("operation"), table: table,
                            buttons: [
                                {name: 'detail', text:__('details'), title:__('details'), icon: 'fa fa-list', classname: 'btn btn-xs btn-primary btn-dialog', url: 'complaint/complaint/detail'}
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