<template>
     <div class="layui-layout layui-layout-admin"  id="app">
        <div class="layui-header ymheader">
            <a href="/"><img class="logo layui-circle" src="./assets/images/logo.png"></a>
            <div class="searchTxt">
                <input type="text" class="layui-input" placeholder="搜索" @keyup.enter="search"/>
            </div>
            <div class="user-action">
               <a href="/">个人工具站 - By </a> <a href="http://www.yimo.link" target="_blank">易墨</a>
            </div>
        </div>
        <div class="layui-side layui-bg-black" id="leftMenu">
            <div class="layui-side-scroll">
                <v-header :menuType="1" :menuModel="menuItems" :allOpen="true"></v-header>
            </div>
        </div>
        <div class="layui-body layui-tab-content" id="rightContent">
            <router-view></router-view>
        </div>
        <div class="layui-footer footer footer-doc" id="contentFooter">
            <p>
                <a href="https://coding.net/u/yimocoding/p/metools/git" target="_blank">源码在此,拿走不谢。</a>
                <a @click="openMsg">少年需要留个言么？</a>
            </p>
        </div>
    </div>
</template>
<script>
export default {
    data (){
      return {
          menuItems:[]
      }
    },
    methods:{
        search(){
            layer.msg('不要了吧，这么点功能还搜索，后面再说~')
        },
        openMsg(){
            layer.open({            
                type:2,
                shade:0,
                skin:'layui-layer-molv',
                anim: 4,
                offset:'rb',
                area: ['400px', '500px'],
                title:'我是谁？',
                content:'/static/msg.html'});
        }
    },
    created(){
      this.menuItems=window.siteData.MenuItems;
    }
}
</script>