webpackJsonp([13],{229:function(t,e,a){function o(t){a(409)}var n=a(17)(a(301),a(492),o,"data-v-712ee861",null);t.exports=n.exports},266:function(t,e,a){t.exports={default:a(267),__esModule:!0}},267:function(t,e,a){a(269),t.exports=a(0).Object.assign},268:function(t,e,a){"use strict";var o=a(14),n=a(57),i=a(23),r=a(36),d=a(106),s=Object.assign;t.exports=!s||a(18)(function(){var t={},e={},a=Symbol(),o="abcdefghijklmnopqrst";return t[a]=7,o.split("").forEach(function(t){e[t]=t}),7!=s({},t)[a]||Object.keys(s({},e)).join("")!=o})?function(t,e){for(var a=r(t),s=arguments.length,l=1,c=n.f,v=i.f;s>l;)for(var m,f=d(arguments[l++]),p=c?o(f).concat(c(f)):o(f),g=p.length,u=0;g>u;)v.call(f,m=p[u++])&&(a[m]=f[m]);return a}:s},269:function(t,e,a){var o=a(10);o(o.S+o.F,"Object",{assign:a(268)})},270:function(t,e,a){"use strict";e.__esModule=!0;var o=a(266),n=function(t){return t&&t.__esModule?t:{default:t}}(o);e.default=n.default||function(t){for(var e=1;e<arguments.length;e++){var a=arguments[e];for(var o in a)Object.prototype.hasOwnProperty.call(a,o)&&(t[o]=a[o])}return t}},301:function(t,e,a){"use strict";Object.defineProperty(e,"__esModule",{value:!0});var o=a(58),n=a.n(o),i=a(270),r=a.n(i),d=a(104),s=a(105),l=a(107);e.default={data:function(){return{formData:{account:null,password:null,identifier:null,captcha:null},imgCode:null,randomCode:null,env:null,barcode:null}},created:function(){this.initData(),this.getRandom()},computed:r()({},a.i(l.a)(["api"])),methods:{initData:function(){this.env="production",this.clearAllCookie()},backPwd:function(){this.$router.push({name:"BackPwd"})},login:function(){var t=this,e=this.formData,a=s.a.isValidate(e);if(a)return void mui.toast(a);s.b.loadStart(this),d.b.login(e).then(function(e){1==e.code?(localStorage.setItem("user_id",e.data.id),localStorage.setItem("userInfo",n()(e.data)),console.log(t.getCookie("token")),localStorage.setItem("cookie",unescape(t.getCookie("token"))),setTimeout(function(){s.b.loadEnd(t),mui.toast(e.msg),t.$router.replace({name:"index"})},1e3)):setTimeout(function(){s.b.loadEnd(t),mui.toast(e.msg)},1e3)})},getImgCode:function(t){var e=this;d.a.getImgCode({identifier:t}).then(function(t){e.imgCode=t})},getRandom:function(){var t=parseInt(1e8*Math.random(0,1)),e=parseInt(1e8*Math.random(0,1));this.randomCode=t+"abcdd"+e+"-"+(new Date).getTime(),this.formData.identifier=this.randomCode},toScan:function(){},onmarked:function(t,e){var a="未知: ";switch(t){case plus.barcode.QR:a="QR: ";break;case plus.barcode.EAN13:a="EAN13: ";break;case plus.barcode.EAN8:a="EAN8: "}alert(a+e)},clearAllCookie:function(){var t=new Date;t.setTime(t.getTime()-1e4);var e=document.cookie.match(/[^ =;]+(?=\=)/g);if(e)for(var a=e.length;a--;)document.cookie=e[a]+"=0; expire="+t.toGMTString()+"; path=/"},goToDownload:function(){this.$router.push({name:"Download"})},getCookie:function(t){console.log("getCookie");var e,a=new RegExp("(^| )"+t+"=([^;]*)(;|$)");return(e=document.cookie.match(a))?unescape(e[2]):null}}}},367:function(t,e,a){e=t.exports=a(224)(!1),e.push([t.i,'.nowrap[data-v-712ee861]{overflow:hidden;text-overflow:ellipsis;white-space:nowrap}.select-none[data-v-712ee861]{moz-user-select:-moz-none;-moz-user-select:none;-o-user-select:none;-webkit-user-select:none;-ms-user-select:none;user-select:none}.no-beforeafter[data-v-712ee861]:after,.no-beforeafter[data-v-712ee861]:before{display:none!important}.has-beforeafter[data-v-712ee861]:after,.has-beforeafter[data-v-712ee861]:before{display:block!important}.fl[data-v-712ee861]{float:left}.fr[data-v-712ee861]{float:right}.clear[data-v-712ee861]:after{content:" ";display:block;clear:both}.overflow-hidden[data-v-712ee861]{overflow:hidden!important}.border-none[data-v-712ee861]{border:none!important}.margin-top-20[data-v-712ee861]{margin-top:2.667vw}.margin-top-10[data-v-712ee861]{margin-top:1.333vw}.margin-top-0[data-v-712ee861]{margin-top:0}.margin-bottom-20[data-v-712ee861]{margin-bottom:2.667vw}.margin-bottom-10[data-v-712ee861]{margin-bottom:1.333vw}.margin-bottom-0[data-v-712ee861]{margin-bottom:0}.margin-right-20[data-v-712ee861]{margin-right:2.667vw}.margin-right-10[data-v-712ee861]{margin-right:1.333vw}.margin-right-0[data-v-712ee861]{margin-right:0}.margin-left-20[data-v-712ee861]{margin-left:2.667vw}.margin-left-10[data-v-712ee861]{margin-left:1.333vw}.margin-left-0[data-v-712ee861]{margin-left:0}.padding-top-20[data-v-712ee861]{padding-top:2.667vw}.padding-top-10[data-v-712ee861]{padding-top:1.333vw}.padding-top-0[data-v-712ee861]{padding-top:0}.padding-bottom-20[data-v-712ee861]{padding-bottom:2.667vw}.padding-bottom-10[data-v-712ee861]{padding-bottom:1.333vw}.padding-bottom-0[data-v-712ee861]{padding-bottom:0}.padding-right-20[data-v-712ee861]{padding-right:2.667vw}.padding-right-10[data-v-712ee861]{padding-right:1.333vw}.padding-right-0[data-v-712ee861]{padding-right:0}.padding-left-20[data-v-712ee861]{padding-left:2.667vw}.padding-left-10[data-v-712ee861]{padding-left:1.333vw}.padding-left-0[data-v-712ee861]{padding-left:0}.text-xl[data-v-712ee861]{font-size:4.8vw}.text-lg[data-v-712ee861]{font-size:4.267vw}.text-size-sd[data-v-712ee861]{font-size:3.733vw}.text-sm[data-v-712ee861]{font-size:3.2vw}.text-xs[data-v-712ee861]{font-size:2.667vw}.text-center[data-v-712ee861]{text-align:center}.text-bold[data-v-712ee861]{font-weight:700}.text-ano[data-v-712ee861]{color:#999}.text-color-sd[data-v-712ee861]{color:#333}.text-sd[data-v-712ee861]{font-size:3.733vw;color:#333}.text-row[data-v-712ee861]{padding-top:1.6vw;padding-bottom:1.6vw}.layout-center-margin[data-v-712ee861]{margin-left:3%;margin-right:3%}.border-radius-standard[data-v-712ee861]{border-radius:1.333vw}.readonlyAsNormal[data-v-712ee861]{opacity:1;-webkit-text-fill-color:#333}.moc-wrap[data-v-712ee861]{position:relative}.moc-wrap .vm[data-v-712ee861]{position:absolute;top:50%;-webkit-transform:translateY(-50%);transform:translateY(-50%)}.moc-wrap .hc[data-v-712ee861]{position:absolute;left:50%;-webkit-transform:translateX(-50%);transform:translateX(-50%)}.moc-wrap .mc[data-v-712ee861]{position:absolute;top:50%;left:50%;-webkit-transform:translate(-50%,-50%);transform:translate(-50%,-50%)}.vHidden[data-v-712ee861]{visibility:hidden}.vShow[data-v-712ee861]{visibility:visible}.hidden[data-v-712ee861]{display:none!important}.show[data-v-712ee861]{display:block}.flexV[data-v-712ee861]{display:box;display:-ms-flexbox;display:-webkit-flex;display:-webkit-box;-webkit-box-orient:vertical;-moz-flex-direction:column;-ms-flex-direction:column;-o-flex-direction:column;display:flex;flex-direction:column;-webkit-box-pack:justify;-ms-flex-pack:justify;justify-content:space-between;height:100%}.body[data-v-712ee861]{background-color:#171a2d}.body .img-box[data-v-712ee861]{display:-webkit-box;display:-ms-flexbox;display:flex;-webkit-box-pack:center;-ms-flex-pack:center;justify-content:center;-webkit-box-align:center;-ms-flex-align:center;align-items:center}.body .img-box .logo[data-v-712ee861]{margin:8vw 0;height:24vw}.body .tj-form .btn-white[data-v-712ee861]{background-color:#f5f5f5;color:#333;font-size:5.333vw;letter-spacing:5px;font-family:fantasy}.body .other[data-v-712ee861]{text-align:right;margin:0 4vw 4vw;color:#fff;font-size:3.2vw}.body .other a[data-v-712ee861]{padding:1.333vw 2.667vw}.body .btn-download[data-v-712ee861]{display:block;width:92%;height:44px;background:#78bc43;margin:0 auto 4vw}',""])},409:function(t,e,a){var o=a(367);"string"==typeof o&&(o=[[t.i,o,""]]),o.locals&&(t.exports=o.locals);a(225)("193b13cc",o,!0)},455:function(t,e,a){t.exports=a.p+"static/images/logo.png"},492:function(t,e,a){t.exports={render:function(){var t=this,e=t.$createElement,a=t._self._c||e;return a("div",{staticClass:"page"},[a("m-header",{attrs:{title:"登录"}}),t._v(" "),a("section",{staticClass:"body"},[t._m(0),t._v(" "),a("form",{staticClass:"tj-form"},[a("div",{staticClass:"form-item"},[a("label",{attrs:{for:"name"}},[t._v("手机号")]),t._v(" "),a("input",{directives:[{name:"model",rawName:"v-model",value:t.formData.account,expression:"formData.account"}],attrs:{type:"text",placeholder:"请输入手机号",oninput:"if(value.length>11)value=value.slice(0,11)"},domProps:{value:t.formData.account},on:{input:function(e){e.target.composing||(t.formData.account=e.target.value)}}})]),t._v(" "),a("div",{staticClass:"form-item"},[a("label",{attrs:{for:"name"}},[t._v("密码")]),t._v(" "),a("input",{directives:[{name:"model",rawName:"v-model",value:t.formData.password,expression:"formData.password"}],attrs:{type:"password",placeholder:"8-12位（不能全是数字或字母）"},domProps:{value:t.formData.password},on:{input:function(e){e.target.composing||(t.formData.password=e.target.value)}}})]),t._v(" "),a("div",{staticClass:"form-item"},[a("label",{attrs:{for:"name"}},[t._v("图形验证码")]),t._v(" "),a("div",{staticClass:"item-flex code"},[a("input",{directives:[{name:"model",rawName:"v-model",value:t.formData.captcha,expression:"formData.captcha"}],attrs:{type:"text",placeholder:"请输入图中的验证码",maxlength:"4"},domProps:{value:t.formData.captcha},on:{input:function(e){e.target.composing||(t.formData.captcha=e.target.value)}}}),t._v(" "),"development"==t.env?a("img",{staticClass:"btn img-code",attrs:{src:"http://localhost:9000/local/api/Captcha/get?identifier="+t.randomCode,alt:""},on:{click:function(e){t.getRandom()}}}):a("img",{staticClass:"btn img-code",attrs:{src:t.api+"/api/Captcha/get?identifier="+t.randomCode,alt:""},on:{click:function(e){t.getRandom()}}})])]),t._v(" "),a("button",{staticClass:"btn-submit btn-origin",attrs:{type:"button"},on:{click:t.login}},[t._v("登录")])]),t._v(" "),a("p",{staticClass:"other"},[a("a",{directives:[{name:"show",rawName:"v-show",value:!1,expression:"false"}],on:{click:t.toScan}},[t._v("扫一扫注册")]),t._v(" "),a("a",{on:{click:t.backPwd}},[t._v("找回密码")])]),t._v(" "),a("button",{staticClass:"btn-submit btn-download",attrs:{type:"button"},on:{click:t.goToDownload}},[t._v("下载 App")])]),t._v(" "),a("m-load",{ref:"load"})],1)},staticRenderFns:[function(){var t=this,e=t.$createElement,o=t._self._c||e;return o("div",{staticClass:"img-box"},[o("img",{staticClass:"logo",attrs:{src:a(455),alt:""}})])}]}}});