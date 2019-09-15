<template>
    <div class="page">
        <m-header :title="type== 'ali'?'绑定支付宝':'绑定微信'" :canback="Boolean(1)"></m-header>
        <section class="body">
            <form class="tj-form" v-if="type=='ali'">
                <div class="form-item">
                    <label for="name">支付宝昵称</label>
                    <input type="text" placeholder="请输入昵称" v-model="aliForm.alipayname">
                </div>
                <div class="form-item">
                    <label for="name">支付宝账户</label>
                    <input type="text" placeholder="请输入支付宝账户" v-model="aliForm.alipayact" >
                </div>
                <div class="form-item mar">
                    <label class="mar" for="name">上传收款二维码</label>
                    <!-- <img class="img" :src="api+aliForm.alipayurl||upladSrc" alt=""> -->
                    <img class="img" :src="aliForm.alipayurl?api+aliForm.alipayurl:upladSrc" alt="">
                    <!-- <input v-if="plus" class="file" @click="upload($event)" readonly> -->
                    <input  class="file" type="file" @change="change($event)">
                </div>
                <!-- <div class="form-item">
                    <label for="name">手机验证码</label>
                    <div class="item-flex code">
                        <input type="text" placeholder="请输入验证码" v-model="aliForm.captcha" maxlength="4">
                        <button type="button" class="btn btn-blue" :disabled="seconds>0" @click="getCode">{{seconds==0?"点击获取":"已发送("+seconds+"s)"}}</button>
                    </div>
                </div> -->
                <div class="form-item" >
                    <label for="name">交易密码</label>
                    <input type="password" placeholder="请输入6位数字交易密码" v-model="aliForm.paypwd" maxlength="6">
                </div>
                <button type="button" class="btn-submit btn-blue" @click="bindAli">绑定</button>
            </form >
            <form class="tj-form" v-else>
                <div class="form-item">
                    <label for="name">微信昵称</label>
                    <input type="text" placeholder="请输入昵称" v-model="wechatForm.wechaname">
                </div>
                <div class="form-item">
                    <label for="name">微信账户</label>
                    <input type="text" placeholder="请输入微信账户" v-model="wechatForm.wechatact" >
                </div>
                <div class="form-item mar">
                    <label class="mar" for="name">上传收款二维码</label>
                    <img class="img" :src="wechatForm.wechaturl?api+wechatForm.wechaturl:upladSrc" alt="">
                    <input class="file" type="file" @change="change($event)">
                </div>
                <div class="form-item" >
                    <label for="name">交易密码</label>
                    <input type="password" placeholder="请输入6位数字交易密码" v-model="wechatForm.paypwd" maxlength="6">
                </div>
                <!-- <div class="form-item">
                    <label for="name">手机验证码</label>
                    <div class="item-flex code">
                        <input type="text" placeholder="请输入验证码" v-model="wechatForm.captcha" maxlength="4">
                        <button type="button" class="btn btn-blue" :disabled="seconds>0" @click="getCode">{{seconds==0?"点击获取":"已发送("+seconds+"s)"}}</button>
                    </div>
                </div> -->
                <button type="button" class="btn-submit btn-blue" @click="bindWechat">绑定</button>
            </form>
        </section>
        <m-load ref="load"></m-load>
    </div>
</template>
<script>
import {Owner,Base} from '@/server/server.js';
import {mapGetters} from 'vuex';
import {Util,Load,HB} from '@/assets/commonjs/utils.js';
export default {
    name:"PayInfo",
    data(){
        return {
            title:'绑定支付宝',
            seconds:0,
            upladSrc:require('@/assets/images/gzlz/upload.png'),
            type:null,
            aliForm:{
                id:null,
                alipayname:null,
                alipayact:null,
                alipayurl:null,
                mobile:null,
                paypwd:null
                // captcha:null
            },
            wechatForm:{
                id:null,
                wechaname:null,
                wechatact:null,
                wechaturl:null,
                // captcha:null,
                mobile:null,
                paypwd:null

            },
            plus:null
        }
    },
    mounted(){
        this.initData();
console.log(this.temp)
// console.log(this.aliForm)
    },
    computed:{
        ...mapGetters(['uid','userInfo','temp','api'])
    },
    methods:{
        initData(){
            let _this = this;
            try{
                this.plus = plus;
            }catch(e){}
            this.type = this.$route.params.type;    
                this.aliForm.alipayname = this.temp.alipayname;
                this.aliForm.alipayact= this.temp.alipayact;
                this.aliForm.alipayurl= this.temp.alipay_url;
                this.aliForm.mobile= this.userInfo.mobile;
                this.aliForm.id = this.uid;

                this.wechatForm.wechaname= this.temp.wechatname;
                this.wechatForm.wechatact= this.temp.wechatact;
                this.wechatForm.wechaturl= this.temp.wechat_url;
                this.wechatForm.mobile= this.userInfo.mobile;
                this.wechatForm.id = this.uid;
        },
        getCode(){
            this.seconds = 60;
            let get =  setInterval(()=>{
                this.$nextTick(()=>{
                    this.seconds = this.seconds-1;
                    if(this.seconds<=0){
                        clearInterval(get);
                    }
                })
            },1000);
// return ;
            // 发送验证码
            Base.sendCode({mobile:this.userInfo.mobile}).then((res)=>{
                mui.toast(res.msg);
            })
        },
        bindAli(){
// console.log(this.aliForm);
// return ;
            // if(!this.isValidate(this.aliForm)){
            //   mui.toast('请正确填写信息！');
            //   return ;
            // }
            let msg = Util.isValidate(this.aliForm);
                if(msg){
                    mui.toast(msg);
                    return ;
                }
            Load.loadStart(this);
            Owner.bindAli(this.aliForm).then(res=>{
                if(res.code == 1){
                    setTimeout(() => {
                        mui.toast(res.msg);
                        Load.loadEnd(this);
                        this.$router.go(-1);
                    }, 1000);
                }else {
                    mui.toast(res.msg);
                    Load.loadEnd(this);
                    return ;
                }
            })
        },
        bindWechat(){
// console.log(this.wechatForm);
// return ;

            // if(!this.isValidate(this.wechatForm)){
            //   mui.toast('请正确填写信息！');
            //   return ;
            // }
            let msg = Util.isValidate(this.wechatForm);
                if(msg){
                    mui.toast(msg);
                    return ;
                }
            Load.loadStart(this);
            Owner.bindwechat(this.wechatForm).then(res=>{
                if(res.code == 1){
                    setTimeout(() => {
                        mui.toast(res.msg);
                        Load.loadEnd(this);
                        this.$router.go(-1);
                    }, 1000);
                }else {
                    mui.toast(res.msg);
                    Load.loadEnd(this);
                    return ;
                }
            })

        },
        change(event){
            let _This = this;
            let formData = new FormData();
            Load.loadStart(_This);
            Util.uploadImgLimit(event.target.files[0],url=>{
                formData.append('image',url);
                Base.upload(formData).then((res)=>{
                Load.loadEnd(_This);
                    if(res.code == 1){
                        if(_This.type == 'ali'){
                            this.aliForm.alipayurl = res.data;
                        }else {
                            this.wechatForm.wechaturl = res.data;
                        }
                    }else {
                        mui.toast(res.msg);
                        return ;
                    }
                })
            })
        },
        // hbuilder 拍照
        upload(){
            let _this = this;
            HB.picForGallery(_this,url=>{
                if(_this.type == 'ali'){
                    _this.aliForm.alipayurl = url;
                }else {
                    _this.wechatForm.wechaturl = url;
                }
            })
            return ;
            HB.HBCapture(_this,url=>{
                if(_this.type == 'ali'){
                    _this.aliForm.alipayurl = url;
                }else {
                    _this.wechatForm.wechaturl = url;
                }
            })
        },
        isValidate(obj){
          if(!Util.filterData(obj)){
              return false;
            }else{
              return true;
            }
        }
    }
}
</script>
<style lang="less" scoped>
@import '~link-less';
    .body {
        background-color: @bg-color;
        // padding: @padding-primary;
        .form-item {
            .img {
                border: 1px solid #f3f3f3;
            }
            
        }
    }
</style>
