创建一个容器div例如


<div id='test'></div>
使用


$("#test').slide();
参数说明


image=["1.jpg","2.jpg"];
$("#text").slide({images:image,//图片数组
    autoPlay:true,//自动播放
    interval:6000,//图片间隔
    barColor:"#3498DB",//导航条颜色hiddenBar:false,//设置是否隐藏底部导航条hiddenControl:false//设置隐藏左右切换按钮
});