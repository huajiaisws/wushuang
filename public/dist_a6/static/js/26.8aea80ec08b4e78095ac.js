webpackJsonp([26],{215:function(t,a,e){function i(t){e(367)}var o=e(14)(e(286),e(453),i,"data-v-59be9790",null);t.exports=o.exports},286:function(t,a,e){"use strict";Object.defineProperty(a,"__esModule",{value:!0});var i=e(48);a.default={data:function(){return{noticeList:[],type:"mh"}},mounted:function(){this.initData();var t={type:this.type,page:1};this.getNotice(t)},methods:{initData:function(){this.type=this.$route.query.type},getNotice:function(t){var a=this;i.c.getTitle({type:"mh"}).then(function(t){1==t.code?a.noticeList=t.data:mui.toast(t.msg)})},goto:function(t){this.$router.push({name:"NoticeDetail",params:{id:t}})}}}},321:function(t,a,e){a=t.exports=e(181)(!1),a.push([t.i,'.nowrap[data-v-59be9790]{overflow:hidden;text-overflow:ellipsis;white-space:nowrap}.select-none[data-v-59be9790]{moz-user-select:-moz-none;-moz-user-select:none;-o-user-select:none;-webkit-user-select:none;-ms-user-select:none;user-select:none}.no-beforeafter[data-v-59be9790]:after,.no-beforeafter[data-v-59be9790]:before{display:none!important}.has-beforeafter[data-v-59be9790]:after,.has-beforeafter[data-v-59be9790]:before{display:block!important}.fl[data-v-59be9790]{float:left}.fr[data-v-59be9790]{float:right}.clear[data-v-59be9790]:after{content:" ";display:block;clear:both}.overflow-hidden[data-v-59be9790]{overflow:hidden!important}.border-none[data-v-59be9790]{border:none!important}.margin-top-20[data-v-59be9790]{margin-top:2.667vw}.margin-top-10[data-v-59be9790]{margin-top:1.333vw}.margin-top-0[data-v-59be9790]{margin-top:0}.margin-bottom-20[data-v-59be9790]{margin-bottom:2.667vw}.margin-bottom-10[data-v-59be9790]{margin-bottom:1.333vw}.margin-bottom-0[data-v-59be9790]{margin-bottom:0}.margin-right-20[data-v-59be9790]{margin-right:2.667vw}.margin-right-10[data-v-59be9790]{margin-right:1.333vw}.margin-right-0[data-v-59be9790]{margin-right:0}.margin-left-20[data-v-59be9790]{margin-left:2.667vw}.margin-left-10[data-v-59be9790]{margin-left:1.333vw}.margin-left-0[data-v-59be9790]{margin-left:0}.padding-top-20[data-v-59be9790]{padding-top:2.667vw}.padding-top-10[data-v-59be9790]{padding-top:1.333vw}.padding-top-0[data-v-59be9790]{padding-top:0}.padding-bottom-20[data-v-59be9790]{padding-bottom:2.667vw}.padding-bottom-10[data-v-59be9790]{padding-bottom:1.333vw}.padding-bottom-0[data-v-59be9790]{padding-bottom:0}.padding-right-20[data-v-59be9790]{padding-right:2.667vw}.padding-right-10[data-v-59be9790]{padding-right:1.333vw}.padding-right-0[data-v-59be9790]{padding-right:0}.padding-left-20[data-v-59be9790]{padding-left:2.667vw}.padding-left-10[data-v-59be9790]{padding-left:1.333vw}.padding-left-0[data-v-59be9790]{padding-left:0}.text-xl[data-v-59be9790]{font-size:4.8vw}.text-lg[data-v-59be9790]{font-size:4.267vw}.text-size-sd[data-v-59be9790]{font-size:3.733vw}.text-sm[data-v-59be9790]{font-size:3.2vw}.text-xs[data-v-59be9790]{font-size:2.667vw}.text-center[data-v-59be9790]{text-align:center}.text-bold[data-v-59be9790]{font-weight:700}.text-ano[data-v-59be9790]{color:#999}.text-color-sd[data-v-59be9790]{color:#333}.text-sd[data-v-59be9790]{font-size:3.733vw;color:#333}.text-row[data-v-59be9790]{padding-top:1.6vw;padding-bottom:1.6vw}.layout-center-margin[data-v-59be9790]{margin-left:3%;margin-right:3%}.border-radius-standard[data-v-59be9790]{border-radius:1.333vw}.readonlyAsNormal[data-v-59be9790]{opacity:1;-webkit-text-fill-color:#333}.moc-wrap[data-v-59be9790]{position:relative}.moc-wrap .vm[data-v-59be9790]{position:absolute;top:50%;transform:translateY(-50%)}.moc-wrap .hc[data-v-59be9790]{position:absolute;left:50%;transform:translateX(-50%)}.moc-wrap .mc[data-v-59be9790]{position:absolute;top:50%;left:50%;transform:translate(-50%,-50%)}.vHidden[data-v-59be9790]{visibility:hidden}.vShow[data-v-59be9790]{visibility:visible}.hidden[data-v-59be9790]{display:none!important}.show[data-v-59be9790]{display:block}.flexV[data-v-59be9790]{display:box;display:-ms-flexbox;display:-webkit-flex;display:-webkit-box;-moz-flex-direction:column;-ms-flex-direction:column;-o-flex-direction:column;display:flex;flex-direction:column;-ms-flex-pack:justify;justify-content:space-between;height:100%}.body[data-v-59be9790]{background:#1c2d47;padding:4vw;height:100%}.body .list .item[data-v-59be9790]{padding:1.6vw 0;border-bottom:1px solid #fff;color:#fff}',""])},367:function(t,a,e){var i=e(321);"string"==typeof i&&(i=[[t.i,i,""]]),i.locals&&(t.exports=i.locals);e(182)("c4d5f53e",i,!0,{})},453:function(t,a){t.exports={render:function(){var t=this,a=t.$createElement,e=t._self._c||a;return e("div",{staticClass:"page"},[e("m-header",{attrs:{title:"公告信息",canback:Boolean(1)}}),t._v(" "),e("section",{staticClass:"body"},[e("ul",{staticClass:"list"},t._l(t.noticeList,function(a){return e("li",{key:a.id,staticClass:"item",on:{click:function(e){return t.goto(a.id)}}},[t._v(t._s(a.title))])}),0)])],1)},staticRenderFns:[]}}});