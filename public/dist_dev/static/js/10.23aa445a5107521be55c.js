webpackJsonp([10],{252:function(t,a,e){function d(t){e(420)}var o=e(17)(e(324),e(503),d,"data-v-f3de394c",null);t.exports=o.exports},266:function(t,a,e){t.exports={default:e(267),__esModule:!0}},267:function(t,a,e){e(269),t.exports=e(0).Object.assign},268:function(t,a,e){"use strict";var d=e(14),o=e(57),i=e(23),n=e(36),r=e(106),f=Object.assign;t.exports=!f||e(18)(function(){var t={},a={},e=Symbol(),d="abcdefghijklmnopqrst";return t[e]=7,d.split("").forEach(function(t){a[t]=t}),7!=f({},t)[e]||Object.keys(f({},a)).join("")!=d})?function(t,a){for(var e=n(t),f=arguments.length,c=1,s=o.f,l=i.f;f>c;)for(var v,p=r(arguments[c++]),m=s?d(p).concat(s(p)):d(p),g=m.length,u=0;g>u;)l.call(p,v=m[u++])&&(e[v]=p[v]);return e}:f},269:function(t,a,e){var d=e(10);d(d.S+d.F,"Object",{assign:e(268)})},270:function(t,a,e){"use strict";a.__esModule=!0;var d=e(266),o=function(t){return t&&t.__esModule?t:{default:t}}(d);a.default=o.default||function(t){for(var a=1;a<arguments.length;a++){var e=arguments[a];for(var d in e)Object.prototype.hasOwnProperty.call(e,d)&&(t[d]=e[d])}return t}},271:function(t,a,e){t.exports=e.p+"static/images/upload.png"},324:function(t,a,e){"use strict";Object.defineProperty(a,"__esModule",{value:!0});var d=e(266),o=e.n(d),i=e(270),n=e.n(i),r=e(105),f=e(104),c=e(107);a.default={name:"shaitu",data:function(){return{upladSrc:e(271),formData:{id:this.uid,type:1,img:null}}},computed:n()({},e.i(c.a)(["api","uid"])),methods:{change:function(t){var a=this;r.a.uploadByUrl(t.target.files[0]).then(function(t){var e=[a.api+t,t];a.upladSrc=e[0],a.formData.img=e[1]})},shaitu:function(){var t=this,a=o()({},this.formData,{id:this.uid}),e=r.a.isValidate(a);if(e)return void mui.toast(e);f.c.shaitu(a).then(function(a){if(1!=a.code)return void mui.toast(a.msg);mui.toast(a.msg),setTimeout(function(){t.$router.go(-1)},1e3)})}}}},378:function(t,a,e){a=t.exports=e(224)(!1),a.push([t.i,'.nowrap[data-v-f3de394c]{overflow:hidden;text-overflow:ellipsis;white-space:nowrap}.select-none[data-v-f3de394c]{moz-user-select:-moz-none;-moz-user-select:none;-o-user-select:none;-webkit-user-select:none;-ms-user-select:none;user-select:none}.no-beforeafter[data-v-f3de394c]:after,.no-beforeafter[data-v-f3de394c]:before{display:none!important}.has-beforeafter[data-v-f3de394c]:after,.has-beforeafter[data-v-f3de394c]:before{display:block!important}.fl[data-v-f3de394c]{float:left}.fr[data-v-f3de394c]{float:right}.clear[data-v-f3de394c]:after{content:" ";display:block;clear:both}.overflow-hidden[data-v-f3de394c]{overflow:hidden!important}.border-none[data-v-f3de394c]{border:none!important}.margin-top-20[data-v-f3de394c]{margin-top:2.667vw}.margin-top-10[data-v-f3de394c]{margin-top:1.333vw}.margin-top-0[data-v-f3de394c]{margin-top:0}.margin-bottom-20[data-v-f3de394c]{margin-bottom:2.667vw}.margin-bottom-10[data-v-f3de394c]{margin-bottom:1.333vw}.margin-bottom-0[data-v-f3de394c]{margin-bottom:0}.margin-right-20[data-v-f3de394c]{margin-right:2.667vw}.margin-right-10[data-v-f3de394c]{margin-right:1.333vw}.margin-right-0[data-v-f3de394c]{margin-right:0}.margin-left-20[data-v-f3de394c]{margin-left:2.667vw}.margin-left-10[data-v-f3de394c]{margin-left:1.333vw}.margin-left-0[data-v-f3de394c]{margin-left:0}.padding-top-20[data-v-f3de394c]{padding-top:2.667vw}.padding-top-10[data-v-f3de394c]{padding-top:1.333vw}.padding-top-0[data-v-f3de394c]{padding-top:0}.padding-bottom-20[data-v-f3de394c]{padding-bottom:2.667vw}.padding-bottom-10[data-v-f3de394c]{padding-bottom:1.333vw}.padding-bottom-0[data-v-f3de394c]{padding-bottom:0}.padding-right-20[data-v-f3de394c]{padding-right:2.667vw}.padding-right-10[data-v-f3de394c]{padding-right:1.333vw}.padding-right-0[data-v-f3de394c]{padding-right:0}.padding-left-20[data-v-f3de394c]{padding-left:2.667vw}.padding-left-10[data-v-f3de394c]{padding-left:1.333vw}.padding-left-0[data-v-f3de394c]{padding-left:0}.text-xl[data-v-f3de394c]{font-size:4.8vw}.text-lg[data-v-f3de394c]{font-size:4.267vw}.text-size-sd[data-v-f3de394c]{font-size:3.733vw}.text-sm[data-v-f3de394c]{font-size:3.2vw}.text-xs[data-v-f3de394c]{font-size:2.667vw}.text-center[data-v-f3de394c]{text-align:center}.text-bold[data-v-f3de394c]{font-weight:700}.text-ano[data-v-f3de394c]{color:#999}.text-color-sd[data-v-f3de394c]{color:#333}.text-sd[data-v-f3de394c]{font-size:3.733vw;color:#333}.text-row[data-v-f3de394c]{padding-top:1.6vw;padding-bottom:1.6vw}.layout-center-margin[data-v-f3de394c]{margin-left:3%;margin-right:3%}.border-radius-standard[data-v-f3de394c]{border-radius:1.333vw}.readonlyAsNormal[data-v-f3de394c]{opacity:1;-webkit-text-fill-color:#333}.moc-wrap[data-v-f3de394c]{position:relative}.moc-wrap .vm[data-v-f3de394c]{position:absolute;top:50%;-webkit-transform:translateY(-50%);transform:translateY(-50%)}.moc-wrap .hc[data-v-f3de394c]{position:absolute;left:50%;-webkit-transform:translateX(-50%);transform:translateX(-50%)}.moc-wrap .mc[data-v-f3de394c]{position:absolute;top:50%;left:50%;-webkit-transform:translate(-50%,-50%);transform:translate(-50%,-50%)}.vHidden[data-v-f3de394c]{visibility:hidden}.vShow[data-v-f3de394c]{visibility:visible}.hidden[data-v-f3de394c]{display:none!important}.show[data-v-f3de394c]{display:block}.flexV[data-v-f3de394c]{display:box;display:-ms-flexbox;display:-webkit-flex;display:-webkit-box;-webkit-box-orient:vertical;-moz-flex-direction:column;-ms-flex-direction:column;-o-flex-direction:column;display:flex;flex-direction:column;-webkit-box-pack:justify;-ms-flex-pack:justify;justify-content:space-between;height:100%}.body[data-v-f3de394c]{background-color:#171a2d;color:#fff}.body .tj-form .file[data-v-f3de394c],.body .tj-form .img[data-v-f3de394c]{width:100%;height:59.733vw}.body .tj-form .btn-submit[data-v-f3de394c]{margin-top:10%}',""])},420:function(t,a,e){var d=e(378);"string"==typeof d&&(d=[[t.i,d,""]]),d.locals&&(t.exports=d.locals);e(225)("3eb0a2c9",d,!0)},503:function(t,a){t.exports={render:function(){var t=this,a=t.$createElement,e=t._self._c||a;return e("div",{staticClass:"page"},[e("m-header",{attrs:{title:"晒图奖励",canback:Boolean(1)}}),t._v(" "),e("section",{staticClass:"body"},[e("form",{staticClass:"tj-form"},[e("div",{staticClass:"form-item"},[e("label",{staticClass:"mar",attrs:{for:"upload"}},[t._v("请上传截图")]),t._v(" "),e("input",{staticClass:"file",attrs:{type:"file"},on:{change:function(a){t.change(a)}}}),t._v(" "),e("img",{staticClass:"img",attrs:{src:t.upladSrc,alt:""}})]),t._v(" "),e("button",{staticClass:"btn-submit btn-origin",attrs:{type:"button"},on:{click:t.shaitu}},[t._v("确定")])])])],1)},staticRenderFns:[]}}});