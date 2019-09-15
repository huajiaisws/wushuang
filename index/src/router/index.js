import Vue from 'vue';
import Router from 'vue-router';
import {Util} from '@/assets/commonjs/utils.js';
// import store from '../store/index';
// import { resolve } from 'url';

const notFoundPage=()=>import('@/views/404Page');
const Welcome=()=>import('@/views/welcome');
const UploadWgt=()=>import('@/views/uploadWgt');


//淘金区
const PanGold=()=>import('@/views/pan-gold/index.vue');
const Service=()=>import('@/views/service/index.vue');
const My=()=>import('@/views/my/index.vue');


// 淘金区模块  --start

const StartMining =resolve => require(['@/views/pan-gold/start-mining.vue'], resolve)
const BuyResule = resolve => require(['@/views/pan-gold/buy-result.vue'], resolve)
//申诉
const Appeal = resolve => require(['@/views/pan-gold/appeal.vue'], resolve)
const NoticeDetail = resolve => require(['@/views/pan-gold/noticeDetail.vue'], resolve)
const Notice = resolve => require(['@/views/pan-gold/notice.vue'], resolve)
// 淘金区模块  --end


//客服中心
const Customer = resolve => require(['@/views/service/customer.vue'], resolve)
const Help = resolve => require(['@/views/service/help.vue'], resolve)
const Problem = resolve =>require(['@/views/service/problem.vue'], resolve)
const QuestionDetails = resolve =>require(['@/views/service/question-details.vue'], resolve)
const QuestionList = resolve =>require(['@/views/service/question-list.vue'], resolve)


// 服务  ----start

// 服务  ----end



// 我的  --start
const Bestpeak = resolve => require(['@/views/my/record/bestpeak.vue'], resolve);
const Mine = resolve => require(['@/views/my/record/mine.vue'], resolve);
const Attorn = resolve => require(['@/views/my/record/attorn.vue'], resolve);
const Detail = resolve => require(['@/views/my/record/detail.vue'], resolve);
const Invite = resolve => require(['@/views/my/nav/invite.vue'], resolve);
const Certify = resolve => require(['@/views/my/nav/certify.vue'], resolve);
const PayInfo = resolve => require(['@/views/my/nav/pay/index.vue'], resolve);
const AddCard = resolve => require(['@/views/my/nav/pay/add-card.vue'], resolve);
const Bind = resolve => require(['@/views/my/nav/pay/bind.vue'], resolve);
const Team = resolve => require(['@/views/my/nav/team/index.vue'], resolve);
const TeamList = resolve => require(['@/views/my/nav/team/team-list.vue'], resolve);
const Set = resolve => require(['@/views/my/set.vue'], resolve);
const ShaiTu = resolve => require(['@/views/my/shaitu.vue'], resolve);
const Dial = resolve => require(['@/views/my/dial.vue'], resolve);
const BlockMine = resolve => require(['@/views/my/middle/block-mine.vue'], resolve);
const Extract =resolve => require(['@/views/my/middle/extract.vue'], resolve);
const ExtractRecord =resolve => require(['@/views/my/middle/extract-record.vue'], resolve)
const MiningMachine =resolve => require(['@/views/my/middle/mining-machine.vue'], resolve)
const TurnOut =resolve => require(['@/views/my/middle/turn-out.vue'], resolve)
const Recharge =resolve => require(['@/views/my/middle/recharge.vue'], resolve)
const CheckUpdate =resolve => require(['@/views/my/checkUpdate.vue'], resolve)

// 我的  --end


// 用户相关  -start
const SetPwd = resolve => require(['@/views/init/set-pwd.vue'], resolve);
const Login = resolve => require(['@/views/init/login.vue'], resolve);
const BackPwd = resolve => require(['@/views/init/back-pwd.vue'], resolve);
const Register = resolve => require(['@/views/init/register.vue'], resolve);

// 用户相关  -end

// 下载
const Download = resolve => require(['@/views/download.vue'], resolve)



Vue.use(Router);
let routes=[
    {
        path: '/',
        redirect:{name:'PanGold'}
    },
    {
        path: '/welcome',
        name:'Welcome',
        component:Welcome
    },
    {
        path: '/uploadwgt',
        name:'UploadWgt',
        component:UploadWgt
    },


    //淘金区
    {
        path:'/pan-gold',
        name:'PanGold',
        component:PanGold
    },
    {
        path:'/service',
        name:'Service',
        component:Service
    },
    {
        path:'/my',
        name:'My',
        component:My
    },
    {
        path:'/bestpeak',
        name:'Bestpeak',
        component:Bestpeak
    },
    {
        path:'/bestpeak',
        name:'Bestpeak',
        component:Bestpeak
    },
    {
        path:'/mine',
        name:'Mine',
        component:Mine
    },
    {
        path:'/attorn',
        name:'Attorn',
        component:Attorn
    },
    {
        path:'/detail',
        name:'Detail',
        component:Detail
    },
    {
        path:'/invite',
        name:'Invite',
        component:Invite
    },
    {
        path:'/certify',
        name:'Certify',
        component:Certify
    },
    {
        path:'/payinfo',
        name:'PayInfo',
        component:PayInfo
    },
    {
        path:'/addcard',
        name:'AddCard',
        component:AddCard
    },
    {
        path:'/team',
        name:'Team',
        component:Team
    },
    {
        path:'/teamlist',
        name:'TeamList',
        component:TeamList
    },
    {
        path:'/bind/:type',
        name:'Bind',
        component:Bind
    },
    {
        path:'/set',
        name:'Set',
        component:Set
    },
    {
        path:'/setpwd',
        name:'SetPwd',
        component:SetPwd
    },
    {
        path:'/shaitu',
        name:'ShaiTu',
        component:ShaiTu
    },
    {
        path:'/login',
        name:'Login',
        component:Login
    },
    {
        path:'/backpwd',
        name:'BackPwd',
        component:BackPwd
    },
    {
        path:'/register/:id/:mobile',
        name:'Register',
        component:Register
    },
    {
        path:'/dial',
        name:'Dial',
        component:Dial
    },
    {
        path:'/my/middle/block-mine',
        name:'BlockMine',
        component:BlockMine
    },
    {
        path:'/my/middle/extract',
        name:'Extract',
        component:Extract
    },
    {
        path:'/my/middle/extract-record',
        name:'ExtractRecord',
        component:ExtractRecord
    },
    {
        path:'/my/middle/mining-machine',
        name:'MiningMachine',
        component:MiningMachine
    },
    {
        path:'/my/middle/turn-out',
        name:'TurnOut',
        component:TurnOut
    },
    {
        path:'/my/middle/recharge',
        name:'Recharge',
        component:Recharge
    },
    {
        path:'/pan-gold/notice',
        name:'Notice',
        component:Notice
    },
    {
        path:'/pan-gold/noticeDetail/:id',
        name:"NoticeDetail",
        component:NoticeDetail
    },
    {
        path:'/my/checkupdate',
        name:"CheckUpdate",
        component:CheckUpdate
    },



// 淘金区模块  --start
    {
        path:'/pan-gold/start-mining/:level',
        name:'StartMining',
        component:StartMining
    },
    {
        path:'/pan-gold/buy-result',
        name:'BuyResule',
        component:BuyResule
    },
    {
        path:'/pan-gold/appeal',
        name:"Appeal",
        component:Appeal
    },


//客服中心
    {
        path:'/service/customer',
        name:'Customer',
        component:Customer
    },
    {
        path:'/service/help',
        name:'Help',
        component:Help
    },
    {
        path:'/service/problem',
        name:'Problem',
        component:Problem
    },
    {
        path:'/service/question-details',
        name:'QuestionDetails',
        component:QuestionDetails
    },
    {
        path:'/service/question-list',
        name:'QuestionList',
        component:QuestionList
    },
    
    {
        path:'/download',
        name:'Download',
        component:Download
    },






    {
        path:'*',
        name:'404Page',
        component: notFoundPage
    }
];
// routes = routes.concat(myCenterRouter);

let router = new Router({
    mode:"hash",
    routes: routes
});
router.beforeEach((to,from,next)=>{
    if(localStorage.getItem('user_id')){
        if(Util.isWhite(to.name)){
            if(to.name =='Login'){
                next({name:'PanGold'})
            }else {
                next({name:from.name});
            }
        }else {
            next();
        }
    }else {
        if(Util.isWhite(to.name)){
            next();
        }else {
            next({name:'Login'});
            // next({name:'Welcome'});
        }
    }
});

// router.beforeEach((to,from,next)=>{
//     if(localStorage.getItem('vip') ||to.name =='Login'){
//         next()
//     }else {
//         next({name:'Login'});
//     }
// });
export default router;
