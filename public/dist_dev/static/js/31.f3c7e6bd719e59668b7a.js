webpackJsonp([31],{265:function(t,a,e){function o(t){e(414)}var i=e(17)(e(337),e(497),o,"data-v-bb7c2238",null);t.exports=i.exports},337:function(t,a,e){"use strict";Object.defineProperty(a,"__esModule",{value:!0});var o=e(104);a.default={data:function(){return{list:null}},created:function(){var t=this,a=this.$route.params.id,e=a||localStorage.getItem("category_id");localStorage.setItem("category_id",e),this.getAllList({category_id:e},function(a){t.list=a})},methods:{getAllList:function(t,a){o.d.getAllList(t).then(function(t){1==t.code?a(t.data):mui.toast(t.msg)})},goTo:function(t,a){this.$router.push({name:t,params:{id:a}})}}}},372:function(t,a,e){a=t.exports=e(224)(!1),a.push([t.i,'.nowrap[data-v-bb7c2238]{overflow:hidden;text-overflow:ellipsis;white-space:nowrap}.select-none[data-v-bb7c2238]{moz-user-select:-moz-none;-moz-user-select:none;-o-user-select:none;-webkit-user-select:none;-ms-user-select:none;user-select:none}.no-beforeafter[data-v-bb7c2238]:after,.no-beforeafter[data-v-bb7c2238]:before{display:none!important}.has-beforeafter[data-v-bb7c2238]:after,.has-beforeafter[data-v-bb7c2238]:before{display:block!important}.fl[data-v-bb7c2238]{float:left}.fr[data-v-bb7c2238]{float:right}.clear[data-v-bb7c2238]:after{content:" ";display:block;clear:both}.overflow-hidden[data-v-bb7c2238]{overflow:hidden!important}.border-none[data-v-bb7c2238]{border:none!important}.margin-top-20[data-v-bb7c2238]{margin-top:2.667vw}.margin-top-10[data-v-bb7c2238]{margin-top:1.333vw}.margin-top-0[data-v-bb7c2238]{margin-top:0}.margin-bottom-20[data-v-bb7c2238]{margin-bottom:2.667vw}.margin-bottom-10[data-v-bb7c2238]{margin-bottom:1.333vw}.margin-bottom-0[data-v-bb7c2238]{margin-bottom:0}.margin-right-20[data-v-bb7c2238]{margin-right:2.667vw}.margin-right-10[data-v-bb7c2238]{margin-right:1.333vw}.margin-right-0[data-v-bb7c2238]{margin-right:0}.margin-left-20[data-v-bb7c2238]{margin-left:2.667vw}.margin-left-10[data-v-bb7c2238]{margin-left:1.333vw}.margin-left-0[data-v-bb7c2238]{margin-left:0}.padding-top-20[data-v-bb7c2238]{padding-top:2.667vw}.padding-top-10[data-v-bb7c2238]{padding-top:1.333vw}.padding-top-0[data-v-bb7c2238]{padding-top:0}.padding-bottom-20[data-v-bb7c2238]{padding-bottom:2.667vw}.padding-bottom-10[data-v-bb7c2238]{padding-bottom:1.333vw}.padding-bottom-0[data-v-bb7c2238]{padding-bottom:0}.padding-right-20[data-v-bb7c2238]{padding-right:2.667vw}.padding-right-10[data-v-bb7c2238]{padding-right:1.333vw}.padding-right-0[data-v-bb7c2238]{padding-right:0}.padding-left-20[data-v-bb7c2238]{padding-left:2.667vw}.padding-left-10[data-v-bb7c2238]{padding-left:1.333vw}.padding-left-0[data-v-bb7c2238]{padding-left:0}.text-xl[data-v-bb7c2238]{font-size:4.8vw}.text-lg[data-v-bb7c2238]{font-size:4.267vw}.text-size-sd[data-v-bb7c2238]{font-size:3.733vw}.text-sm[data-v-bb7c2238]{font-size:3.2vw}.text-xs[data-v-bb7c2238]{font-size:2.667vw}.text-center[data-v-bb7c2238]{text-align:center}.text-bold[data-v-bb7c2238]{font-weight:700}.text-ano[data-v-bb7c2238]{color:#999}.text-color-sd[data-v-bb7c2238]{color:#333}.text-sd[data-v-bb7c2238]{font-size:3.733vw;color:#333}.text-row[data-v-bb7c2238]{padding-top:1.6vw;padding-bottom:1.6vw}.layout-center-margin[data-v-bb7c2238]{margin-left:3%;margin-right:3%}.border-radius-standard[data-v-bb7c2238]{border-radius:1.333vw}.readonlyAsNormal[data-v-bb7c2238]{opacity:1;-webkit-text-fill-color:#333}.moc-wrap[data-v-bb7c2238]{position:relative}.moc-wrap .vm[data-v-bb7c2238]{position:absolute;top:50%;-webkit-transform:translateY(-50%);transform:translateY(-50%)}.moc-wrap .hc[data-v-bb7c2238]{position:absolute;left:50%;-webkit-transform:translateX(-50%);transform:translateX(-50%)}.moc-wrap .mc[data-v-bb7c2238]{position:absolute;top:50%;left:50%;-webkit-transform:translate(-50%,-50%);transform:translate(-50%,-50%)}.vHidden[data-v-bb7c2238]{visibility:hidden}.vShow[data-v-bb7c2238]{visibility:visible}.hidden[data-v-bb7c2238]{display:none!important}.show[data-v-bb7c2238]{display:block}.flexV[data-v-bb7c2238]{display:box;display:-ms-flexbox;display:-webkit-flex;display:-webkit-box;-webkit-box-orient:vertical;-moz-flex-direction:column;-ms-flex-direction:column;-o-flex-direction:column;display:flex;flex-direction:column;-webkit-box-pack:justify;-ms-flex-pack:justify;justify-content:space-between;height:100%}.body[data-v-bb7c2238]{background:#171a2d;font-size:4.267vw}.body .list[data-v-bb7c2238]{display:-webkit-box;display:-ms-flexbox;display:flex;-webkit-box-pack:justify;-ms-flex-pack:justify;justify-content:space-between;padding:4vw;background:#252436;border-bottom:1px solid #171a2d;color:#fff}',""])},414:function(t,a,e){var o=e(372);"string"==typeof o&&(o=[[t.i,o,""]]),o.locals&&(t.exports=o.locals);e(225)("76711acc",o,!0)},497:function(t,a){t.exports={render:function(){var t=this,a=t.$createElement,e=t._self._c||a;return e("div",{staticClass:"page"},[e("m-header",{attrs:{title:"问题列表",canback:Boolean(1)}}),t._v(" "),e("div",{staticClass:"body"},[e("ul",t._l(t.list,function(a){return e("li",{key:a.id,staticClass:"list",on:{click:function(e){t.goTo("QuestionDetails",a.id)}}},[e("span",[t._v(t._s(a.title))]),t._v(" "),e("i",{staticClass:"iconfont iconright"})])}))])],1)},staticRenderFns:[]}}});