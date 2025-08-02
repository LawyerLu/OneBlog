<?php
/**
 * 相册页面
 * @package custom
 * 本页面依赖于OneBlog主题定制插件：Album
 */
if (!defined('__TYPECHO_ROOT_DIR__')) exit;
$this->need('header.php');
$currentPage = max(1, intval($this->request->get('page', 1)));
$this->widget('Album_Widget_Photo@photoList', 'page=' . $currentPage)->to($photos);
?>
<div class="main">
    <?php $this->need('module/head2.php');?>
    <!--背景图片+logo-->
    <div class="page_thumb blur">
        <!-- 背景图片容器 -->
        <div class="post_bg lazy-load" data-src="<?php echo $this->fields->thumb ? $this->fields->thumb : resource_cdn() . 'img/photo.jpg'; ?>"></div>
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
            <h1 class="page-head"><?php $this->archiveTitle(' &raquo; ', ''); ?><span>Scenery along the way</span></h1> 
        </div>
    </div>
    <div class="page-title animated fadeIn pc">
        <h1><?php $this->title(); ?></h1>   
    </div>
    <div class="photo-contain blur animated fadeIn">
        <?php if (array_key_exists('Album', Typecho_Plugin::export()['activated'])):?>
        <!--相册-->
        <div class="photos" id="photos">
            <?php for ($photos->rewind(); $photos->valid(); $photos->next()): ?>
            <div class="photo image-shadow">
                <a href="<?= $photos->rawUrl ?>" data-fancybox="gallery" 
                   data-caption="<?= $photos->title ?>&nbsp;&nbsp;&nbsp;&nbsp;<?= $photos->caption ?>&nbsp;&nbsp;&nbsp;&nbsp;<?= date('M d, Y', $photos->date) ?>">
                    <img class="lazy-load" data-src="<?= $photos->url ?>" />
                </a>
            </div>
            <?php endfor; ?>
        </div>
        <!-- 点击无限加载 -->
        <div class="load" id="loadmore">
            <?php if ($photos->haveNextPage()): ?>
                <a href="?ajax=1&page=<?php echo $currentPage + 1; ?>" class="next">点击查看更多</a>
            <?php else: ?>
                —&nbsp;&nbsp;&nbsp;暂无更多内容&nbsp;&nbsp;&nbsp;—
            <?php endif; ?>
        </div>
        <?php else:?>
    	<div class="nodata">
            <img src='<?php echo resource_cdn() . 'img/nodata.svg'; ?>'></img>
            <span>暂未启用相册插件，请先安装并启用该插件。</span>
        </div>
    	<?php endif;?>
    </div>
</div>
<a id="gototop" class="hidden"><i class="iconfont icon-up"></i></a>
</div>
<?php $this->need('footer.php'); ?>
