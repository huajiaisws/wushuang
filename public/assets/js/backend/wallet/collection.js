/**
 * Created by Administrator on 2019/3/19.
 */
define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'wallet/collection/index',
                    add_url:'wallet/collection/add',
                    table: 'collection_log'
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
                        {field: 'id', title: 'ID'},
                        {field: 'addr', title: __("Addr")},
                        {field: 'hash', title: __("Hash")},
                        {field: 'createtime', title: __("Time"),formatter:Table.api.formatter.datetime},

                    ]
                ]
            });
            // 为表格绑定事件
            Table.api.bindevent(table);
        },
        add: function () {
            Controller.api.bindevent();
        },
        api: {
            bindevent: function () {
                $(document).on('click','#toolbar',function () {
                    console.log(this)
                    alert(1)
                    return false;
                })
                Form.api.bindevent($("form[role=form]"));
            }
        }
    };
    return Controller;
});