webpackJsonp([21],{190:function(t,a,e){function o(t){e(371)}var i=e(14)(e(261),e(457),o,"data-v-68c46576",null);t.exports=i.exports},261:function(t,a,e){"use strict";Object.defineProperty(a,"__esModule",{value:!0});var o=e(49),i=e.n(o),n=e(28),d=e(21);a.default={data:function(){return{}},mounted:function(){var t=this;d.a.getVersion(function(a){localStorage.setItem("version",a),t.saveVersion(a)})},computed:i()({},e.i(n.b)(["uid","api","version"])),methods:i()({},e.i(n.a)(["saveVersion"]),{checkUpdate:function(){d.a.checkVersion()}})}},325:function(t,a,e){a=t.exports=e(181)(!1),a.push([t.i,'.nowrap[data-v-68c46576]{overflow:hidden;text-overflow:ellipsis;white-space:nowrap}.select-none[data-v-68c46576]{moz-user-select:-moz-none;-moz-user-select:none;-o-user-select:none;-webkit-user-select:none;-ms-user-select:none;user-select:none}.no-beforeafter[data-v-68c46576]:after,.no-beforeafter[data-v-68c46576]:before{display:none!important}.has-beforeafter[data-v-68c46576]:after,.has-beforeafter[data-v-68c46576]:before{display:block!important}.fl[data-v-68c46576]{float:left}.fr[data-v-68c46576]{float:right}.clear[data-v-68c46576]:after{content:" ";display:block;clear:both}.overflow-hidden[data-v-68c46576]{overflow:hidden!important}.border-none[data-v-68c46576]{border:none!important}.margin-top-20[data-v-68c46576]{margin-top:2.667vw}.margin-top-10[data-v-68c46576]{margin-top:1.333vw}.margin-top-0[data-v-68c46576]{margin-top:0}.margin-bottom-20[data-v-68c46576]{margin-bottom:2.667vw}.margin-bottom-10[data-v-68c46576]{margin-bottom:1.333vw}.margin-bottom-0[data-v-68c46576]{margin-bottom:0}.margin-right-20[data-v-68c46576]{margin-right:2.667vw}.margin-right-10[data-v-68c46576]{margin-right:1.333vw}.margin-right-0[data-v-68c46576]{margin-right:0}.margin-left-20[data-v-68c46576]{margin-left:2.667vw}.margin-left-10[data-v-68c46576]{margin-left:1.333vw}.margin-left-0[data-v-68c46576]{margin-left:0}.padding-top-20[data-v-68c46576]{padding-top:2.667vw}.padding-top-10[data-v-68c46576]{padding-top:1.333vw}.padding-top-0[data-v-68c46576]{padding-top:0}.padding-bottom-20[data-v-68c46576]{padding-bottom:2.667vw}.padding-bottom-10[data-v-68c46576]{padding-bottom:1.333vw}.padding-bottom-0[data-v-68c46576]{padding-bottom:0}.padding-right-20[data-v-68c46576]{padding-right:2.667vw}.padding-right-10[data-v-68c46576]{padding-right:1.333vw}.padding-right-0[data-v-68c46576]{padding-right:0}.padding-left-20[data-v-68c46576]{padding-left:2.667vw}.padding-left-10[data-v-68c46576]{padding-left:1.333vw}.padding-left-0[data-v-68c46576]{padding-left:0}.text-xl[data-v-68c46576]{font-size:4.8vw}.text-lg[data-v-68c46576]{font-size:4.267vw}.text-size-sd[data-v-68c46576]{font-size:3.733vw}.text-sm[data-v-68c46576]{font-size:3.2vw}.text-xs[data-v-68c46576]{font-size:2.667vw}.text-center[data-v-68c46576]{text-align:center}.text-bold[data-v-68c46576]{font-weight:700}.text-ano[data-v-68c46576]{color:#999}.text-color-sd[data-v-68c46576]{color:#333}.text-sd[data-v-68c46576]{font-size:3.733vw;color:#333}.text-row[data-v-68c46576]{padding-top:1.6vw;padding-bottom:1.6vw}.layout-center-margin[data-v-68c46576]{margin-left:3%;margin-right:3%}.border-radius-standard[data-v-68c46576]{border-radius:1.333vw}.readonlyAsNormal[data-v-68c46576]{opacity:1;-webkit-text-fill-color:#333}.moc-wrap[data-v-68c46576]{position:relative}.moc-wrap .vm[data-v-68c46576]{position:absolute;top:50%;transform:translateY(-50%)}.moc-wrap .hc[data-v-68c46576]{position:absolute;left:50%;transform:translateX(-50%)}.moc-wrap .mc[data-v-68c46576]{position:absolute;top:50%;left:50%;transform:translate(-50%,-50%)}.vHidden[data-v-68c46576]{visibility:hidden}.vShow[data-v-68c46576]{visibility:visible}.hidden[data-v-68c46576]{display:none!important}.show[data-v-68c46576]{display:block}.flexV[data-v-68c46576]{display:box;display:-ms-flexbox;display:-webkit-flex;display:-webkit-box;-moz-flex-direction:column;-o-flex-direction:column;display:flex;-ms-flex-pack:justify;justify-content:space-between;height:100%}.body[data-v-68c46576],.flexV[data-v-68c46576]{-ms-flex-direction:column;flex-direction:column}.body[data-v-68c46576]{background-color:#1c2d47;padding:4vw 6.667vw;display:-ms-flexbox;display:flex;-ms-flex-align:center;align-items:center}.body .icon[data-v-68c46576]{width:50%}.body .text[data-v-68c46576]{margin-top:2.667vw}.body .btn-blue[data-v-68c46576]{margin-top:13.333vw;width:100%}',""])},371:function(t,a,e){var o=e(325);"string"==typeof o&&(o=[[t.i,o,""]]),o.locals&&(t.exports=o.locals);e(182)("60f2dde0",o,!0,{})},421:function(t,a,e){t.exports=e.p+"static/images/logo.png"},457:function(t,a,e){t.exports={render:function(){var t=this,a=t.$createElement,o=t._self._c||a;return o("div",{staticClass:"page"},[o("m-header",{attrs:{title:"关于",canback:Boolean(1)}}),t._v(" "),o("section",{staticClass:"body"},[o("img",{staticClass:"icon",attrs:{src:e(421),alt:""}}),t._v(" "),o("p",{staticClass:"text"},[t._v("当前版本 v"+t._s(t.version?t.version:"1.0"))]),t._v(" "),o("button",{staticClass:"btn btn-blue",on:{click:t.checkUpdate}},[t._v("检查更新")])])],1)},staticRenderFns:[]}}});