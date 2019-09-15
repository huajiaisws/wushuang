<template>
  <div class="page">
    <m-header title="服务中心" ></m-header>
    <section class="body">
      <img class="service-img" :src="proData && api+proData.service_bg||defaultSrc">
      <div class="help" @click="goTo('Help')">
        <img class="img" src="../../assets/images/gzlz/icon_help.png">
        <div>帮助中心</div>
      </div>
      <div class="customer" @click="goTo('Customer')">
        <img class="img" src="../../assets/images/gzlz/icon_service.png">
        <div>客服中心</div>
      </div>
    </section>
    <m-Footer></m-Footer>
  </div>
</template>
<script>
import {mapGetters} from 'vuex';
export default {
  data(){
    return {
       defaultSrc:require('@/assets/images/gzlz/service-img.png')
    }
  },
  computed:{
    ...mapGetters(['api','proData'])
  },
  methods: {
    goTo(routeName) {
      this.$router.push({ name: routeName });
    }
  }
};
</script>
<style lang="less" scoped>
@import "~link-less";
.body {
  background: @bg-color;
  .display-flex();
  flex-direction: column;
  align-items: center;
  justify-content: center;
  font-size: 32px;
  color: @bg-color-white;
  .service-img {
    width: 71%;
  }
  .help,
  .customer {
    width: 76%;
    height: 128px;
    .display-flex();
    justify-content: center;
    align-items: center;
    border-radius: 8px;
    background: @btn-primary-color;
    .img {
      width: 50px;
      margin-right: 32px;
    }
  }
  .help {
    margin-top: 96px;
  }
  .customer {
    margin-top: 70px;
  }
}
</style>
