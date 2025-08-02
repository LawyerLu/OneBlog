<?php
/**
 * 微语页面
 *
 * @package custom
 */
 
if (!defined('__TYPECHO_ROOT_DIR__')) exit;
$this->need('header.php'); ?>
<meta name="csrf-token" content="<?php echo Helper::security()->getToken($this->request->getRequestUrl()); ?>">
<meta name="comment-url" content="<?php $this->commentUrl(); ?>">
<div class="main">
<?php $this->need('module/head2.php');?>

<!--背景图片+logo-->
<div class="page_thumb blur">
    <!-- 背景图片容器 -->
    <div class="post_bg lazy-load" data-src="<?php echo $this->fields->thumb ? $this->fields->thumb : resource_cdn() . 'img/memos.jpg';?>"></div>
    <div class="pc">
        <!-- 新增的菜单按钮 -->
        <i class="iconfont icon-nav menu-button"></i>
        <div class="page-head">
            <a class="logo" href="<?php $this->options->siteUrl(); ?>">
                <img src="<?php echo $this->options->logoWhite ? $this->options->logoWhite : resource_cdn() . 'img/logoWhite.svg'; ?>">
            </a>
        </div>
    </div>
    <div class="m">
        <h1 class="page-head"><?php $this->archiveTitle(' &raquo; ', ''); ?><span>A fleeting inspiration</span></h1> 
    </div>
    <div class="memos-btn">
        <?php if($this->user->hasLogin()): ?>
            <button id="publish-button">发布</button>
        <?php else: ?>
            <button id="login-button">登录</button>
        <?php endif; ?>
    </div>
</div>

<!--微语列表-->
    <div id="comments" class="memos padding animated fadeIn blur">
        <?php $this->comments()->to($comments); ?>
        <?php if ($comments->have()): ?>
            <ul class="comment-list">
                <?php while($comments->next()): ?>
                    <?php MemosList($comments, $this->user); ?>
                <?php endwhile; ?>
            </ul>
            <?php $comments->pageNav('', ''); ?>
            <!-- 加载更多按钮，仅PC端显示，移动端隐藏 -->
            <div class="load" id="load-more-comments">加载更多动态</div>
            
            <!-- 原有加载提示 -->
            <div id="loading-spinner" style="display: none;">
                <div class="spinner"></div><span>加载中...</span>
            </div>
            <div class="load" id="no-more" style="display: none;">—&nbsp;已加载全部数据&nbsp;—</div>
        <?php endif; ?>
    </div>


<a id="gototop" class="hidden"><i class="iconfont icon-up"></i></a>
</div>
<!--传给js处理-->
<script>
    var loginAction = "<?php echo $this->options->loginAction(); ?>";
    var commentLikeUrl = "<?php Helper::options()->index("?commentLike=dz"); ?>";
</script>
<?php $this->need('footer.php'); ?>
