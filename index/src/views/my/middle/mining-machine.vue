<template>
  <div class="page">
    <m-header :title="setTitle(type)" :canback="Boolean(1)">
      <i @click="goTo('TurnOut',type)">{{type=='credit1'?'转出':'出售'}}</i>
      <!-- <i @click="goTo('TurnOut',type)">{{type=='credit1'?'转出':''}}</i> -->
    </m-header>
    <div class="body">
      <div class="content">
        <!-- <div v-if="type== 'credit1'" class="mining-machine">当前</div>
        <div v-else class="mining-machine"> -->
        <div  class="mining-machine" >当前</div>
        <div  class="num">{{data?data.money:'0'}}</div>
        <!-- <div v-if="type== 'credit1'" class="num">{{data?data.money:'0'}}</div> -->
        <div v-if="type== 'credit1' && canRecharge == 1" class="recharge" @click="goTo('Recharge',type)">充值</div>

        <div v-if="type== 'credit3'" class="mining-machine">
          <div class="profit">
            <div>
              <div>推荐收益</div>
              <div class="num">{{data?data.tjsy:'0.00'}}</div>
            </div>
            <div>
              <div>团队收益</div>
              <div class="num">{{data?data.tdsj:'0.00'}}</div>
            </div>
          </div>
        </div>

      </div>
      <ul v-if="data&&data.data&&data.data.length>0">
        <li class="list" v-for="(item,index) in data.data" :key="index">
          <div class="mine-grab">
            <span>{{item.remark}}</span>
            <span :class="{'list-num1':Number(item.num)<0, 'list-num2':Number(item.num)>0 }">{{item.num}}</span>
          </div>
          <div>{{item.time}}</div>
        </li>
      </ul>
      <!-- <p v-else class="no-msg">没有更多了呢~</p> -->
      <next v-show="data&&data.data&&data.data.length==0" ref="next" :nomore="true"></next>

    </div>
  </div>
</template>
<script>
import { mapGetters } from "vuex";
import { Owner } from "@/server/server.js";
import {Load} from '@/assets/commonjs/utils.js';

export default {
  data() {
    return {
      type: null,
      data: null,
      canRecharge:0
    };
  },
  mounted() {
    this.initData();
    this.getCoin(this.type);
  },
  computed: {
    ...mapGetters(['uid','coinTxt'])
  },
  methods: {
    initData() {
      this.type = this.$route.query.type;
      Load.loadNext(this);
    },
    goTo(route, type) {
      this.$router.push({ name: route, query: { type } });
    },
    setTitle(type) {
      switch (type) {
        case "credit3":
          return this.coinTxt[type+'_text'];
          break;
        case "credit1":
          return this.coinTxt[type+'_text'];
          break;
      }
    },
    /**
     * 获取币种余额
     */
    getCoin(type) {
      let params = {
        id: this.uid,
        type
      };
      Owner.getCoin(params).then(res => {
        // if (res.code == 1) {
          this.data = res.data;
          this.canRecharge = res.data.kj_recharge;
        // } else {
        //   mui.toast(res.msg);
        //   return;
        // }
      });
    }
  }
};
</script>
<style lang="less" scoped>
@import "~link-less";
.page {
  background: @bg-color;
  .body {
    margin-top: 10px;
    padding: 10px 30px 30px;
    color: @bg-color-white;
    .content {
      //   height: 270px;
      .display-flex();
      flex-direction: column;justify-content: center;
      padding: 35px;
      background: @bg-card-color;
      border-radius: 8px;
      text-align: center;
      .mining-machine {
        font-size: 28px;
        // padding-bottom: 50px;
        .num {
          color: @primary-color;
          font-size: 28px;
        }
      }
      .profit {
        .display-flex();
        justify-content: space-between;
      }
      .num {
        margin-top: 15px;
        color: @font-primary-color;
        font-size: 45px;
      }
      .recharge {
        width: 50%;height: 70px;line-height: 70px;
        margin: auto;margin-top: 57px;
        background: @btn-primary-color;
        border-radius: 8px;
        text-align: center;
      }
    }
    .list {
      margin-top: 20px;
      padding: 20px 30px;
      background: #252436;
      border-radius: 8px;
      .mine-grab {
        .display-flex();
        justify-content: space-between;
        padding-bottom: 8px;
        .list-num1 {
          font-size: 32px;
          color: red;
        }
        .list-num2 {
          font-size: 32px;
          color: green;
        }
      }
    }
  }
}
</style>
