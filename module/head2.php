<!-- 移动端侧栏菜单-->
<div class="menu">
    <div class="close m">
        <span id="close"><i class="iconfont icon-cancel"></i></span>
    </div>
    <?php if ($menu = CustomMenu()): ?>
        <?php echo $menu['hasIcon']; ?>
        <li class="pc"><a href="javascript:void(0);" onclick="openSearch();"><i class="iconfont icon-search"></i>搜索</a></li>
    <?php endif; ?>
    <div class="copyright m">
        <div class="switch">
            夜间模式<input type="checkbox" id="night1" class="switchBtn"><label for="night1" class="switchBtn"></label>
        </div>
        <span>©<?php if (!empty($this->options->Webtime)): echo $this->options->Webtime().'-'; ?><?php endif; ?><?php echo date('Y'); ?>&nbsp;&nbsp;<a href="<?php echo $this->options->siteUrl; ?>"><?php echo $this->options->title; ?></a></span>
        <span>Theme by <a id="copyright-m" href="https://oneblog.net/oneblog" title="自豪地使用OneBlog主题" target="_blank">OneBlog</a></span>
    </div>
</div>

<!--顶部菜单-->
<div class="header bg-white m">
    <i class="iconfont icon-nav"></i>
    <a class="logo" id="logo" href="<?php $this->options->siteUrl(); ?>" style="background-image:url('<?php echo $this->options->logo ? $this->options->logo : resource_cdn() . 'img/logo.svg'; ?>')"></a>
    <i id="search-btn" class="iconfont icon-search"></i>
</div>

<!--搜索弹框-->
<div class="search-layer">
    <button class="close-search pc"><i class="iconfont icon-cancel"></i></button>
    <div class="search">
        <h5 class="pc">搜索</h5>
        <form autocomplete="off" id="search" method="post" action="<?php $this->options->siteUrl(); ?>" role="search" class="search-form">
            <input type="text" name="s" class="input" placeholder="<?php _e('输入关键字搜索'); ?>" required />
            <button type="submit" class="search-icon">
                <span class="m">搜索</span>
                <i class="iconfont icon-search pc"></i>
            </button>
        </form>
    </div>
    <div class="tagscloud pc">
        <h5>标签</h5>
        <?php getTopTags(); ?>
    </div>
</div>
