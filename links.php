<?php
/**
 * 友情链接
 *
 * @package custom
 */
 
if (!defined('__TYPECHO_ROOT_DIR__')) exit;
$this->need('header.php'); ?>

<div class="main">
<?php $this->need('module/head2.php');?>

<!--背景图片+logo-->
<div class="page_thumb blur">
    <!-- 背景图片容器 -->
    <div class="post_bg lazy-load" data-src="<?php echo $this->fields->thumb ? $this->fields->thumb : resource_cdn() . 'img/friend.jpg';?>"></div>
    
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
        <h1 class="page-head"><?php $this->archiveTitle(' &raquo; ', ''); ?><span>My online friends</span></h1> 
    </div>
</div>
<div class="page-title animated fadeIn pc">
    <h1><?php $this->title(); ?></h1>   
</div>
<?php if (array_key_exists('Links', Typecho_Plugin::export()['activated'])):?>
    <div class="links padding blur">
        <?php Links_Plugin::output("
			<li class='link'>
				<a href='{url}' target='_blank'>
				    <img src='{image}' alt='{name}'/>
				    <div class='link-info'>
				        <h3>{name}</h3>
				        <span class='lite-black' title='{description}'>{description}</span>
				    </div>
				</a>
			</li>
		 ", 0); ?>
    </div>
    <?php else:?>
	<div class="nodata blur">
        <img src='<?php echo resource_cdn() . 'img/nodata.svg'; ?>'></img>
        <span>暂未启用Links插件，请先安装并启用该插件。</span>
    </div>
	<?php endif;?>
    <div class="post_content padding animated fadeIn blur">
        <h4 class="link-request">
            <span>#</span>
            友链要求
        </h4>
        <?php echo AutoLightbox($this->content);?>
    </div>
    <?php $this->need('comments.php'); ?>
    <a id="gototop" class="hidden"><i class="iconfont icon-up"></i></a>
</div>
<?php $this->need('footer.php'); ?>
