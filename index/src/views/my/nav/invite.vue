<template>
    <div class="page">
        <m-header title="推荐二维码" :canback="Boolean(1)"></m-header>
        <section class="body">
            <div class="img-box">
                <p v-if="userInfo" class="url">{{api+'#'+'/register/'+uid+'/'+userInfo.mobile}}</p>
                <img class="img" :src="inviteSrc" alt="">
            </div>
        </section>
    </div>
</template>
<script>
import QRCode from 'qrcode';
import {mapGetters} from 'vuex';
export default {
    name:"invite",
    data(){
        return {
            inviteSrc:null,
            inviteUrl:null,
        }
    },
    mounted(){
        // console.log(this.userInfo)
        this.initData();
        this.setCode(this.inviteUrl);
    },
    computed:{
        ...mapGetters(['uid','userInfo','api'])
    },
    methods:{
        initData(){
            this.inviteUrl = this.api+'/dist/index.html#'+'/register/'+this.uid+'/'+this.userInfo.mobile;

        },
        /**
         * 生成二维码
         */
        setCode(url){
            QRCode.toDataURL(url).then(url => {
                this.inviteSrc = url;
            })
            .catch(err => {
                console.error(err)
            })
        }
    }
}
</script>
<style lang="less" scoped>
@import '~link-less';
    .body {
        text-align: center;
        background-image: url(../../../assets/images/gzlz/invate_bg.png);
        background-size: 100% 100%;
        .title {
            font-size: 40px;
            color: @primary-color;
            margin-top: 5%;
        }
        .url {
            // margin-top: 75%;
            margin-bottom: 20px;
        }
        .img-box {
            position: absolute;bottom: 20%;left: 50%;
            .transform(translateX(-50%));
            // margin-top: 85%;
            // margin-top: 
        }
        @media screen and (max-height:667px){
            .img-box {
                position: absolute;bottom: 10%;left: 50%;
                .transform(translateX(-50%));
            }
        }
    }
</style>
