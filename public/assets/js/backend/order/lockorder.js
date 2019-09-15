/**
 * Created by Administrator on 2019/4/3.
 */
define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'order/lockorder/index',
                    //edit_url: 'order/lockorder/edit',
                    //del_url: 'order/lockorder/del',
                    multi_url: 'order/lockorder/multi',
                    table: 'ore_order',
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
                        {field: 'id', title: __('Id'),visible:false},
                        {field: 'periods', title: __('期数'),visible:false},
                        {field: 'ordersn', title: __('订单编号')},
                        {field: 'level', title: __('level'),searchList:$.getJSON('blockore/booking/getLvName')},
                        {field: 'orecode', title: __('orecode')},
                        {field: 'pcp', title: __('本金')},
                        {field: 'total_money', title: __('总额(本金+收益)')},
                        {field: 'buy_username', title: __('买家')},
                        {field: 'sell_username', title: __('卖家'),visible:false},
                        {field: 'sell_ordersn', title: __('卖家的订单编号'),visible:true},
                        {field: 'days', title: __('合约天数')},
                        {field: 'per', title: __('合约百分比(%)'),visible:false},
                        {field: 'money', title: __('money'),visible:false},
                        {field: 'money2', title: __('money2'),visible:false},
                        {field: 'credit2', title: __('credit2')},
                        {field: 'fee', title: __('fee')},
                        //{field: 'credit4_per', title: __('可挖DOGE百分比%'),visible:false},
                        //{field: 'credit4', title: __('可挖DOGE收益')},
                        {field: 'credit5', title: __('credit5')},
                        {field: 'images', title: __('付款凭证'),formatter: Table.api.formatter.image},
                        {field: 'success_time', title: __('抢购成功时间'),formatter:Table.api.formatter.datetime, operate: 'RANGE', addclass: 'datetimerange',visible:false},
                        {field: 'pay_etime', title: __('截止付款时间'),formatter:Table.api.formatter.datetime, operate: 'RANGE', addclass: 'datetimerange',visible:false},
                        {field: 'pay_time', title: __('付款时间'),formatter:Table.api.formatter.datetime, operate: 'RANGE', addclass: 'datetimerange',visible:false},
                        {field: 'lock_stime', title: __('冻结开始时间'),formatter:Table.api.formatter.datetime, operate: 'RANGE', addclass: 'datetimerange',visible:false},
                        {field: 'lock_etime', title: __('冻结结束时间'),formatter:Table.api.formatter.datetime, operate: 'RANGE', addclass: 'datetimerange',visible:false},
                        {field: 'wc_time', title: __('交易完成时间'),formatter:Table.api.formatter.datetime, operate: 'RANGE', addclass: 'datetimerange',visible:false},
                        {field: 'due_time', title: __('到期时间'),formatter:Table.api.formatter.datetime, operate: 'RANGE', addclass: 'datetimerange',visible:false},
                        {field: 'delay_time', title: __('延期的开始时间'),formatter:Table.api.formatter.datetime, operate: 'RANGE', addclass: 'datetimerange',visible:false},
                        {field: 'delay_etime', title: __('延期的结束时间'),formatter:Table.api.formatter.datetime, operate: 'RANGE', addclass: 'datetimerange',visible:false},
                        {field: 'delay_credit5', title: __('延期收益'),visible:false},
                        {field: 'sell_time', title: __('转让完成时间'),formatter:Table.api.formatter.datetime, operate: 'RANGE', addclass: 'datetimerange',visible:false},
                        {field: 'status', title: __('订单状态'), formatter: Table.api.formatter.status, searchList: {0: __('待付款'), 1: __('待确认'), 2: __('收益中'), 3: __('收益完成')}},
                        {field: 'status2', title: __('冻结状态'), formatter: Table.api.formatter.status, searchList: {0: __('非冻结'), 1: __('冻结中')}},
                        {field: 'status3', title: __('申诉状态'), formatter: Table.api.formatter.status, searchList: {0: __('非申诉'), 1: __('申诉中')}},
                        {field: 'status4', title: __('转让状态'), formatter: Table.api.formatter.status, searchList: {0: __('未到期'), 1: __('待转让'), 2: __('待付款'), 3: __('待确认'), 4: __('交易完成')}},
                        {field: 'status5', title: __('拆分状态'),visible:false, formatter: Table.api.formatter.status, searchList: {0: __('正常'), 1: __('已拆分')}},
                        {field: 'operate', title: __('Operate'),formatter: function(value, row, index){
                            return '<a class="btn btn-info btn-xs" href="./clock?id='+row.id+'" id="clock">取消冻结</a>'+'&nbsp;&nbsp;<a class="btn btn-info btn-xs" href="./ctst?id='+row.id+'" id="ctst">强制交易</a>'+'&nbsp;&nbsp;<a class="btn btn-info btn-xs" href="./jt_play?id='+row.id+'" id="jt_play">假图处理</a>';
                        }
                        }
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