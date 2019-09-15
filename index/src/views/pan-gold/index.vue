<template>
  <div class="page">
    <!-- <m-header title="淘金区块矿" :icon="'iconfont iconqkk_icon_sy'"></m-header> -->
    <section class="body">
      <scroller 
            ref="my_scroller"
            :on-refresh="refresh"
            :on-infinite="infinite">
        </scroller>
      <div class="notice" @click="notice()"> 
        <div class="notice-inner">
          <i class="iconfont icongonggao mark"></i><span style="margin:0 5px;">公告:</span>
            <ul v-if="noticeList && noticeList.length>0" class="notice-list">
                <li class="notice-item" v-for="(item,index) in noticeList" :key="index" >{{item.title}}</li>
            </ul>
            <span v-else>暂无</span>
        </div>
      </div>
      <!-- <img class="banner" :src="bannerSrc?api+bannerSrc:defaultBanner" alt> -->
      <swiper :options="swiperOption" class="swiper-wrap" ref="mySwiper" v-if="banner.length!=0">
        <swiper-slide v-for="(item, index) in banner" :key="index">
          <img :src="api+item" class="title-img" />
        </swiper-slide>
        <!-- 常见的小圆点 -->
        <div
          class="swiper-pagination"
          v-for="(item,index) in banner"
          :key="index"
          slot="pagination"
        ></div>
      </swiper>
      <ul class="list" v-if="list">
        <!-- <li class="list-con" v-for="(item,index) in list" :key="index" v-if="item.status == 1"> -->
        <li class="list-con" v-for="(item,index) in list" :key="index">
          <div class="img-wrap">
            <img :src="api+item.images" class="img">
          </div>
          <ul class="con-wrap">
            <li class="title">{{item.levelname}}</li>
            <li>价值：{{item.min_price}}-{{item.max_price}}</li>
            <li>挖矿时间：{{formatTime(item.stime)}}-{{formatTime(item.etime)}}</li>
            <li>预约/即抢采{{coinTxt&&coinTxt['credit1_text']}}：{{parseInt(item.money)}}/{{parseInt(item.money2)}}</li>
            <li>智能合约收益：{{item.days}}天/{{parseInt(item.per)}}%</li>
            <li>可挖{{coinTxt&&coinTxt['credit2_text']}}：{{parseInt(item.credit2)}}枚</li>
            <li>可挖{{coinTxt&&coinTxt['credit4_text']}}：收益{{parseInt(item.credit4)}}%</li>
          </ul>
      
          <!-- <div  class="type-btn btn-default" >敬请等待</div> -->
          <div v-if="item.isbooking ==0" :class="{'type-btn btn-blue':true,'btn-red':item.cd_status == 0}" @click="event(item.isbooking,item.level,item.cd_status)">{{setTimeDown(item.isbooking,item.cd_status,item.cd_time,item.minutes,item.seconds,item.level,index)}}</div>
          <div v-else-if="item.isbooking ==1" :class="{'type-btn':true,'btn-blue':item.status !=-1&&item.isbooking == 1,'btn-default':item.status ==-1,'btn-red':item.cd_status == 0}" @click="event(item.isbooking,item.level,item.cd_status)">{{setTimeDown(item.isbooking,item.cd_status,item.cd_time,item.minutes,item.seconds,item.level,index)}}</div>
          <div v-else-if="item.isbooking ==2" class="type-btn btn-default" >敬请等待</div>
          <div v-else-if="item.isbooking ==3" class="type-btn btn-default" >{{coinTxt&&coinTxt['mining_text']}}</div>
          <div v-else-if="item.isbooking ==4" :class="{'type-btn':true,'btn-blue':item.isbooking!=2||item.isbooking!=3,'btn-red':item.cd_status == 0}" @click="event(item.isbooking,item.level,item.cd_status)">{{setTimeDown(item.isbooking,item.cd_status,item.cd_time,item.minutes,item.seconds,item.level,index)}}</div>
        </li>
      </ul>
      <p v-else class="msg">{{msg}}</p>
    </section>
    <m-Footer></m-Footer>
    <m-load ref="load"></m-load>

  </div>
</template>
<script>
import { swiper, swiperSlide } from "vue-awesome-swiper";
import {Init,Index,Base} from '@/server/server.js';
import {Util,Load} from '@/assets/commonjs/utils.js';
import {mapGetters,mapMutations} from 'vuex';
require("swiper/dist/css/swiper.css"); //引入swiper.css

export default {
  components: {
    swiper,
    swiperSlide
  },
  data(){
      return {
        list:null,
         levelArr:[],
         timeInt:null,
         mineInt:null,
         mineFlag:true,
         bookFlag:true,
         orderFlag:true,
         msg:null,
        noticeList:[],
        //  move:0,
        //  moveTime:3,
        //  moveInter:null,
        defaultBanner:require('@/assets/images/gzlz/banner.png'),
        swiperOption: {
        loop: true,
          autoplay: {
            delay: 2500,
            stopOnLastSlide: false,
            /* 触摸滑动后是否继续轮播 */
            disableOnInteraction: false
          },
          // 分页器设置
          pagination: {
            el: ".swiper-pagination",
            clickable: true,
            type: "bullets"
          }
      },
      banner: [],
      }
  },
  mounted() {
      this.initData();
      this.getMine();
      this.mineInt =  setInterval(() => {
        this.getMine();
      }, 60000);
      this.getTitle();
      // if(!this.bannerSrc) 
      // this.getBanner();
      if(!this.coinTxt)
      this.getText()
  },
  computed: {
    ...mapGetters(['uid','api','bannerSrc','coinTxt','proData'])
  },
  methods: {
    initData(){
      this.banner = this.proData&&this.proData.home_top_banner ||[require('@/assets/images/gzlz/banner.png')]
    },
    ...mapMutations(['saveBanner','saveCoinTxt']),
    /**
     * 获取币中名称与按钮
     */
    getText(){
        Base.getText().then(res=>{
            if(res.code == 1){
              this.saveCoinTxt(res.data);
              localStorage.setItem('coinTxt',JSON.stringify(res.data))
            }
        })
    },
    /**
     * 下拉刷新
     */
    refresh(){
        this.getMine();
        setTimeout(() => {
            this.$refs.my_scroller.finishPullToRefresh();
            mui.toast('加载完毕！')
        }, 1000);
    },
    infinite(){},
    goTo(routeName) {
      this.$router.push({name:routeName});
    },
    /**
     * 获取公告
     */
    getTitle(){
        Index.getTitle({type:'mh'}).then(res=>{
            if(res.code == 1){
                this.noticeList = res.data;
            }else {
              mui.toast(res.msg);
            }
        })
    },
    /**
     * 公告入口
     */
    notice(){
        this.$router.push({name:'Notice',query:{type:'mh'}});
    },
    /**
     * 获取单张banner
     */
    getBanner(){
      Index.getBanner().then(res=>{
          if(res.code == 1){
            if(res.data && res.data.banner_img && res.data.banner_img!='')
            this.saveBanner(res.data.banner_img)
          }
      })
    },
    /**
     * 获取首页列表
     */
    async getMine(){
        // console.log('getMine')
        if(!this.orderFlag)
        return;
        let _This =this;
        this.levelArr = [];
        this.orderFlag = false;
        await  Index.getMine().then(res=>{
            if(res.code == 1){
                this.orderFlag = true;
                this.list = res.data||[];
            }else {
                this.orderFlag = true;
                this.msg = res.msg;
                mui.toast(res.msg);
                return ;
            }
        })
    },
    formatTime(time){
      return Util.formatTime(time).substr(0,5)
    },
    event(status,level,cdStatus){
      switch(status){
          case 0:
              this.booking(level);
              break;
          case 1:
              if(cdStatus == 0 ||cdStatus == -1 )
              return ;
              this.mining(level);
              // if(isbooking==1 ||isbooking ==2){
              //   this.$router.push({name:'StartMining',params:{level}});
              // }
              // this.$router.push({name:'StartMining'});
              break;
          case 4:
              if(cdStatus == 0 ||cdStatus == -1)
              return ;
              this.mining(level);
              // this.$router.push({name:'StartMining',params:{level}});
              break;
      }
    },
    /**
     * 预约
     */
    booking(level){
      // console.log(level)
      // return ;
        if(!this.bookFlag)
        return;
        this.bookFlag = false;
        Load.loadStart(this);
        Index.booking({level}).then(res=>{
          if(res.code == 1){
            this.bookFlag = true;
            Load.loadEnd(this);
            mui.toast(res.msg);
            this.getMine();
          }else {
            this.bookFlag = true;
            Load.loadEnd(this);
            mui.toast(res.msg);
          }
        })
    },
    /**
     * 倒计时
     */
    setTimeDown(isbooking,status,cdTime,minutes,seconds,level,index){
        let time = String(new Date().getTime()).substr(0,10);
        // console.log(Number(time),cdTime)
        switch(status){
          case -1:  //-1表示不倒计时的时间段内，不能抢购
            if(isbooking == 0){
              return  this.coinTxt['booking_text']
            }else if(isbooking ==4 ||isbooking ==1){
              return this.coinTxt['mining_text'] 
            }
            // else {
            //   return '已预约'
            // }
            break;
          case 0: //0表示还在倒计时
            this['level'+level] = {
              minutes,seconds
            }
            this.setDown(this['level'+level],index);
            return '距离抢购'+this['level'+level].minutes+':'+this['level'+level].seconds;
            break;
          case 1:  //1表示倒计时结束开始抢购
            return  this.coinTxt['mine_grab_text']
            break;
          case 2:  //2表示倒计时结束处于抢购中的时间段
            return this.coinTxt['mine_grab_text']
            break;

        }
    },
    /**
     * 立即抢购
     */
    mining(level){
          this.result = null;
          Load.loadStart(this);
          if(!this.mineFlag)
          return;
          this.mineFlag = false;
          Index.mining({level}).then(res=>{
              if(res.code == 1){
                  this.mineFlag = true;
                  Load.loadEnd(this);
                  this.$router.push({name:'StartMining',params:{level}});
              }else {
                  this.mineFlag = true;
                  Load.loadEnd(this);
                  mui.confirm(res.msg);
                  return ;
              }
          })
      },
    /**
     * 开始倒计时
     */
    setDown(obj,level){  
      obj.seconds+=1;
      let timeInt = setInterval(() => {
        // console.log(obj.seconds)
        if(!document.getElementsByClassName('list-con')[0]){
          clearInterval(timeInt);
        }
        if(obj.seconds == 0 && obj.minutes == 0){
          clearInterval(timeInt);
          this.getMine();
          return;
        }
        if(obj.seconds<10){
          // console.log(obj.seconds)
            obj.seconds = '0'+Number(obj.seconds);
          }
          if(obj.minutes<10){
            // console.log(obj.minutes)
            obj.minutes = '0'+Number(obj.minutes);
          }
          if(obj.seconds>0){
            obj.seconds = obj.seconds-1;
            if(Number(obj.seconds)<10){
              obj.seconds = '0'+Number(obj.seconds);
            }
          }else{
              if(obj.minutes >0){
                obj.minutes = obj.minutes- 1;
                if(obj.minutes <10){
                  obj.minutes = '0'+Number(obj.minutes);
                }
                  obj.seconds = 59;
              }else {
                // console.log('else')
                // obj.seconds = 0;
                // obj.minutes = 0;
                // clearInterval(timeInt);
                // this.getMine();
              }
            }
            if(obj.minutes ==0 && obj.seconds == 0){
              document.getElementsByClassName('list-con')[level].children[2].innerText = this.coinTxt['mine_grab_text'];
            }else{
              document.getElementsByClassName('list-con')[level].children[2].innerText = '距离抢购'+obj.minutes+':'+obj.seconds;
            }
          
        }, 1000);
    }
  },
  beforeRouteLeave (to, from, next) {
      clearInterval(this.mineInt);
      next();
    }
};
</script>
<style lang="less" scoped>
@import "~link-less";
.body {
  line-height: 36px;
  background-color: @bg-color;
  @keyframes lunbo {
    from {
      right: -75%;
    }
    to {
      right: 75%;
    }
  }
  .swiper-wrap .title-img {
      height: 300px;width: 100%;
  }
  .notice {
        position: relative;margin:0 2%;
        height: 40PX;line-height: 40PX;
        color: white;
        font-size: 24px;
        border-radius:40px;
        overflow: hidden;
        padding-left:5%;
        i,span {
            vertical-align: top;
            font-size: 12PX;
        }
        .notice-inner {
            position: absolute;right: -75%;top: 0;
            background: transparent;
            width: 75%;
            animation-name:lunbo;animation-duration:12s;animation-iteration-count:infinite;
            z-index: 2;
        }
        .notice-list {
            width: 70%;
            display: inline-block;vertical-align: top;
        }
        .notice-item {
            // color: @bg-color;
            color: white;background: transparent;
            font-size: 24px;
            text-overflow: ellipsis;overflow: hidden;white-space: nowrap;
        }
    }
  .banner {
    width: 100%;height: 300px;
  }
  .list {
    margin: 0 4%;
    font-size: 0px;
    .list-con:nth-child(odd) {
      margin-right: 5%;
    }
    .list-con {
      width: 47.5%;
      display: inline-block;margin-top: 30px;padding-bottom: 29px;
      background: @bg-card-color;
      font-size: 22px;
      border-radius:8px;
      .img-wrap {
        text-align: center;
        .img {
          height:140px;
          margin-top: 41px;
        }
      }
      .con-wrap {
        padding: 19px 19px 25px 19px;
        color: #ddd;
        .title {
          padding-bottom: 10px;
          color: #f5be28;
          font-size: 28px;
        }
      }
      .type-btn {
        width: 82%;height: 68px;line-height: 68px;
        display: block;margin: auto;
        text-align: center;
        color: @bg-color-white;
        font-size: 32px;letter-spacing: 1px;
        border-radius: 8px;
      }
      .bg1 {
        background: linear-gradient(
          90deg,
          rgba(108, 234, 114, 1) 0%,
          rgba(26, 184, 0, 1) 98%
        );
      }
      .bg2 {
        background:rgba(68,66,90,1);
      }
      .bg3 {
        background: linear-gradient(
          90deg,
          rgba(245, 190, 40, 1) 0%,
          rgba(255, 132, 42, 1) 100%
        );
      }
      .bg4 {
        background: linear-gradient(
          -90deg,
          rgba(254, 45, 45, 1) 0%,
          rgba(255, 96, 96, 1) 100%
        );
      }
    }
  }
  .msg {
        width: 100%;
        text-align: center;
        margin-top: 20%;
  }
}
</style>
<style lang="less">
._v-container {
  height: 300px!important;
  margin-top: -40PX!important;
    z-index: 1;
}
._v-container>._v-content>.loading-layer .spinner-holder .spinner, ._v-container>._v-content>.pull-to-refresh-layer .spinner-holder .spinner{
    margin-top: 0!important;
    fill: white!important;
    stroke: white!important;
    z-index: 1;
}
</style>
