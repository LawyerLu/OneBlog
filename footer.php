<div class="footer pc">
    <div class="navigation"><!--底部菜单导航-->
        <?php if ($menu = CustomMenu()): ?>
        <?php echo $menu['noIcon']; ?>
        <?php endif; ?>
        <!--如果需要仅在网站底部额外增加页面路径，则按照以下格式增加即可：
        <a href="/archives">归档</a>
        -->
    </div>
    <div class="copyright">
        Copyright&copy;<?php if (!empty($this->options->Webtime)): echo $this->options->Webtime().'-'; ?><?php endif; ?><?php echo date('Y'); ?>&nbsp;&nbsp;All Rights Reserved.&nbsp;&nbsp;Load：<?php echo timer_stop();?><br>
            <?php if (!empty($this->options->WA)): ?>
                <img src="<?php echo resource_cdn() . 'img/beian.png'; ?>"/><a href="https://beian.mps.gov.cn" rel="nofollow noreferrer" target="_blank"><?php $this->options->WA(); ?></a>&nbsp;&nbsp;
            <?php endif; ?>
            <?php if (!empty($this->options->ICP)): ?>
                <a href="https://beian.miit.gov.cn/" target="_blank" rel="nofollow noreferrer"><?php $this->options->ICP(); ?></a><br>
            <?php endif; ?>
            Powered by <a href="https://typecho.org/" title="Typecho框架" target="_blank">Typecho</a> Theme is <a id="copyright-pc" href="https://oneblog.net/oneblog" title="自豪地使用OneBlog主题" target="_blank">OneBlog </a>         
            <div class="switch">
                <span>夜间模式</span>
                <input type="checkbox" id="night2" class="night-toggle">
                <label for="night2" class="switchBtn"></label>
            </div>
    </div>
    <div class="contact">
        <?php if (!empty($this->options->QQ)): ?>
        <a id="qq" title="QQ"><i class="iconfont icon-qq"></i></a>
        <?php endif; ?>
        <?php if (!empty($this->options->Weixin)): ?>
        <a id="wxmp" title="微信公众号"><i class="iconfont icon-wechat"></i></a>
        <?php endif; ?>
        <?php if (!empty($this->options->Email)): ?>
        <a id="tomail" title="博主邮箱"><i class="iconfont icon-mail"></i></a>
        <?php endif; ?>
        <?php if (!empty($this->options->Github)): ?>
        <a href="<?php $this->options->Github();?>" target="_blank" title="Github"><i class="iconfont icon-github"></i></a>
        <?php endif; ?>
    </div>
</div>


<?php $this->footer();?>
<script src="<?php echo resource_cdn() . 'sdk/jquery.min.js'; ?>"></script><!--基础依赖放在最前面-->
<script src="<?php echo resource_cdn() . 'sdk/fancybox3/jquery.fancybox.min.js'; ?>"></script><!--图片灯箱效果-->
<script src="<?php echo resource_cdn() . 'sdk/layer/layer.js'; ?>"></script>
<?php if ($this->is('index')):?>
<script src="<?php echo resource_cdn() . 'sdk/swiper/swiper-bundle.min.js'; ?>"></script>
<script>
var bannerSwitch = '<?= $this->options->switch === 'on' ? 'on' : 'off' ?>';
</script>
<?php endif;?>
<?php if ($this->is('post') || $this->is('page')): ?>
<?php if ($this->options->BeCode == 'on'):?>
<!--代码高亮逻辑-->
<script src="<?php echo resource_cdn() . 'sdk/highlightjs/highlight.min.js'; ?>"></script>
<script defer>
document.addEventListener('DOMContentLoaded', function () {
    const codeBlocks = document.querySelectorAll('pre code');
    const observer = new IntersectionObserver(function (entries) {
        entries.forEach(function (entry) {
            if (entry.isIntersecting) {
                hljs.highlightElement(entry.target);
                observer.unobserve(entry.target); 
            }
        });
    }, {
        rootMargin: '0px',
        threshold: 0.1 // 当代码块进入视口 10% 时触发，减少资源占用
    });

    codeBlocks.forEach(function (codeBlock) {
        observer.observe(codeBlock); 
        codeBlock.style.filter = 'none'; // 显示高亮后的代码块
    });
});
</script>
<?php endif;?>

<!--表情支持-->
<script src="<?php echo resource_cdn() . 'js/emoji.js'; ?>"></script>

<!--评论无限加载js-->
<script src="<?php echo resource_cdn() . 'js/comments.js'; ?>"></script>

<?php endif;?>

<script src="<?php echo resource_cdn() . 'js/main.js?v=3.6.2'; ?>"></script><!--主题js-->

<!-- 版权信息 -->
<div id="copyright-info" style="display: none;">
<p>开源不易，请尊重作者版权，保留基本的版权信息。</p>
</div>

<script type="text/javascript">
$(document).on('click', '#qq', function() {layer.msg('<?php $this->options->QQ();?>',{time:4000});});    	
$(document).on('click', '#wxmp', function() {layer.open({type: 1,title: false,closeBtn: 0,shadeClose: true,skin: 'layui-layer-nobg',area: ['auto'], content: '<img id= "mywxmp" style="width:20rem;height:20rem;display:block;" src="<?php $this->options->Weixin();?>">'});});
$(document).on('click', '#tomail', function() {layer.msg('联系邮箱：<?php $this->options->Email();?>',{time:4000});});    	
</script>
<!--自定义JS代码-->
<?php if (!empty($this->options->JS)): ?>
<?php $this->options->JS();?>
<?php endif; ?>
</body>
</html>
