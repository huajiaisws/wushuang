<template>
    <div class="page">
        <m-header title="我的团队" :canback="Boolean(1)"></m-header>
        <section class="body">
            <ul class="list">
                <router-link class="item" :to="{name:'TeamList',query:{type:item.type}}" v-for="(item,index) in listArr" :key="index">
                    <span>{{item.name}}</span>
                   <i class="iconfont iconright"></i>
                </router-link>
            </ul>
        </section>
    </div>
</template>
<script>

export default {
    name:"PayInfo",
    data(){
        return {
            listArr:[
                {
                    name:'我的直推',
                    type:'zt'
                },
                {
                    name:'我的间推',
                    type:'jt'
                },
            ]
        }
    },

}
</script>
<style lang="less" scoped>
@import '~link-less';
    .body {
        background-color: @bg-color;
        padding: @padding-primary;
        color: white;
        .list {
            .item {
                .display-flex();
                justify-content: space-between;
                align-items: center;
                padding: @padding-primary 0;
                // border-bottom: 1px solid #ddd;
            }
        }
    }
</style>
