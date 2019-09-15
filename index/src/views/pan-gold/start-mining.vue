<template>
  <div class="page">
    <m-header title="抢购中" :canback="Boolean(1)"></m-header>
    <div class="body">
      <img class="img" src="../../assets/images/gzlz/mining.gif">
      <p class="tip">哈希碰撞挖矿中，请不要退出此页面</p>
      <!-- <div @click="goTo('BuyResule', 'success')">抢购成功跳转</div>
      <div @click="goTo('BuyResule', 'fail')">抢购失败跳转</div> -->
    </div>
  </div>
</template>
<script>
import {Index} from '@/server/server.js';
import {mapGetters} from 'vuex';
export default {
    data (){
        return {
            level:null,
            mineTime:null,
            mineInt:null,
            randomTime:null,
            lastTime:null
        }
    },
    mounted(){
        this.initData();
        let _this = this;
        //3分钟 请求结果
        this.mineTime = setTimeout(() => {
          setTimeout(() => {
            this.mineInt = setInterval(() => {
              this.getoreResult(this.level);
            }, 3000);
          }, this.randomTime*1000);
        }, 180000);
        //5分钟超时截断跳转
        this.lastTime = setTimeout(() => {
          mui.alert('抢购过于热情，请前往记录查看抢购详情',()=>{
            _this.$router.push({name:'Mine'})
          })
        }, 300000);
    },
    computed:{
      ...mapGetters(['userInfo'])
    },
    methods: {
        initData(){
            this.level = this.$route.params.level;
            this.randomTime = parseInt(Math.random()*20);
        },
        goTo(routeName, params) {
          this.$router.push({ name: routeName ,query:{ type:params }});
        },
        /**
         * 抢矿结果
         */
        getoreResult(level){
            Index.getoreResult({level,username:this.userInfo.username}).then(res=>{
                if(res.code == 1){
                    this.$router.push({path:res.data.url})
                }
                else {
                  return ;
                }
            })
        },
    },
     beforeRouteEnter (to, from, next) {
        next(vm=>{
          if(from.name=='BuyResule' || from.name == 'Mine'){
            vm.$router.go(-1);
          }
        })
    },
    beforeRouteLeave (to, from, next) {
        clearInterval(this.mineInt);
        clearTimeout(this.mineTime);
        next();
      }
};
</script>
<style lang="less" scoped>
@import "~link-less";
.body {
  .display-flex();
  padding-top: 215px;
  flex-direction: column;align-items: center;
  background: @bg-color;color: #fff;
  font-size: 25px;
  .img {
    width: 80%;
  }
  .tip {
    color: red;
    font-size: 32px;
    margin-top: 50px;
  }
}
</style>
