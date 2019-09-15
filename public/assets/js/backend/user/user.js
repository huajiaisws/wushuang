define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'user/user/index',
                    add_url: 'user/user/add',
                    edit_url: 'user/user/edit',
                    del_url: 'user/user/del',
                    multi_url: 'user/user/multi',
                    table: 'user'
                }
            });

            var table = $("#table");

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'id',
                sortName: 'user.id',
                columns: [
                    {checkbox: true},
                    {field: 'id', title: __('Id'), sortable: true},
                    {field: 'username', title: __('Username'), operate: 'LIKE'},
                    {field: 'detail.realname', title: __('Realname'), operate: 'LIKE'},
                    {field: 'mobile', title: __('Mobile'), operate: 'LIKE'},
                    {field: 'level', title: __('Level'), operate: 'LIKE'},
                    {field: 'credit1', title: __('credit1'), operate: 'LIKE'},
                    {field: 'credit2', title: __('credit2'), operate: 'LIKE'},
                    {field: 'credit3', title: __('credit3'), operate: 'LIKE'},
                    {field: 'credit4', title: __('credit4'), operate: 'LIKE'},
                    {field: 'credit5', title: __('credit5'), operate: 'LIKE'},
                    {field: 'total', title: __('total'), operate: 'LIKE'},
                    {field: 'polatoon', title: __('Platoon arrangement'), operate: 'LIKE'},
                    //{field: 'createtime', title: __('Createtime'), formatter: Table.api.formatter.datetime, operate: 'RANGE', addclass: 'datetimerange', sortable: true},
                    {field: 'status', title: __('Status'), formatter: Table.api.formatter.toggle,searchList:{'normal':__('正常'),'hidden':__('封号')},yes:'normal',no:'hidden'},
                    {field: 'operate', title: __('Operate'), table: table, buttons: [
                            {name: 'addcc', text: '充值', title: '会员充值', icon: 'fa fa-battery', classname: 'btn btn-xs btn-primary btn-dialog',url: 'user/user/addcc'}
                        ], events: Table.api.events.operate, formatter: Table.api.formatter.operate}
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
        addcc:function () {
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
