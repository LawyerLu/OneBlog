<?php
/**
 * 归档页面
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
    <div class="post_bg lazy-load" data-src="<?php echo $this->fields->thumb ? $this->fields->thumb : resource_cdn() . 'img/archives.jpg';?>"></div>
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
        <h1 class="page-head"><?php $this->archiveTitle(' &raquo; ', ''); ?><span>Blog's Archives</span></h1> 
    </div>
</div>
<div class="page-title animated fadeIn pc">
    <h1><?php $this->title(); ?></h1>   
</div>

<!-- 全部文章（不再排除任何分类） -->
<section class="archives padding animated fadeIn blur">
    <?php 
    $this->widget('Widget_Contents_Post_Recent', 'pageSize=10000')->to($archives);
    $articlesByYear = [];
    
    while ($archives->next()) {
        $year = date('Y', $archives->created);
        if (!isset($articlesByYear[$year])) {
            $articlesByYear[$year] = [];
        }
        $articlesByYear[$year][] = [
            'title' => $archives->title,
            'permalink' => $archives->permalink,
            'date' => date('m月d日', $archives->created)
        ];
    }
    
    foreach ($articlesByYear as $year => $articles) {
        echo '<h3><span>#</span>' . $year . '年</h3>';
        foreach ($articles as $article) {
            echo '<li><a href="' . $article['permalink'] . '"><span>' . $article['date'] . '</span>' . $article['title'] . '</a></li>';
        }
    }
    ?>
</section>

<!-- 全部独立页面 -->
<section class="archives padding animated fadeIn blur">
    <h3><span>#</span>页面</h3>
    <?php 
    $this->widget('Widget_Contents_Page_List')->to($pages);
    while ($pages->next()) {
        echo '<li><a href="' . $pages->permalink . '">' . $pages->title . '</a></li>';
    }
    ?>
</section>

<!-- 全部分类 -->
<section class="archives padding animated fadeIn blur">
    <h3><span>#</span>分类</h3>
    <?php 
    $this->widget('Widget_Metas_Category_List')->to($categories);
    while ($categories->next()) {
        echo '<li><a href="' . $categories->permalink . '">' . $categories->name . '</a></li>';
    }
    ?>
</section>

<!-- 全部标签 -->
<section class="archives padding animated fadeIn blur">
    <h3><span>#</span>标签</h3>
    <div class="tags">
        <?php 
        $this->widget('Widget_Metas_Tag_Cloud', 'ignoreZeroCount=1')->to($tags);
        while ($tags->next()) {
            echo '<a href="' . $tags->permalink . '">' . $tags->name . '</a>';
        }
        ?>
    </div>
</section>

    <div class="load blur" >— END —</div>
    <a id="gototop" class="hidden"><i class="iconfont icon-up"></i></a>
</div>
<?php $this->need('footer.php'); ?>
