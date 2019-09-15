<template>
    <div class="page">
        <m-header title="登录"></m-header>

        <section class="body">
            <div class="img-box">
                <img class="logo" :src="proData&&api+proData.login_logo" alt="">
            </div>
             <form class="tj-form">
                <div class="form-item">
                    <label for="name">手机号</label>
                    <input type="text" placeholder="请输入手机号" v-model="formData.account"  oninput="if(value.length>11)value=value.slice(0,11)">
                </div>
                <div class="form-item">
                    <label for="name">密码</label>
                    <input type="password" placeholder="8-12位（不能全是数字或字母）" v-model="formData.password">
                </div>
                <div class="form-item">
                    <label for="name">图形验证码</label>
                    <div class="item-flex code">
                        <input type="text" placeholder="请输入图中的验证码" v-model="formData.captcha" maxlength="4">
                        <img class="btn img-code" :src="api+'/api/Captcha/get?identifier='+randomCode" alt="" @click="getRandom()">
                    </div>
                </div>
                <button type="button" class="btn-submit btn-blue" @click="login">登录</button>
            </form>
            <!-- <p class="other"><a @click="toScan">扫一扫注册</a> <a @click="backPwd">找回密码</a></p> -->
            <p class="other"><a @click="actionSheet">{{isApp?'扫一扫注册':''}}</a> <a @click="backPwd">找回密码</a></p>
            <button type="button" class="btn-submit btn-download" @click="goToDownload">下载 App</button>
        </section>
        <m-load ref="load"></m-load>
    </div>
</template>
<script>
import {Init,Base} from '@/server/server.js';
import {Util,Load,HB} from '@/assets/commonjs/utils.js';
import {mapGetters} from 'vuex';
export default {
    data () {
        return {
            formData:{
                account:null,
                password:null,
                identifier:null,
                captcha:null,
            },
            imgCode:null,
            randomCode:null,
            barcode:null,
            scan:null,
            isApp:false
        }
    },
    mounted(){
        this.initData();
        this.getRandom();
    },
    computed:{
        ...mapGetters(['api','proData'])
    },
    methods:{
        initData(){
             mui.init({
                swipeBack:true //启用右滑关闭功能
            });
            try{
                if(plus){
                    this.isApp = true;
                }
            }catch(e){}

        },
        /**
         * 找回密码
         */
        backPwd(){
            this.$router.push({name:'BackPwd'})
        },
        /**
         * 登录
         */
        login(){
            let params = this.formData;
// console.log(params);
// return ;
            let msg = Util.isValidate(params);
            if(msg){
              mui.toast(msg);
              return ;
            }
            Load.loadStart(this);
            Init.login(params).then((res)=>{
                mui.toast(res.msg);
                if(res.code == 1){
                    localStorage.setItem('user_id',res.data.id);
                    localStorage.setItem('userInfo',JSON.stringify(res.data));
                    localStorage.setItem('cookie',res.data.token);
                    setTimeout(() => {
                        Load.loadEnd(this);
                        this.$router.replace({name:'index'})
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
         * 扫码选项  相机 相册
         */
        actionSheet(){
            let _this = this;
            try{
                plus.nativeUI.actionSheet({
                    // title:"Plus is ready!",
                    cancel:"取消",
                    buttons:[
                        {
                            title:"相机"
                        },{
                            title:"相册"
                            }
                    ]},
                    function(e){
                        console.log("User pressed: "+e.index);
                        if(e.index == 1){
                            _this.toScan();
                        }else {
                            _this.selectGallery();
                        }
                    });

            }catch(e){}
        },
        /**
         * 随机标识key
         */
        getRandom(){
            let random1 = parseInt(Math.random(0,1)*100000000);
            let random2 = parseInt(Math.random(0,1)*100000000);
            let str = 'abcdd';
            // return  random1+'abcdd'+random2;
            this.randomCode = random1+'abcdd'+random2+'-'+new Date().getTime();
            this.formData.identifier = this.randomCode;
        },
        /**
         * 相册扫码
         */
        selectGallery(){
            let _This = this;
                HB.picForGallery(_This,res=>{
                    Load.loadStart(_This);
                    plus.downloader.createDownload( _This.api+res, {filename:"_doc/images/"}, function(d,status){  
                        if ( status == 200 ) {   
                                plus.barcode.scan( d.filename, function(type,result) {
                                    Load.loadEnd(_This);
                                    plus.nativeUI.confirm("确定跳转?", function(e){
                                        if(e.index == 0){
                                            plus.runtime.openURL(JSON.parse(result));
                                        }
                                    });
                            }, function(e){
                                console.log("Scan failed: "+JSON.stringify(e));
                            } );
                        } else {  
                            console.log("下载图片文件失败！");  
                        }  
                        plus.nativeUI.closeWaiting();  
                    }).start(); 
                })
        },
        /**
         * 相机扫码
         */
        toScan(){
            let _This = this;
            if(!this.barcode){
                this.barcode = plus.barcode.create('barcode', [plus.barcode.QR], {
                    top:'100px',
                    left:'0px',
                    width: '100%',
                    height: '500px',
                    position: 'static'
                });
                this.barcode.onmarked = _This.onmarked;
                plus.webview.currentWebview().append(this.barcode);
            }
            this.barcode.start();
            // this.scan = new plus.barcode.Barcode('bcid');
            // this.scan.onmarked = this.onmarked();
            // this.scan.start();
        },
        /**
         * 扫码回调
         */
        onmarked(type, result) {
            var text = '未知: ';
            switch(type){
                case plus.barcode.QR:
                text = 'QR: ';
                break;
                case plus.barcode.EAN13:
                text = 'EAN13: ';
                break;
                case plus.barcode.EAN8:
                text = 'EAN8: ';
                break;
            }
            if(result){
                // alert( text+result );
                this.barcode.close();
                try{
                    if(plus){
                        mui.confirm('确定跳转',actions=>{
                            if(actions.index == 0)
                            plus.runtime.openURL(result);
                            
                        })
                    }
                }catch(e){
                    mui.confirm('确定跳转',actions=>{
                        if(actions.index == 1)
                        plus.runtime.openURL(result);
                    })
                }
            }
        },
        /**
         * 下载
         */
        goToDownload() {
          this.$router.push({name:'Download'})
        }
    },
}
</script>

<style scoped lang="less">
@import '~link-less';
.body {
    background-color: @bg-color;
    .img-box {
        .display-flex();
        justify-content: center;align-items: center;
        .logo {
            margin:60px 0;
            height: 180px;
        }
    }
    .tj-form {
        .btn-white {
            background-color: whitesmoke;color: #333;
            font-size: 40px;letter-spacing: 5PX;font-family: fantasy; //monospace fantasy
        }
    }
    .other {
        .display-flex();
        justify-content: space-between;
        margin: 0 @margin-primary  @margin-primary ;
        color: white;
        font-size: 24px;
        a {
            padding: 10px 20px;
        }
    }
    .btn-download {
      display: block;margin: 0 auto 4vw;
      width: 92%;height: 44PX;
      background: #78bc43;
    }
}
</style >

