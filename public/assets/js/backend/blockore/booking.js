/**
 * Created by Administrator on 2019/3/27.
 */
define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'blockore/booking/index',
                    //add_url: 'blockore/blockore/add',
                    //edit_url: 'blockore/blockore/edit',
                    //del_url: 'blockore/blockore/del',
                    //multi_url: 'blockore/blockore/multi',
                    //detail_url: 'blockore/blockore/detail',
                    table: 'booking_log'
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
                        {field: 'id', title: __('id'),visible:false},
                        {field: 'periods', title: __('periods')},
                        {field: 'username', title: __('username')},
                        {field: 'level', title: __('level'),visible:false,searchList:$.getJSON('blockore/booking/getLvName')},
                        {field: 'levelname', title: __('levelname'),operate:false},
                        {field: 'credit1', title: __('credit1'),operate:false},
                        {field: 'status', title: __('Status'), formatter: Table.api.formatter.status, searchList: {0: __('预约成功'), 1: __('已退还'),2:__('抢购成功')}},
                        {field: 'createtime', title: __('createtime'), formatter: Table.api.formatter.datetime, operate: 'RANGE', addclass: 'datetimerange'},
                       /* {field: 'operate', title: __('Operate'), table: table,
                            events: Table.api.events.operate,
                            buttons: [*//*{
                             name: 'detail',
                             text: __('Detail'),
                             icon: 'fa fa-list',
                             classname: 'btn btn-info btn-xs btn-detail btn-dialog',
                             url: 'machine/blockore/detail'
                             }*//*],
                            formatter: Table.api.formatter.operate
                        }*/
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
