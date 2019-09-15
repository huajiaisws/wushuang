/**
 * Created by Administrator on 2019/3/27.
 */
define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'general//turntable/index',
                    add_url: 'general/turntable/add',
                    edit_url: 'general/turntable/edit',
                    del_url: 'general/turntable/del',
                    table: 'turntable'
                }
            });

            var table = $("#table");

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'id',
                sortName: 'id',
                escape: false,
                columns: [
                    [
                        {checkbox: true},
                        {field: 'id', title: __('Id')},
                        {field: 'title', title: __('title')},
                        {field: 'percent', title: __('percent'), align: 'left'},
                        {field: 'reward', title: __('reward')},
                        {field: 'num', title: __('num')},
                        {field: 'rewardimg', title: __('images'),operate: false, formatter: Table.api.formatter.image},
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
/**
 * Created by Administrator on 2019/3/31 0031.
 */
