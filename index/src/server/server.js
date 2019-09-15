import axios from 'axios'
import qs from 'qs'

/**
 * 基础模块
 */
export const Base = new class {
    constructor(){
        this.uploadApi = '/api/user/upload';  //上传
        this.sendcodeApi = '/api/sms/send';//验证码
        this.imgcodeApi = '/api/Captcha/get'; //图形验证码
        this.bankApi = '/api/user/getbank';//银行
        this.download = '/api/app/info'; //下载
        this.uploadWgtApi = '/api/upload/upFile';//上传wgt
        this.downloadWgtAPi = '/api/upload/downfile';//下载资源更新
        this.getVersionAPi = '/api/upload/getVersion'; //获取服务器版本号
        this.getTextApi =  '/api/app/gettext'; //币与按钮名称
        this.dataApi = '/api/index/getImg'; //基础数据

    }
    getBank(){
        return axios.post(this.bankApi).then((res)=>res.data);
    }
    upload(params){
        return  axios.post(this.uploadApi,params).then((res)=>res.data);
    }
    sendCode(params){
        return axios.post(this.sendcodeApi,qs.stringify(params)).then((res)=>res.data); 
    }
    getImgCode(params){
        return axios.get(this.imgcodeApi,{params}).then((res)=>res.data); 
    }
    getDownload(){
        return axios.get(this.download).then((res)=>res.data); 
    }
    uploadWgt(params){
        return axios.post(this.uploadWgtApi,params).then((res)=>res.data); 
    }
    getVersion(){
        return axios.get(this.getVersionAPi).then((res)=>res.data); 
    }
    getText(){
        return axios.get(this.getTextApi).then((res)=>res.data); 
    }
    getData(){
        return axios.post(this.dataApi).then(res=>res.data)
    }
}

/**
 * 用户模块
 */
 export const Init = new class {
     constructor(){
        this.registerApi = '/api/user/register';  //注册
        this.changepwdApi = '/api/user/getbackpass';  //修改密码
        this.backpwdApi = '/api/user/getbackpass';  //忘记密码
        this.userApi = '/api/user/getinfo'; //用户信息
        this.capitalApi = ''; //用户资产
        this.realApi = '/api/user/changeisreal';  //实名认证
        this.loginApi = '/api/user/login';  //登录
        this.backTradeApi = '/api/user/uppaypsw';//修改交易密码
        this.quitApi = '/api/user/logout';//退出登录
    }
     register(params){
        return  axios.post(this.registerApi,qs.stringify(params)).then((res)=>res.data)
    }
    changePwd(params){
        return  axios.post(this.changepwdApi,qs.stringify(params)).then((res)=>res.data)
    }
    backpwd(params){
        return  axios.post(this.backpwdApi,qs.stringify(params)).then((res)=>res.data)
    }
    getUser(params){
        return  axios.post(this.userApi,qs.stringify(params)).then((res)=>res.data)
    }
    getCapital(params){
        return  axios.post(this.capitalApi,qs.stringify(params)).then((res)=>res.data)
    }
    toReal(params){
        return  axios.post(this.realApi,qs.stringify(params)).then((res)=>res.data)
    }
    login(params){
        return  axios.post(this.loginApi,qs.stringify(params)).then((res)=>res.data)
    }
    backTrade(params){
        return  axios.post(this.backTradeApi,qs.stringify(params)).then((res)=>res.data)
    }
    quit(){
        return  axios.get(this.quitApi).then((res)=>res.data)
    }
 }
 

/**
 * 个人中心
 */
export const Owner = new class {
    constructor(){
        this.bespeakListApi = '';//预约列表
        this.teamApi = '/api/user/team';//团队
        this.kjfeeApi = '/api/user/getkjfee';//充值矿机费率
        this.payInfoApi = '/api/user/payinfo';//支付信息
        this.bindaliApi = '/api/user/uppay'; //支付宝绑定
        this.bindwechatApi = '/api/user/upwechar'; //微信绑定
        this.userCoinApi= '/api/user/getUserCoin'; //资产模块
        this.coinOrderApi = '/api/user/getCoinOrder';//明细 
        this.coinApi = '/api/user/getCoin';//矿机与推广收益
        this.transferApi = '/api/user/transfer';//转出
        this.shaituApi = '/api/usershow/add';  //晒图
        this.rechargeInfoApi = '/api/user/getRechargeInfo'; //矿机充值信息
        this.rechargeApi = '/api/user/addRechargeLog'; //充值信息
        this.booklistApi = '/api/booking/booking/getlog'; //预约记录
        this.sellInfoApi = '/api/order/order/getSellPay'; //预约记录
        this.bindbankApi = '/api/user/upbank'; //银行卡绑定
        this.payApi= '/api/order/order/payment'; //支付
        this.diallistApi = '/api/turntable/getlist';  //转盘奖品
        this.rewardApi = '/api/turntable/getreward';  //奖品

        // 矿机记录
        this.buystutasApi = '/api/order/order/buyStutas'; //抢矿中列表
        this.buystutas2Api = '/api/order/order/buyStutas2'; //抢矿收益中列表
        this.buyappealApi = '/api/order/order/buyAppeal'; //抢矿申诉列表
        
        // 转让记录
        this.sellstatusApi = '/api/order/order/sellStatus'; //待转让列表
        this.sellstatus2Api = '/api/order/order/sellStatus2'; //转让中列表
        this.sellstatus4Api = '/api/order/order/sellStatus4'; //转让完成列表
        this.sellappealApi = '/api/order/order/sellAppeal'; //转让申诉列表
        // 申诉记录
        this.setappealApi = '/api/order/order/setAppeal';  //申诉
        this.buyerApi = '/api/order/order/payment'; //买家信息

        this.contractApi = '/api/order/order/renewContract'; //续约

        this.sellLogApi = '/api/order/sell/getselllog';  //收益出售记录   !!!!暂时不用！！！
        this.setSellApi = '/api/order/sell/setselllog';  //收益出售

        this.applyApi = '/api/wallet/apply'; //提取
        this.applyLogApi = '/api/wallet/lists'; //提取记录
        this.rateApi = '/api/wallet/getRate'; //兑换比例
        this.titleApi = '/api/notice/gettitles';  //公告
        this.noticeDetailApi = '/api/notice/details';  //公告详情

    }

    getTeam(params){
        return  axios.post(this.teamApi,qs.stringify(params)).then((res)=>res.data);
    }
    getPayInfo(params){
        return  axios.get(this.payInfoApi,{params}).then((res)=>res.data);
    }
    getUserCoin(params){
        return  axios.get(this.userCoinApi,{params}).then((res)=>res.data);
    }
    getCoinOrder(params){
        return  axios.get(this.coinOrderApi,{params}).then((res)=>res.data);
    }
    getCoin(params){
        return  axios.post(this.coinApi,qs.stringify(params)).then((res)=>res.data);
    }
    getTransfer(params) {
        return axios.post(this.transferApi,qs.stringify(params)).then(res => res.data);
    }
    shaitu(params) {
        return axios.post(this.shaituApi,qs.stringify(params)).then(res => res.data);
    }
    getRechargeInfo(params) {
        return axios.post(this.rechargeInfoApi,qs.stringify(params)).then(res => res.data);
    }
    recharge(params) {
        return axios.post(this.rechargeApi,qs.stringify(params)).then(res => res.data);
    }
    getBooklist(params) {
        return axios.get(this.booklistApi,{params}).then(res => res.data);
    }
    getSellInfo(params) {
        return axios.get(this.sellInfoApi,{params}).then(res => res.data);
    }
    bindAli(params) {
        return axios.post(this.bindaliApi,qs.stringify(params)).then(res => res.data);
    }
    bindwechat(params) {
        return axios.post(this.bindwechatApi,qs.stringify(params)).then(res => res.data);
    }
    getOrder(params) {
        return axios.get(this.orderListApi,{params}).then(res => res.data);
    }
    addBank(params) {
        return axios.post(this.bindbankApi,qs.stringify(params)).then(res => res.data);
    }
    buyStutas(params){
        return axios.get(this.buystutasApi,{params}).then(res => res.data);
    }
    buyStutas2(params){
        return axios.get(this.buystutas2Api,{params}).then(res => res.data);
    }
    buyAppeal(params){
        return axios.get(this.buyappealApi,{params}).then(res => res.data);
    }
    sellStatus(params){
        return axios.get(this.sellstatusApi,{params}).then(res => res.data);
    }
    sellStatus2(params){
        return axios.get(this.sellstatus2Api,{params}).then(res => res.data);
    }
    sellStatus4(params){
        return axios.get(this.sellstatus4Api,{params}).then(res => res.data);
    }
    sellAppeal(params){
        return axios.get(this.sellappealApi,{params}).then(res => res.data);
    }
    setAppeal(params){
        return axios.post(this.setappealApi,params).then(res => res.data);
    }
    minePay(params){
        return axios.post(this.payApi,params).then(res => res.data);
    }
    buyer(params){
        return axios.get(this.buyerApi,{params}).then(res => res.data);
    }
    getDialList(params){
        return axios.get(this.diallistApi,{params}).then(res => res.data);
    }
    getReward(params){
        return axios.get(this.rewardApi,{params}).then(res => res.data);
    }
    contract(params){
        return axios.post(this.contractApi,qs.stringify(params)).then(res => res.data);
    }
    sellLog(){
        return axios.get(this.sellLogApi).then(res => res.data);
    }
    setSell(params){
        return axios.post(this.setSellApi,qs.stringify(params)).then(res => res.data);
    }
    apply(params){
        return axios.post(this.applyApi,qs.stringify(params)).then(res => res.data);
    }
    getapplyList(params){
        return axios.get(this.applyLogApi,{params}).then(res => res.data);
    }
    getrate(){
        return axios.get(this.rateApi).then(res => res.data);
    }
    getKjfee(){
        return axios.get(this.kjfeeApi).then(res => res.data);
    }
    getTitle(params){
        return axios.get(this.titleApi,{params}).then(res=>res.data)
    }
    getNoticeDetail(params){
        return axios.get(this.noticeDetailApi,{params}).then(res=>res.data)
    }
}

/**
 *  帮助中心 
 */
export const Help = new class {
    constructor(){
        this.getCategoryListApi = '/api/question/getCategoryList';//全部问题
        this.getAllListApi = '/api/question/getAllList'
        this.getDetailApi = '/api/question/getDetail';//问题详情
        this.getCenterApi = '/api/question/getCenter';
        this.feedbackApi = '/api/question/addFeedback';  //提交问题

    }
    getCategoryList(params) {
        return axios.get(this.getCategoryListApi,{params}).then((res) => res.data)
    }
    getAllList(params) {
        return axios.post(this.getAllListApi,qs.stringify(params)).then((res) => res.data)
    }

    getDetail(params) {
        return axios.post(this.getDetailApi,qs.stringify(params)).then((res) => res.data)
    }
    getCenter(params) {
        return axios.get(this.getCenterApi,{params}).then((res) => res.data)
    }
    addFeedback(params) {
        return axios.post(this.feedbackApi,qs.stringify(params)).then((res) => res.data)
    }
}

/**
 * 主页
 */
export const Index = new class {
    constructor(){
        // this.fa_blockApi = '/api/user/fa_block';  //矿机这也列表
        this.fa_blockApi = '/api/ore/ore/getorelevel';  //矿机这也列表
        this.miningApi = '/api/mining/mining/index';  //抢矿
        this.oreResultApi = '/api/mining/mining/getOreResult';  //抢矿结果
        this.bookingApi = '/api/booking/booking/setBooking';  //预约
        this.countDownApi = '/api/ore/ore/countDown'; //抢矿倒计时
        this.titleApi = '/api/notice/gettitles';  //公告
        this.bannerAPi = 'api/question/getTopImg';   //banner     


    }
    getMine(){
        return axios.get(this.fa_blockApi).then((res) => res.data)
    }
    mining(params){
        return axios.get(this.miningApi,{params}).then((res) => res.data)
    }
    booking(params){
        return axios.get(this.bookingApi,{params}).then((res) => res.data)
    }
    getcountDown(params){
        return axios.post(this.countDownApi,qs.stringify(params)).then(res=>res.data)
    }
    getoreResult(params){
        return axios.post(this.oreResultApi,qs.stringify(params)).then(res=>res.data)
    }
    getTitle(params){
        return axios.get(this.titleApi,{params}).then(res=>res.data)
    }
    getBanner(){
        return axios.post(this.bannerAPi).then(res=>res.data)

    }
}