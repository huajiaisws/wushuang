webpackJsonp([21],{231:function(a,t,e){function o(a){e(411)}var c=e(17)(e(303),e(494),o,"data-v-7c6c87ca",null);a.exports=c.exports},266:function(a,t,e){a.exports={default:e(267),__esModule:!0}},267:function(a,t,e){e(269),a.exports=e(0).Object.assign},268:function(a,t,e){"use strict";var o=e(14),c=e(57),n=e(23),i=e(36),r=e(106),s=Object.assign;a.exports=!s||e(18)(function(){var a={},t={},e=Symbol(),o="abcdefghijklmnopqrst";return a[e]=7,o.split("").forEach(function(a){t[a]=a}),7!=s({},a)[e]||Object.keys(s({},t)).join("")!=o})?function(a,t){for(var e=i(a),s=arguments.length,d=1,l=c.f,p=n.f;s>d;)for(var m,v=r(arguments[d++]),f=l?o(v).concat(l(v)):o(v),u=f.length,g=0;u>g;)p.call(v,m=f[g++])&&(e[m]=v[m]);return e}:s},269:function(a,t,e){var o=e(10);o(o.S+o.F,"Object",{assign:e(268)})},270:function(a,t,e){"use strict";t.__esModule=!0;var o=e(266),c=function(a){return a&&a.__esModule?a:{default:a}}(o);t.default=c.default||function(a){for(var t=1;t<arguments.length;t++){var e=arguments[t];for(var o in e)Object.prototype.hasOwnProperty.call(e,o)&&(a[o]=e[o])}return a}},303:function(a,t,e){"use strict";Object.defineProperty(t,"__esModule",{value:!0});var o=e(270),c=e.n(o),n=e(104),i=e(107),r=e(105);t.default={components:{},data:function(){return{seconds:0,type:null,formData:{mobile:null,newpassword:null,password:null,captcha:null}}},created:function(){this.initData()},mounted:function(){},computed:c()({},e.i(i.a)(["uid","userInfo"])),methods:{initData:function(){this.type=this.$route.query.type,this.formData.mobile=this.userInfo.mobile},getCode:function(){var a=this;this.seconds=60;var t=setInterval(function(){a.$nextTick(function(){a.seconds=a.seconds-1,a.seconds<=0&&clearInterval(t)})},1e3);n.a.sendCode({mobile:this.userInfo.mobile}).then(function(a){mui.toast(a.msg)})},confirm:function(){var a=this;if("login"==this.type){var t=r.a.isValidate(this.formData);if(t)return void mui.toast(t);n.b.backpwd(this.formData).then(function(t){if(1!=t.code)return void mui.toast(t.msg);mui.toast(t.msg),setTimeout(function(){a.$router.go(-1)},1e3)})}else{var e={mobile:this.formData.mobile,paypwd:this.formData.newpassword,paypwd_b:this.formData.password,id:this.uid,captcha:this.formData.captcha},o=r.a.isValidate(e);if(o)return void mui.toast(o);n.b.backTrade(e).then(function(t){if(1!=t.code)return void mui.toast(t.msg);mui.toast(t.msg),setTimeout(function(){a.$router.go(-1)},1e3)})}},setTitle:function(a){switch(a){case"login":return"修改登录密码";case"trade":return"修改交易密码"}}}}},369:function(a,t,e){t=a.exports=e(224)(!1),t.push([a.i,'.nowrap[data-v-7c6c87ca]{overflow:hidden;text-overflow:ellipsis;white-space:nowrap}.select-none[data-v-7c6c87ca]{moz-user-select:-moz-none;-moz-user-select:none;-o-user-select:none;-webkit-user-select:none;-ms-user-select:none;user-select:none}.no-beforeafter[data-v-7c6c87ca]:after,.no-beforeafter[data-v-7c6c87ca]:before{display:none!important}.has-beforeafter[data-v-7c6c87ca]:after,.has-beforeafter[data-v-7c6c87ca]:before{display:block!important}.fl[data-v-7c6c87ca]{float:left}.fr[data-v-7c6c87ca]{float:right}.clear[data-v-7c6c87ca]:after{content:" ";display:block;clear:both}.overflow-hidden[data-v-7c6c87ca]{overflow:hidden!important}.border-none[data-v-7c6c87ca]{border:none!important}.margin-top-20[data-v-7c6c87ca]{margin-top:2.667vw}.margin-top-10[data-v-7c6c87ca]{margin-top:1.333vw}.margin-top-0[data-v-7c6c87ca]{margin-top:0}.margin-bottom-20[data-v-7c6c87ca]{margin-bottom:2.667vw}.margin-bottom-10[data-v-7c6c87ca]{margin-bottom:1.333vw}.margin-bottom-0[data-v-7c6c87ca]{margin-bottom:0}.margin-right-20[data-v-7c6c87ca]{margin-right:2.667vw}.margin-right-10[data-v-7c6c87ca]{margin-right:1.333vw}.margin-right-0[data-v-7c6c87ca]{margin-right:0}.margin-left-20[data-v-7c6c87ca]{margin-left:2.667vw}.margin-left-10[data-v-7c6c87ca]{margin-left:1.333vw}.margin-left-0[data-v-7c6c87ca]{margin-left:0}.padding-top-20[data-v-7c6c87ca]{padding-top:2.667vw}.padding-top-10[data-v-7c6c87ca]{padding-top:1.333vw}.padding-top-0[data-v-7c6c87ca]{padding-top:0}.padding-bottom-20[data-v-7c6c87ca]{padding-bottom:2.667vw}.padding-bottom-10[data-v-7c6c87ca]{padding-bottom:1.333vw}.padding-bottom-0[data-v-7c6c87ca]{padding-bottom:0}.padding-right-20[data-v-7c6c87ca]{padding-right:2.667vw}.padding-right-10[data-v-7c6c87ca]{padding-right:1.333vw}.padding-right-0[data-v-7c6c87ca]{padding-right:0}.padding-left-20[data-v-7c6c87ca]{padding-left:2.667vw}.padding-left-10[data-v-7c6c87ca]{padding-left:1.333vw}.padding-left-0[data-v-7c6c87ca]{padding-left:0}.text-xl[data-v-7c6c87ca]{font-size:4.8vw}.text-lg[data-v-7c6c87ca]{font-size:4.267vw}.text-size-sd[data-v-7c6c87ca]{font-size:3.733vw}.text-sm[data-v-7c6c87ca]{font-size:3.2vw}.text-xs[data-v-7c6c87ca]{font-size:2.667vw}.text-center[data-v-7c6c87ca]{text-align:center}.text-bold[data-v-7c6c87ca]{font-weight:700}.text-ano[data-v-7c6c87ca]{color:#999}.text-color-sd[data-v-7c6c87ca]{color:#333}.text-sd[data-v-7c6c87ca]{font-size:3.733vw;color:#333}.text-row[data-v-7c6c87ca]{padding-top:1.6vw;padding-bottom:1.6vw}.layout-center-margin[data-v-7c6c87ca]{margin-left:3%;margin-right:3%}.border-radius-standard[data-v-7c6c87ca]{border-radius:1.333vw}.readonlyAsNormal[data-v-7c6c87ca]{opacity:1;-webkit-text-fill-color:#333}.moc-wrap[data-v-7c6c87ca]{position:relative}.moc-wrap .vm[data-v-7c6c87ca]{position:absolute;top:50%;-webkit-transform:translateY(-50%);transform:translateY(-50%)}.moc-wrap .hc[data-v-7c6c87ca]{position:absolute;left:50%;-webkit-transform:translateX(-50%);transform:translateX(-50%)}.moc-wrap .mc[data-v-7c6c87ca]{position:absolute;top:50%;left:50%;-webkit-transform:translate(-50%,-50%);transform:translate(-50%,-50%)}.vHidden[data-v-7c6c87ca]{visibility:hidden}.vShow[data-v-7c6c87ca]{visibility:visible}.hidden[data-v-7c6c87ca]{display:none!important}.show[data-v-7c6c87ca]{display:block}.flexV[data-v-7c6c87ca]{display:box;display:-ms-flexbox;display:-webkit-flex;display:-webkit-box;-webkit-box-orient:vertical;-moz-flex-direction:column;-ms-flex-direction:column;-o-flex-direction:column;display:flex;flex-direction:column;-webkit-box-pack:justify;-ms-flex-pack:justify;justify-content:space-between;height:100%}.body[data-v-7c6c87ca]{background-color:#171a2d}',""])},411:function(a,t,e){var o=e(369);"string"==typeof o&&(o=[[a.i,o,""]]),o.locals&&(a.exports=o.locals);e(225)("de58af70",o,!0)},494:function(a,t){a.exports={render:function(){var a=this,t=a.$createElement,e=a._self._c||t;return e("div",{staticClass:"page"},[e("m-header",{attrs:{title:a.setTitle(a.type),canback:Boolean(1)}}),a._v(" "),e("section",{staticClass:"body"},[e("form",{staticClass:"tj-form"},[e("div",{staticClass:"form-item"},[e("label",{attrs:{for:"name"}},[a._v(a._s("login"==a.type?"登录密码":"交易密码"))]),a._v(" "),"login"==a.type?e("input",{directives:[{name:"model",rawName:"v-model",value:a.formData.newpassword,expression:"formData.newpassword"}],attrs:{type:"password",placeholder:"8-12位（不能全是数字或字母）"},domProps:{value:a.formData.newpassword},on:{input:function(t){t.target.composing||(a.formData.newpassword=t.target.value)}}}):e("input",{directives:[{name:"model",rawName:"v-model",value:a.formData.newpassword,expression:"formData.newpassword"}],attrs:{type:"password",placeholder:"请输入6位数字交易密码",maxlength:"6"},domProps:{value:a.formData.newpassword},on:{input:function(t){t.target.composing||(a.formData.newpassword=t.target.value)}}})]),a._v(" "),e("div",{staticClass:"form-item"},[e("label",{attrs:{for:"name"}},[a._v("确认"+a._s("login"==a.type?"登录密码":"交易密码"))]),a._v(" "),"login"==a.type?e("input",{directives:[{name:"model",rawName:"v-model",value:a.formData.password,expression:"formData.password"}],attrs:{type:"password",placeholder:"8-12位（不能全是数字或字母）"},domProps:{value:a.formData.password},on:{input:function(t){t.target.composing||(a.formData.password=t.target.value)}}}):e("input",{directives:[{name:"model",rawName:"v-model",value:a.formData.password,expression:"formData.password"}],attrs:{type:"password",placeholder:"请输入6位数字交易密码",maxlength:"6"},domProps:{value:a.formData.password},on:{input:function(t){t.target.composing||(a.formData.password=t.target.value)}}})]),a._v(" "),e("div",{staticClass:"form-item"},[e("label",{attrs:{for:"name"}},[a._v("手机验证码")]),a._v(" "),e("div",{staticClass:"item-flex code"},[e("input",{directives:[{name:"model",rawName:"v-model",value:a.formData.captcha,expression:"formData.captcha"}],attrs:{type:"text",placeholder:"请输入验证码",oninput:"if(value.length>4)value=value.slice(0,4)"},domProps:{value:a.formData.captcha},on:{input:function(t){t.target.composing||(a.formData.captcha=t.target.value)}}}),a._v(" "),e("button",{staticClass:"btn btn-origin",attrs:{type:"button",disabled:a.seconds>0},on:{click:a.getCode}},[a._v(a._s(0==a.seconds?"点击获取":"已发送("+a.seconds+"s)"))])])]),a._v(" "),e("button",{staticClass:"btn-submit btn-origin",attrs:{type:"button"},on:{click:a.confirm}},[a._v("确定")])])])],1)},staticRenderFns:[]}}});