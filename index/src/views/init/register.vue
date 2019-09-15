<template>
  <div class="page">
    <m-header title="注册"></m-header>
    <section class="body">
      <form class="tj-form">
        <!-- <div class="form-item">
          <label for="name">用户名</label>
          <input type="text" placeholder="英文数字组成" v-model="formData.username">
        </div> -->
        <div class="form-item">
          <label for="name">手机号</label>
          <!-- <input type="text" placeholder="请输入手机号" v-model="formData.mobile" @blur="IsMobilePhoneNumber($event)" oninput="if(value.length>11)value=value.slice(0,11)"> -->
          <input type="text" placeholder="请输入手机号" v-model="formData.mobile"  oninput="if(value.length>11)value=value.slice(0,11)">
          <!-- <span class="error" v-show="!rule.isMobile.validate"><i class="iconfont iconerror1">{{rule.isMobile.msg}}</i></span> -->
        </div>
        <div class="form-item">
          <label for="name">登录密码</label>
          <input type="password" placeholder="8-12位（不能全是数字或字母）" @blur="IsIntegerAndEnglishCharacter($event)" v-model="formData.password">
          <span class="error" v-show="!rule.isPwd.validate"><i class="iconfont iconerror1">{{rule.isPwd.msg}}</i></span>
        </div>

        <div class="form-item">
          <label for="name">确认登录密码</label>
          <input type="password" placeholder="8-12位（不能全是数字或字母）" @blur="confirmL($event)">
          <span class="error" v-show="!rule.conFirmL.validate"><i class="iconfont iconerror1">{{rule.conFirmL.msg}}</i></span>
        </div>
        <div class="form-item">
          <label for="name">设置交易密码</label>
          <input type="password" placeholder="请输入6位数字交易密码" v-model="formData.paypwd" maxlength="6">
        </div>
        <div class="form-item">
          <label for="name">确认交易密码</label>
          <input type="password" placeholder="请输入6位数字交易密码" maxlength="6" @blur="confirmP($event)">
          <span class="error" v-show="!rule.conFirmP.validate"><i class="iconfont iconerror1">{{rule.conFirmP.msg}}</i></span>
        </div>
        <div class="form-item">
          <label for="name">推荐人账号</label>
          <input type="text" readonly v-model="tjMobile">
        </div>
        <div class="form-item">
          <label for="name">图形验证码</label>
          <div class="item-flex code">
            <input type="text" placeholder="请输入图中的验证码" v-model="formData.imgcaptcha" maxlength="4">
            <img
              class="btn img-code"
              :src="api+'/api/Captcha/get?identifier='+randomCode"
              alt
              @click="getRandom()"
            >
          </div>
        </div>
        <div class="form-item">
          <label for="name">手机验证码</label>
          <div class="item-flex code">
            <input type="text" placeholder="请输入验证码" v-model="formData.captcha" maxlength="4">
            <button type="button" class="btn btn-blue" :disabled="seconds>0" @click="sendCode()">{{seconds==0?"点击获取":"已发送("+seconds+"s)"}}</button>
          </div>
        </div>
        <button type="button" class="btn-submit btn-blue" @click="register()">注册</button>
      </form>
    </section>
    <m-load ref="load"></m-load>
  </div>
</template>
<script>
import {Init,Base} from '@/server/server.js';
import {Util,Load} from '@/assets/commonjs/utils.js';
import {mapGetters} from 'vuex';
export default {
    components:{
    },
    data () {
        return {
            seconds:0,
            formData:{
                username:null,
                mobile:null,
                password: null,
                paypwd: null,
                tjjr: 222,
                captcha:null,
                imgcaptcha:null,
                identifier: null
            },
            randomCode:null,
            tjMobile:null,
            rule:{
              // isMobile:{
              //   validate:true,
              //   msg:'请输入正确的手机号'
              // },
              isPwd:{
                validate:true,
                msg:'密码应为8-12位字母+数字'
              },
              conFirmL:{
                validate:true,
                msg:'两次输入密码不一致！'
              },
              conFirmP:{
                validate:true,
                msg:'两次输入密码不一致！'
              },
            },

        }
    },
    mounted(){
        this.initData();
        this.getRandom();
    },
    computed:{
      ...mapGetters(['api'])
    },
    methods:{
        initData(){
            this.formData.tjjr = this.$route.params.id;
            this.tjMobile = this.$route.params.mobile;
        },
        /**
         * 发送验证码
         */
        sendCode(){
            // if(!this.formData.mobile){
            //     mui.toast('请输入正确的手机号！');
            //     return ;
            // }
            this.seconds = 60;
            let get =  setInterval(()=>{
                this.$nextTick(()=>{
                    this.seconds = this.seconds-1;
                    if(this.seconds<=0){
                        clearInterval(get);
                    }
                })
            },1000)
            // 发送验证码
            Base.sendCode({mobile:this.formData.mobile}).then((res)=>{
                mui.toast(res.msg);
            })
        },
        /**
         * 注册
         */
        register(){
            // console.log(this.formData);return;
            this.formData.username = this.formData.mobile;
            let msg = Util.isValidate(this.formData,this.rule);
            if(msg){
              mui.toast(msg);
              return ;
            }
            Load.loadStart(this);
            Init.register(this.formData).then((res)=>{
                mui.toast(res.msg);
                if(res.code == 1){
                    setTimeout(() => {
                        Load.loadEnd(this);
                        this.$router.replace({name:'Login'});
                    }, 500);
                }else {
                    setTimeout(() => {
                        Load.loadEnd(this);
                        return ;
                    }, 500);
                }
            })
        },
        /**
         * 随机码key
         */
        getRandom(){
            let random1 = parseInt(Math.random(0,1)*100000000);
            let random2 = parseInt(Math.random(0,1)*100000000);
            let str = 'bacdd';
            this.randomCode = random1+'abcdd'+random2+'-'+new Date().getTime();
            this.formData.identifier = this.randomCode;
        },
        confirmL(event){
          if(event.target.value !=this.formData.password){
            this.rule.conFirmL.validate = false;
          }else {
            this.rule.conFirmL.validate = true;
          }
        },
        confirmP(event){
          if(event.target.value !=this.formData.paypwd){
            this.rule.conFirmP.validate = false;
          }else {
            this.rule.conFirmP.validate = true;
          }
        },
        IsMobilePhoneNumber(event) {
          var regex = /^((\+)?86|((\+)?86)?)0?1[3458]\d{9}$/;
          if (event.target.value.match(regex)) {
            this.rule.isMobile.validate = true;
          } else {
            this.rule.isMobile.validate = false;            
          }
        },
        IsIntegerAndEnglishCharacter(input) {
          var regex = /[A-Za-z].*[0-9]|[0-9].*[A-Za-z]/;
          if (event.target.value.match(regex)) {
            this.rule.isPwd.validate = true;
          } else {
            this.rule.isPwd.validate = false;
          }
        },
    }
}
</script>

<style scoped lang="less">
@import "~link-less";
.body {
  background-color: @bg-color;
  .tj-form {
    .btn-white {
      background-color: whitesmoke;color: #333;
      font-size: 40px;letter-spacing: 5px;font-family: fantasy; //monospace fantasy
    }
  }
  .error {
    position: absolute;bottom: 0;left: 0;
    color: red;
    font-size: 24px;
  }
}
</style >

