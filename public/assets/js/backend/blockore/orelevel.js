/**
 * Created by Administrator on 2019/3/27.
 */
define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'blockore/orelevel/index',
                    add_url: 'blockore/orelevel/add',
                    edit_url: 'blockore/orelevel/edit',
                    del_url: 'blockore/orelevel/del',
                    multi_url: 'blockore/orelevel/multi',
                    detail_url: 'blockore/orelevel/detail',
                    table: 'orelevel'
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
                        {field: 'level', title: __('level')},
                        {field: 'levelname', title: __('level name')},
                        {field: 'images', title: __('Images'), operate: false, formatter: Table.api.formatter.image},
                        {field: 'min_price', title: __('price'),formatter:function(value,obj,index){
                            return value+'~'+obj.max_price;
                        }},
                        //{field: 'max_price', title: __('Maximum price')},
                        {field: 'stime', title: __('Mining time'),formatter:function(value,obj,index){
                            return obj.stime_text+'-'+obj.etime_text;
                        }},
                        //{field: 'etime', title: __('end time')},
                        {field: 'money', title: __('Reservation of required miners')},
                        {field: 'money2', title: __('Not reservation of required miners')},
                        {field: 'days', title: __('Income days')},
                        {field: 'per', title: __('Percentage of Income')},
                        {field: 'credit2', title: __('Digable block Mine')},
                        {field: 'credit4', title: __('Digable block DOGE')},
                        {field: 'status', title: __('Status'), formatter: Table.api.formatter.status, searchList: {1: __('open'), 0: __('close')}},
                        {field: 'operate', title: __('Operate'), table: table,
                            events: Table.api.events.operate,
                            buttons: [/*{
                                name: 'detail',
                                text: __('Detail'),
                                icon: 'fa fa-list',
                                classname: 'btn btn-info btn-xs btn-detail btn-dialog',
                                url: 'machine/blockore/detail'
                            }*/],
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
