<?php if (!defined('__TYPECHO_ROOT_DIR__')) exit;
/**
 * Theme：OneBlog
 * Updated: 2026-01-22
 * Author: ©彼岸临窗 oneblog.net
 * 注释含命名规范，开源不易，如需引用请注明来源:彼岸临窗 https://oneblog.net。
 * 本主题已取得软件著作权（登记号：2025SR0334142）和外观设计专利（专利号：第7121519号），请严格遵循GPL-2.0协议使用本主题及源码。
 */
 
//主题版本号自动获取
function parseThemeVersion() {
    $indexFile = __DIR__ . '/index.php'; 
    $content = file_get_contents($indexFile);
    preg_match('/\* @version\s+([0-9.]+)/', $content, $matches);
    return $matches[1] ?? '1.0.0'; 
}

//主题自定义
function themeConfig($form) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['_ajax'])) {
        if (ob_get_length()) ob_clean();
        header('Content-Type:application/json; charset=utf-8');
        $theTheme = 'OneBlog';
        $db = Typecho_Db::get();
        $uploadDir = Helper::options()->uploadDir ?: 'usr/uploads';
        $uploadDir = rtrim($uploadDir, '/\\');
        $absUploadDir = __TYPECHO_ROOT_DIR__ . '/' . $uploadDir;
        if (!is_dir($absUploadDir)) {
            @mkdir($absUploadDir, 0755, true);
        }
        $backPath = $absUploadDir . '/BackupSetting_' . $theTheme . '.txt';
        $ret = ['success'=>false, 'message'=>'未知错误'];
        $action = $_POST['action'] ?? '';
        if ($action === 'oneblog_theme_backup') {
            $themeConfStr = $db->fetchRow($db->select()->from('table.options')->where('name = ?', 'theme:' . $theTheme))['value'];
            $ok = file_put_contents($backPath, $themeConfStr);
            $ret = $ok !== false
                ? ['success'=>true, 'message'=>'备份成功']
                : ['success'=>false, 'message'=>'备份失败，uploads 目录不可写'];
        } elseif ($action === 'oneblog_theme_restore') {
            if (file_exists($backPath)) {
                $str = file_get_contents($backPath);
                $updateThemeConQuery = $db->update('table.options')->rows(['value'=>$str])->where('name=?', 'theme:' . $theTheme);
                $ok = $db->query($updateThemeConQuery);
                $ret = $ok !== false
                    ? ['success'=>true, 'message'=>'恢复成功']
                    : ['success'=>false, 'message'=>'恢复失败，数据库操作异常'];
            } else {
                $ret = ['success'=>false, 'message'=>'未找到备份文件，无法恢复'];
            }
        } elseif ($action === 'oneblog_theme_upload_image') {
            $user = Typecho_Widget::widget('Widget_User');
            if (!$user || !$user->pass('administrator', true)) {
                $ret = ['success'=>false, 'message'=>'无权限操作'];
            } else {
                $allowedKeys = ['logo','logoWhite','Favicon','Tagbg','Webthumb','Weixin'];
                $key = trim((string)($_POST['key'] ?? ''));
                if ($key === '' || !in_array($key, $allowedKeys, true)) {
                    $ret = ['success'=>false, 'message'=>'不支持的字段'];
                } elseif (empty($_FILES['file']) || !is_array($_FILES['file'])) {
                    $ret = ['success'=>false, 'message'=>'未选择文件'];
                } else {
                    $file = $_FILES['file'];
                    $error = $file['error'] ?? UPLOAD_ERR_NO_FILE;
                    if ($error !== UPLOAD_ERR_OK) {
                        $ret = ['success'=>false, 'message'=>'上传失败（错误码：' . $error . '）'];
                    } else {
                        $tmpName = $file['tmp_name'] ?? '';
                        if (!$tmpName || !is_uploaded_file($tmpName)) {
                            $ret = ['success'=>false, 'message'=>'无效的上传文件'];
                        } else {
                            $origName = (string)($file['name'] ?? '');
                            $ext = strtolower(pathinfo($origName, PATHINFO_EXTENSION));
                            $allowedExt = ['jpg','jpeg','png','gif','webp','svg','ico'];
                            if ($ext === '' || !in_array($ext, $allowedExt, true)) {
                                $ret = ['success'=>false, 'message'=>'仅支持：' . implode(' / ', $allowedExt)];
                            } else {
                                $nameMap = [
                                    'logo' => 'logo',
                                    'logoWhite' => 'logoWhite',
                                    'Favicon' => 'favicon',
                                    'Tagbg' => 'Tagbg',
                                    'Webthumb' => 'Webthumb',
                                    'Weixin' => 'Weixin',
                                ];
                                $baseName = $nameMap[$key] ?? $key;
                                $baseName = preg_replace('/[^A-Za-z0-9_-]/', '', (string)$baseName);
                                if ($baseName === '') {
                                    $ret = ['success'=>false, 'message'=>'文件名不合法'];
                                } else {
                                    $themeDir = __DIR__;
                                    if (!is_dir($themeDir) || !is_writable($themeDir)) {
                                        $ret = ['success'=>false, 'message'=>'主题目录不可写：' . $themeDir];
                                    } else {
                                        $newName = $baseName . '.' . $ext;
                                        $destPath = $themeDir . DIRECTORY_SEPARATOR . $newName;
                                        if (file_exists($destPath) && !@unlink($destPath)) {
                                            $ret = ['success'=>false, 'message'=>'无法覆盖旧文件：' . $newName];
                                        } elseif (!move_uploaded_file($tmpName, $destPath)) {
                                            $ret = ['success'=>false, 'message'=>'保存失败，请检查目录权限'];
                                        } else {
                                            @chmod($destPath, 0644);
                                            $url = rtrim((string)Helper::options()->themeUrl, '/\\') . '/' . $newName;
                                            $ret = ['success'=>true, 'message'=>'上传成功', 'url'=>$url, 'filename'=>$newName];
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
        echo json_encode($ret);
        exit;
    }
    
    $theTheme = 'OneBlog';
    $db = Typecho_Db::get();
    $uploadDir = Helper::options()->uploadDir ?: 'usr/uploads';
    $uploadDir = rtrim($uploadDir, '/\\');
    $absUploadDir = __TYPECHO_ROOT_DIR__ . '/' . $uploadDir;
    if (!is_dir($absUploadDir)) {
        @mkdir($absUploadDir, 0755, true);
    }
    $backPath = $absUploadDir . '/BackupSetting_' . $theTheme . '.txt';

    $themeConfStr = $db->fetchRow($db->select()->from('table.options')->where('name = ?', 'theme:' . $theTheme))['value'];
    $backstr = file_exists($backPath) ? file_get_contents($backPath) : '';?>

    <link rel="stylesheet" href="https://cncdn.cc/oneblog/3.6.5/admin.css" type="text/css" />
    <script src="https://cncdn.cc/jquery/3.7.1/dist/jquery.min.js" type="text/javascript"></script>
    <script src="https://cncdn.cc/layer/3.1.1/layer.js" type="text/javascript"></script>
    <script src="<?php echo Helper::options()->themeUrl('static/js/admin.js'); ?>" type="text/javascript"></script>
    <div class="OneBlog"><h3>OneBlog 主题设置</h3></div>
    <div id="tab-container">
        <ul id="tab-nav"></ul>
        <div id="tab-content">
            <div id="tab1" class="tab-pane active">
                <h2>OneBlog V<?php echo parseThemeVersion();?></h2>
                <p>本主题精心打磨多年，且持续优化，现免费开源，致敬互联网社区开源精神，也致敬热爱生活和记录的我们。</p>
                <p>使用教程请前往<b></b>主题文档</b>：<a href="https://docs.oneblog.net" target="_blank">docs.oneblog.net</a> 获取，主题最新版本请前往Github仓库：<a href="https://github.com/cncodehub/OneBlog" target="_blank">OneBlog（最新）</a> 或 <a href="https://gitcode.com/cncdn/OneBlog" target="_blank">国内镜像仓库（延迟一天同步）</a>查看，记得★Star，既是对作者的支持，也方便记住来时的路。本主题几乎所有代码都清晰地注释了，因此博友们完全可以以OneBlog为基础二次开发或单独开发属于自己的主题，但希望大家注明来源，保留基本的版权信息。</p>
                <p>本主题目前仅有QQ交流群：<b>939170079</b>，其他均不是官方群组，此外，还可以通过<a href="https://litebbs.com" target="_blank">LiteBBS</a>讨论交流。本主题的介绍、后续的更新或周边插件的开发更新，除了QQ群公告，还会同步发布在<a href="https://litebbs.com" target="_blank">LiteBBS</a>，欢迎大家参与讨论。</p>
                <div class="backup">
                    <div class="backup-listen">
                        <b>主题设置备份与恢复：</b>
                        <?php if (strcmp($backstr, $themeConfStr) === 0): ?>
                        当前的配置信息与备份信息一致，无需备份或恢复。
                        <?php else: ?>
                        未备份或备份数据与当前设置不一致。<br>
                        <div class="backupbtn">若以备份数据为准，建议：<button id="restorebtn" type="button">恢复主题配置</button></div>
                        <div class="backupbtn">若以当前设置为准，建议：<button id="backupbtn" type="button">备份主题配置</button></div>
                        <?php endif; ?>
                    </div>
                </div>
                <p>主题图标库（可直接引用，如 "iconfont icon-home"）：</p>
                <div class="icon-list" id="iconList"></div>
            </div>
        </div>
    </div>
    <?php
    
    //—————————————————————————————————————— 基础设置 ——————————————————————————————————————
    //LOGO风格
    $logoStyle = new Typecho_Widget_Helper_Form_Element_Radio('logoStyle', array('text' => '文字logo','logo' => '图片logo'),'text', 'LOGO风格', '选择文字风格则logo处显示网站名称，选择图片风格则需要在下方设置logo地址');
    $form->addInput($logoStyle);
    
    //网站logo
    $logo = new Typecho_Widget_Helper_Form_Element_Text('logo', NULL, NULL, _t('深色版LOGO'), _t('请输入深色logo图片的url，填写后会显示在PC首页和移动端顶栏，建议尺寸：300×83'));
    $form->addInput($logo); 
    
    //夜间模式下的logo
    $logoWhite = new Typecho_Widget_Helper_Form_Element_Text('logoWhite', NULL, NULL, _t('浅色版LOGO'), _t('请输入浅白色logo图片的url，填写后会显示在黑夜模式下的移动端顶栏，建议尺寸：300×83'));
    $form->addInput($logoWhite);
    
    //网站slogan
    $slogan = new Typecho_Widget_Helper_Form_Element_Text('slogan', NULL, NULL, _t('网站slogan'), _t('一句话介绍网站，填写后会显示在独立页面的顶栏和首页的标题中。'));
    $form->addInput($slogan); 
    
    //网站favicon
    $Favicon = new Typecho_Widget_Helper_Form_Element_Text('Favicon', NULL, NULL, _t('Favicon'), _t('请输入网站favicon图片的url。'));
    $form->addInput($Favicon); 
    
    //自定义菜单
    $MenuSet = new Typecho_Widget_Helper_Form_Element_Textarea('MenuSet',NULL,NULL,_t('自定义菜单'),_t('每行一个菜单项，菜单项的参数用英文逗号隔开。格式：菜单项名称,链接,图标类名<br>示例：<br>首页,/,iconfont icon-home<br>相册,/photos,iconfont icon-pic')
    );
    $form->addInput($MenuSet);
 
    //首页杂志效果开关
    $switch = new Typecho_Widget_Helper_Form_Element_Radio('switch', array('on' => '显示','off' => '不显示'),'on', '首页是否显示Banner文章', '选择开启则需要填写下方的文章cid；PC端会在首页顶部显示杂志效果文章，移动端会在首页顶部显示幻灯片自动切换。');
    $form->addInput($switch);
    
    //首页杂志效果文章
    $Banner = new Typecho_Widget_Helper_Form_Element_Text('Banner', NULL, NULL, _t('首页banner文章cid'), _t('用英文逗号隔开，限3个,填需要显示在banner区域三篇文章的cid。'));
    $form->addInput($Banner);   
    
    //移动端标签归档页背景图
    $Tagbg = new Typecho_Widget_Helper_Form_Element_Text('Tagbg', NULL, NULL, _t('标签页背景图'), _t('填写后会在移动端标签归档页的顶部背景区域（分类归档页的背景图片直接在分类描述中填写图片链接即可）。'));
    $form->addInput($Tagbg); 
    
    //网站标识图
    $Webthumb = new Typecho_Widget_Helper_Form_Element_Text('Webthumb', NULL, NULL, _t('网站标识图'), _t('请填写图片地址，用于SEO优化，建议尺寸：1280×720'));
    $form->addInput($Webthumb); 
    
    //建站年份
    $Webtime = new Typecho_Widget_Helper_Form_Element_Text('Webtime', NULL, NULL, _t('建站年份'), _t('填写后显示在网站底栏，格式：2016，如果是今年刚建站，请勿填写。'));
    $form->addInput($Webtime);
    
    //ICP备案号
    $ICP = new Typecho_Widget_Helper_Form_Element_Text('ICP', NULL, NULL, _t('ICP备案号'), _t('如需要显示，请填写网站备案号。'));
    $form->addInput($ICP);   
    
    //公安备案号
    $WA = new Typecho_Widget_Helper_Form_Element_Text('WA', NULL, NULL, _t('公安备案号'), _t('如需要显示，请填写公安备案号，跳转链接请自行在footer.php中修改。'));
    $form->addInput($WA);

    //—————————————————————————————————————— 高级设置 ——————————————————————————————————————
    
    // 添加自定义 DNS 预解析域名字段
    $dnsPrefetch = new Typecho_Widget_Helper_Form_Element_Textarea('dnsPrefetch',NULL,NULL,_t('DNS预解析域名'),_t('请输入需要预解析的域名，每行一个。例如：<br>https://oneblog.net<br>https://cdn.oneblog.net')
    );
    $form->addInput($dnsPrefetch);
    
    // 缩略图参数
    $imgSmall = new Typecho_Widget_Helper_Form_Element_Text('imgSmall', NULL, NULL, _t('缩略图参数'), _t('填写服务端支持的缩略图参数（如 !small），需搭配 CDN 或云存储图片处理功能使用。<br>留空则显示原图,填写后文章列表的缩略图会自动携带该参数，请确保文章内的图片支持缩略图处理。'));
    $form->addInput($imgSmall);
    
    // 代码块美化
    $BeCode = new Typecho_Widget_Helper_Form_Element_Radio('BeCode', array('on' => '开启','off' => '不开启'),'on','代码块美化', '默认开启，开启后会美化代码区域，技术博客请开启，否则代码块会显示异常，纯生活记录类博客建议关闭。');
    $form->addInput($BeCode); 
    
    // 随机高清文艺图片源
    $RandomIMG = new Typecho_Widget_Helper_Form_Element_Radio('RandomIMG', array('oneblog' => '主题图库','off' => '关闭'),'off','随机高清缩略图', '设置后文章列表页在文章没有任何图片且没有单独设置封面时显示随机缩略图，如果想让文章详情页显示封面图，请编辑文章时填写自定义字段[文章封面]。');
    $form->addInput($RandomIMG);
    
    // Cloudflare Turnstile 前端 Site Key
    $cfSiteKey = new Typecho_Widget_Helper_Form_Element_Text('CFSiteKey', NULL, '', _t('Cloudflare Turnstile SiteKey'), _t('填写 Turnstile 的 sitekey（用于前端）。留空则不启用 CF。'));
    $form->addInput($cfSiteKey);

    // Cloudflare Turnstile Secret（用于服务器端验证）
    $cfSecret = new Typecho_Widget_Helper_Form_Element_Text('CFSecret', NULL, '', _t('Cloudflare Turnstile Secret'), _t('填写 Turnstile 的 secret（用于服务器端验证）。请妥善保管，不要公开。'));
    $form->addInput($cfSecret);
    
    // 评论极验验证
    $GeetestID = new Typecho_Widget_Helper_Form_Element_Text('GeetestID', NULL, NULL, _t('极验ID'), _t('如需开启评论提交前的极验验证，请填写极验后台生成的 验证ID'));
    $form->addInput($GeetestID);
    
    $GeetestKEY = new Typecho_Widget_Helper_Form_Element_Text('GeetestKEY', NULL, NULL, _t('极验KEY'), _t('如需开启评论提交前的极验验证，请填写极验后台生成的 验证KEY'));
    $form->addInput($GeetestKEY);
    
    
    //—————————————————————————————————————— 社交按钮 ——————————————————————————————————————

    $QQ = new Typecho_Widget_Helper_Form_Element_Text('QQ', NULL, NULL, _t('QQ'), _t('请填写完整的QQ群描述或QQ号描述，输入的内容会直接作为弹框消息显示。'));
    $form->addInput($QQ);   
    
    $Weixin = new Typecho_Widget_Helper_Form_Element_Text('Weixin', NULL, NULL, _t('微信公众号'), _t('请填写微信公众号或个人微信的二维码图片url，格式为:https://。'));
    $form->addInput($Weixin);   
    
    $Email = new Typecho_Widget_Helper_Form_Element_Text('Email', NULL, NULL, _t('邮箱'), _t('请填写站长邮箱。'));
    $form->addInput($Email);
    
    $Github = new Typecho_Widget_Helper_Form_Element_Text('Github', NULL, NULL, _t('Github'), _t('请填写Github地址。'));
    $form->addInput($Github);
    
    //—————————————————————————————————————— 自定义样式 ——————————————————————————————————————
    // 自定义CSS
    $CSS = new Typecho_Widget_Helper_Form_Element_Textarea('CSS',NULL,NULL,_t('自定义CSS'),_t('可以填写css，覆盖默认的样式，本css优先级最高。')
    );
    $form->addInput($CSS);
    
    // 自定义JS
    $JS = new Typecho_Widget_Helper_Form_Element_Textarea('JS',NULL,NULL,_t('自定义JS'),_t('请输入自定义js代码，填写后会直接加载至页脚。')
    );
    $form->addInput($JS);
    
    // 自定义主题色
    $themeColor = new Typecho_Widget_Helper_Form_Element_Text('themeColor',NULL,'#ff5050',_t('主题色'),_t('请选择主题色调。')
    );
    $form->addInput($themeColor);

}

//文章自定义字段
function themeFields($layout) { ?>
    <link rel="stylesheet" href="https://cncdn.cc/oneblog/3.6.5/admin.css" type="text/css" />
    <?php 
    $thumb = new Typecho_Widget_Helper_Form_Element_Text('thumb', NULL, NULL, _t('封面图片'), _t('此处填写后会让文章/独立页面详情样式显示为有封面图的样式效果，文章列表也会出现封面缩略图，搜索引擎抓取的也是该封面图。'));
 	$thumb->input->setAttribute('class', 'full-width-input');
    $layout->addItem($thumb); 
    
    $origin = new Typecho_Widget_Helper_Form_Element_Text('origin', NULL, NULL, _t('图片来源'), _t('请输入图片的版权所有人名称或网站名（如：Unsplash）'));
    $origin->input->setAttribute('class', 'full-width-input');
    $layout->addItem($origin);  
    
    $author = new Typecho_Widget_Helper_Form_Element_Text('author', NULL, NULL, _t('作者'), _t('不填则默认为原创文章，作者为账号本人。'));
    $author->input->setAttribute('class', 'full-width-input');
    $layout->addItem($author); 
}

//自定义菜单
function CustomMenu() {
    $menuItems = Typecho_Widget::widget('Widget_Options')->MenuSet;
    $hasIcon = '';
    $noIcon = '';
    if (!empty($menuItems)) {
        $lines = explode("\n", $menuItems);
        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line)) continue;
            @list($name, $url, $icon, $target) = array_pad(array_map('trim', explode(',', $line)), 4, '');
            if (empty($name) || empty($url)) continue;
            $targetAttr = ($target === '_blank') ? ' target="_blank" rel="noopener"' : '';
            $hasIcon .= sprintf(
                '<li><a href="%s"%s><i class="%s"></i>%s</a></li>',//移动端菜单格式
                htmlspecialchars($url),
                $targetAttr,
                htmlspecialchars($icon ?? ''),
                htmlspecialchars($name)
            );
            $noIcon .= sprintf(
                '<a href="%s"%s>%s</a>',//PC端底部菜单格式
                htmlspecialchars($url),
                $targetAttr,
                htmlspecialchars($name)
            );
        }
    }
    return [
        'hasIcon' => $hasIcon ? $hasIcon : '',
        'noIcon'  => $noIcon ? $noIcon : ''
    ];
}

//PC端右键菜单数据格式化
function getNZMenuData() {
    static $cachedData = null; // 添加静态缓存
    if ($cachedData !== null) return $cachedData;
    $menuItems = Typecho_Widget::widget('Widget_Options')->MenuSet;
    $data = [];
    if (!empty($menuItems)) {
        foreach (explode("\n", $menuItems) as $line) {
            $line = trim($line);
            if ($line === '') continue;
            $parts = array_map('trim', explode(',', $line, 4)); 
            if (count($parts) < 3) continue;
            $name = $parts[0] ?? '';
            $url = $parts[1] ?? '';
            $icon = $parts[2] ?? '';
            $target = ($parts[3] ?? '') === '_blank' ? '_blank' : '';
            if ($name === '' || $url === '') continue;
            $data[] = [
                'name' => htmlspecialchars($name, ENT_QUOTES, 'UTF-8'), 
                'url'  => htmlspecialchars($url, ENT_QUOTES, 'UTF-8'),
                'icon' => htmlspecialchars($icon, ENT_QUOTES, 'UTF-8'),
                'target' => $target
            ];
        }
    }
    return $cachedData = $data; 
}

//首页置顶banner文章 极致优化 只查询1次
function get_banner_data($options) {
    if ($options->switch != 'on') return [];
    
    $cids = array_filter(
        array_slice(
            preg_split('/[,\s]+/', $options->Banner ?? '', -1, PREG_SPLIT_NO_EMPTY),
            0, 3
        ),
        function($v) { return ctype_digit((string)$v) && $v > 0; }
    );
    if (!$cids) return [];

    $db = Typecho_Db::get();
    $cid_str = implode(',', $cids);
    $is_mysql = stripos($db->getAdapterName(), 'mysql') !== false;

    $query = $db->select()
        ->from('table.contents')
        ->where("cid IN ($cid_str)")
        ->where('type = ?', 'post');

    if ($is_mysql) {
        $query->order("FIELD(cid, $cid_str)");
    }
    $results = $db->fetchAll($query);

    if (!$is_mysql && count($results) > 1) {
        usort($results, function($a, $b) use ($cids) {
            return array_search($a['cid'], $cids) - array_search($b['cid'], $cids);
        });
    }
    return $results;
}

// 查询文章数最多的前10个标签
function getTopTags() {
    $db = Typecho_Db::get();
    
    // 直接查询前10个热门标签的名称和slug
    $query = $db->select('name', 'slug')
        ->from('table.metas')
        ->where('type = ?', 'tag')
        ->order('count', Typecho_Db::SORT_DESC)
        ->limit(10);
    
    $tags = $db->fetchAll($query);
    
    // 输出标签链接
    foreach ($tags as $tag) {
        $tagUrl = Typecho_Common::url('tag/' . urlencode($tag['slug']), Helper::options()->index);
        echo '<a href="' . $tagUrl . '"># ' . htmlspecialchars($tag['name']) . '</a>';
        echo "\n"; // 换行分隔
    }
}

//文章内图片标签自动解析为灯箱效果
function AutoLightbox($content) {
    if (empty($content)) {return $content;}
    $pattern = '/<img.*?src=["\'](.*?)["\'].*?>/i';
    if (!preg_match($pattern, $content)) {return $content;}
    $replacement = '<a data-fancybox="gallery" href="$1"><img class="lazy-load" data-src="$1" src="$1" /></a>';
    $content = preg_replace($pattern, $replacement, $content);
    return $content;
}

//表情短代码解析
function parseEmojis($content) {
    // null、false等都转为空字符串，防止报错
    $content = (string)$content;
    $emojiPath = Helper::options()->siteUrl.'usr/themes/OneBlog/static/img/emoji/';
    return preg_replace_callback('/\[emoji:([a-zA-Z0-9_]+)\]/', function($matches) use ($emojiPath) {
        $emojiName = $matches[1];
        return '<img class="biaoqing" src="' . $emojiPath . $emojiName . '.svg" alt="' . $emojiName . '">';
    }, $content);
}

//无插件阅读数，cookie保证阅读量真实性
function get_post_view($archive) {
    $cid = $archive->cid;
    $db = Typecho_Db::get();
    $prefix = $db->getPrefix();
    
    // 确保views字段存在
    try {
        $db->fetchRow($db->select()->from('table.contents')->where('cid = ?', $cid));
    } catch (Typecho_Db_Exception $e) {
        try {
            $db->query('ALTER TABLE ' . $prefix . 'contents ADD views INT DEFAULT 0;');
        } catch (Typecho_Db_Exception $e) {
            // 忽略重复字段错误
        }
    }

    // 双重验证字段
    $fieldCheck = $db->fetchRow($db->select()->from('table.contents')->where('cid = ?', $cid));
    if (!array_key_exists('views', $fieldCheck)) {
        try {
            $db->query('ALTER TABLE ' . $prefix . 'contents ADD views INT DEFAULT 0;');
        } catch (Typecho_Db_Exception $e) {
            echo 0;
            return;
        }
    }

    // 获取当前阅读数
    $row = $db->fetchRow($db->select('views')->from('table.contents')->where('cid = ?', $cid));
    $currentViews = (int) $row['views'];
    $shouldCount = false;
    $views = [];
    
    if ($archive->is('single')) {
        $cookieData = Typecho_Cookie::get('extend_contents_views');
        $views = $cookieData ? explode(',', $cookieData) : [];
        
        if (!in_array($cid, $views)) {
            $shouldCount = true;
            $db->query($db->update('table.contents')
                ->rows(['views' => $currentViews + 1])
                ->where('cid = ?', $cid));
            
            $views[] = $cid;
            Typecho_Cookie::set('extend_contents_views', implode(',', $views));
        }
    }
    $displayViews = $currentViews + ($shouldCount ? 1 : 0);
    echo formatNum($displayViews);
}

//格式化阅读数：≥1000 单位转化为k；≥10000 单位转化为W；最多显示10W+
function formatNum($num) {
	if ($num >= 100000) {
		$num = '10w+';
	} elseif ($num >= 10000) {
        $num = round($num / 10000 * 100) / 100 ;
        $num = round($num,1).' w';//四舍五入保留一位小数
    } elseif($num >= 1000) {
        $num = round($num / 1000 * 100) / 100 ;
        $num = round($num,1). ' k';//四舍五入保留一位小数
    } else {
        $num = $num;
    }
    return $num;
}

//文章发表时间格式化
function time_ago($date) {
    $timestamp = strtotime($date->format('Y-m-d H:i:s'));
    $current_time = time();
    $time_diff = $current_time - $timestamp;
    if ($time_diff < 60) {
        return $time_diff . "秒前";
    } elseif ($time_diff < 3600) {
        return floor($time_diff / 60) . "分钟前";
    } elseif ($time_diff < 86400) {
        return floor($time_diff / 3600) . "小时前";
    } elseif ($time_diff < 2592000) { // 30天以内
        return floor($time_diff / 86400) . "天前";
    } elseif ($time_diff < 31536000) { // 1年内
        return floor($time_diff / 2592000) . "个月前";
    } elseif ($time_diff < 94608000) { // 3年内
        return floor($time_diff / 31536000) . "年前";
    } else {
        return $date->format('Y/m/d'); // 超过3年显示具体日期
    }
}

//页面加载时间统计
function timer_start(){
    global $timestart;
    $mtime = explode( ' ', microtime() );
    $timestart = $mtime[1] + $mtime[0];
    return true;
}
timer_start();

//页面加载时间格式化为秒
function timer_stop($display = 0, $precision = 3){
    global $timestart, $timeend;
    $mtime = explode( ' ', microtime() );
    $timeend = $mtime[1] + $mtime[0];
    $timetotal = number_format( $timeend - $timestart, $precision );
    $r = $timetotal < 1 ? $timetotal  . " s" : $timetotal . " s";
    if($display){echo $r;}
    return $r;
}

//文章字数统计
function art_count ($cid){
    $db=Typecho_Db::get ();
    $rs=$db->fetchRow ($db->select ('table.contents.text')->from ('table.contents')->where ('table.contents.cid=?',$cid)->order ('table.contents.cid',Typecho_Db::SORT_ASC)->limit (1));
    echo mb_strlen($rs['text'], 'UTF-8');
}

//评论者等级
function dengji($i) {
    $db = Typecho_Db::get();
    $adminAuthorId = 1;

    // 如果邮箱为空，使用站长邮箱
    if (empty($i)) {
        $admin = $db->fetchRow($db->select('mail')->from('table.users')->where('uid = ?', $adminAuthorId));
        $i = $admin ? $admin['mail'] : null;
    }

    // 检查邮箱是否获取成功
    if (empty($i)) {
        echo '<span class="level">Lv.1</span>';
        return;
    }

    $author = $db->fetchRow($db->select('authorId')->from('table.comments')->where('mail = ?', $i)->limit(1));
    $authorId = $author ? $author['authorId'] : null;

    if ($authorId == $adminAuthorId) {
        echo '<span class="level owner">博主</span>';
        return;
    }

    $mail = $db->fetchRow($db->select(array('COUNT(cid)' => 'rbq'))->from('table.comments')->where('mail = ?', $i)->where('authorId = ?', '0'));
    $rbq = $mail ? $mail['rbq'] : 0; // 如果没有评论就是0

    if ($rbq < 3) {
        echo '<span class="level">Lv.1</span>';
    } elseif ($rbq < 10) {
        echo '<span class="level">Lv.2</span>';
    } elseif ($rbq < 20) {
        echo '<span class="level">Lv.3</span>';
    } elseif ($rbq < 30) {
        echo '<span class="level">Lv.4</span>';
    } elseif ($rbq < 40) {
        echo '<span class="level">Lv.5</span>';
    } else {
        echo '<span class="level owner">知己</span>';
    }
}

//替换默认的Gravatar头像地址为国内镜像源 QQ邮箱取用qq头像
function getGravatar($email, $s = 96, $d = 'mp', $r = 'g', $img = false, $atts = array()){
    preg_match_all('/((\d)*)@qq.com/', $email, $vai);
    if (empty($vai['1']['0'])) {
        $url = 'https://weavatar.com/avatar/';
        $url .= md5(strtolower(trim($email)));
        $url .= "?s=$s&d=$d&r=$r";
        if ($img) {
            $url = '<img src="' . $url . '"';
            foreach ($atts as $key => $val)
                $url .= ' ' . $key . '="' . $val . '"';
            $url .= ' />';
        }
    }else{
        $url = 'https://q2.qlogo.cn/headimg_dl?dst_uin='.$vai['1']['0'].'&spec=100';
    }
    return  $url;
}

//获取文章缩略图
function showThumbnail($widget){
    // 如果文章设置了缩略图，优先返回缩略图
    if ($widget->fields->thumb) {
        return $widget->fields->thumb;
    }
    // 如果文章内容有图片，返回第一张图片作为缩略图
    $content = $widget->content;
    preg_match_all('/<img.*?src=["\'](.*?)["\']/', $content, $matches);
    if (isset($matches[1][0])) {
        return $matches[1][0];
    }
    // 如果设置了随机缩略图
    if (Helper::options()->RandomIMG == 'oneblog'){
        $randomParam = '?t=' . time() . rand(1, 1000);
        return Helper::options()->themeUrl . '/api/img.php' . $randomParam;
    }
    // 如果没有任何图片，则返回NULL。
    return;
}

//挂载点赞路径 + Ajax评论
function themeInit($archive) {
    // 评论点赞
    if ($archive->request->is("commentLike=dz")) {
        commentLikes($archive);
    }
    
    // Ajax 评论
    if ($archive->request->isPost() && 
        !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
        strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
        
        Typecho_Plugin::factory('Widget_Feedback')->finishComment = function($comment) {
            if (ob_get_length()) ob_clean();
            header('Content-Type: application/json; charset=UTF-8');
            
            echo json_encode([
                'success' => true,
                'message' => $comment->status === 'waiting' ? '评论提交成功，请等待审核' :  '评论发表成功',
                'coid'    => $comment->coid
            ], JSON_UNESCAPED_UNICODE);
            exit;
        };
    }
}

//评论点赞 cookie保证点赞数量准确
function commentLikesNum($coid, &$record = NULL){
    $db = Typecho_Db::get();
    $callback = array(
        'likes' => 0,
        'recording' => false
    );
    if (array_key_exists('likes', $data = $db->fetchRow($db->select()->from('table.comments')->where('coid = ?', $coid)))) {
        $callback['likes'] = $data['likes'];
    } else {
        $db->query('ALTER TABLE `' . $db->getPrefix() . 'comments` ADD `likes` INT(10) NOT NULL DEFAULT 0;');
    }
    if (empty($recording = Typecho_Cookie::get('__typecho_comment_likes_record'))) {
        Typecho_Cookie::set('__typecho_comment_likes_record', '[]');
    } else {
        $callback['recording'] = is_array($record = json_decode($recording)) && in_array($coid, $record);
    }
    return $callback;
}

//评论点赞处理
function commentLikes($archive){
    $archive->response->setStatus(200); 
    $_POST['coid'];
    $_POST['behavior'];
    $loginState = Typecho_Widget::widget('Widget_User')->hasLogin();
    $res1 = commentLikesNum($_POST['coid'], $record);
    $num = 0;
    if(!empty($_POST['coid']) && !empty($_POST['behavior'])){
        $db = Typecho_Db::get();
        $prefix = $db->getPrefix();
        $coid = (int)$_POST['coid'];
        if (!array_key_exists('likes', $db->fetchRow($db->select()->from('table.comments')))) {
        $db->query('ALTER TABLE `' . $prefix . 'comments` ADD `likes` INT(30) DEFAULT 0;');
        }
        $row = $db->fetchRow($db->select('likes')->from('table.comments')->where('coid = ?', $coid));
        $updateRows = $db->query($db->update('table.comments')->rows(array('likes' => (int) $row['likes'] + 1))->where('coid = ?', $coid));
        if($updateRows){
            $num = $row['likes'] + 1;
            $state =  "success";
            array_push($record, $coid);
            Typecho_Cookie::set('__typecho_comment_likes_record', json_encode($record));
        }else{
            $num = $row['likes'];
            $state =  "error";
        }
    }else{
        $state = 'Illegal request';
    }  
    $archive->response->throwJson(array(
       "state" => $state,
       "num" => $num
    ));    
}

// 微语数据加载
function MemosList($comments, $user) { ?>
    <li class="animated fadeIn">
        <div id="<?php echo $comments->theId(); ?>">
            <div class="user">
                <?php
                $email = $comments->mail;
                $imgUrl = getGravatar($email);
                echo '<img class="avatar" src="' . $imgUrl . '">';
                ?>
                <div class="user-info">
                    <span class="name"><?php echo $comments->author(); ?></span>
                    <span class="date lite-black"><?php echo $comments->date('Y-m-d H:i'); ?></span>
                </div>
            </div>

            <?php echo $comments->content(); ?>

            <?php if (isMemosImageEnabled()) : ?>
                <?php $imgs = getMemosImages($comments->coid); ?>
                <?php if (!empty($imgs)) : ?>
                    <?php $count = count($imgs); ?>
                    <div class="memos-img-grid grid-<?php echo $count; ?>">
                        <?php foreach ($imgs as $img) : ?>
                            <?php $thumb = getMemosThumbUrl($img); ?>
                            <div class="memos-img-item">
                                <a href="<?php echo htmlspecialchars($img); ?>"
                                   data-fancybox="memos-<?php echo $comments->coid; ?>"
                                   data-caption="<?php echo $comments->content(); ?>">
                                    <img class="lazy-load"
                                         src="<?php echo htmlspecialchars($thumb); ?>"
                                         data-src="<?php echo htmlspecialchars($thumb); ?>">
                                </a>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            <?php endif; ?>

            <?php
            $commentLikes = commentLikesNum($comments->coid);
            $likes = $commentLikes['likes'];
            $recording = $commentLikes['recording'];
            ?>
            <div class="commentLike">
                <a class="commentLikeOpt"
                   id="commentLikeOpt-<?php echo $comments->coid; ?>"
                   href="javascript:;"
                   data-coid="<?php echo $comments->coid; ?>"
                   data-recording="<?php echo $recording; ?>">
                    <i id="commentLikeI-<?php echo $comments->coid; ?>"
                       class="<?php echo $recording ? 'iconfont icon-liked red' : 'iconfont icon-like red'; ?>"></i>
                    <span class="lite-black" id="commentLikeSpan-<?php echo $comments->coid; ?>"><?php echo $likes; ?></span>
                </a>
            </div>
        </div>
    </li>
<?php }

/**
 * 判断插件是否启用
 */
function isMemosImageEnabled()
{
    $export = Typecho_Plugin::export();
    return isset($export['activated']['MemosImage']);
}

/**
 * 获取微语图片（插件启用且表存在时才返回）
 */
function getMemosImages($coid)
{
    if (!isMemosImageEnabled()) {
        return [];
    }

    $db = Typecho_Db::get();
    $table = $db->getPrefix() . 'memos_img';

    $exists = $db->fetchRow($db->query("SHOW TABLES LIKE '{$table}'"));
    if (!$exists) {
        return [];
    }

    $row = $db->fetchRow(
        $db->select('imgs')->from('table.memos_img')->where('coid = ?', $coid)
    );

    if (!$row || empty($row['imgs'])) {
        return [];
    }

    $imgs = json_decode($row['imgs'], true);
    return is_array($imgs) ? array_slice($imgs, 0, 9) : [];
}

/**
 * 生成缩略图 URL
 */
function getMemosThumbUrl($url)
{
    $append = '';
    try {
        $plugin = Helper::options()->plugin('MemosImage');
        $append = (string)($plugin->cosThumbAppend ?? '');
    } catch (Throwable $e) {}

    $isLocalUpload = (strpos($url, '/usr/uploads/') !== false);

    if ($isLocalUpload) {
        $pi = pathinfo($url);
        if (empty($pi['extension'])) {
            return $url;
        }
        return $pi['dirname'] . '/' . $pi['filename'] . '-s.' . $pi['extension'];
    }

    return $append !== '' ? ($url . $append) : $url;
}


//修复评论区域xss注入漏洞 2026.1.13
function oneblog_comment_submit(){
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') return;
    // 仅在提交评论时执行安全检查
    if (!isset($_POST['text']) && !isset($_POST['author'])) return;
    // 通用清理器：移除换行和控制符
    foreach ($_POST as $k => &$v) {
        if (is_string($v)) {
            $v = str_replace(["\r", "\n", "\0"], '', $v);
        }
    }
    unset($v);
    // 清理 url 字段
    if (isset($_POST['url'])) {
        $url = trim($_POST['url']);
        if ($url === '') {
            $_POST['url'] = '';
        } else {
            // 拦截包含危险字符、禁止危险协议、并只允许 http(s) 或 //
            $hasBadChars = preg_match('/[\"\';<>\(\)\[\]\{\}]/', $url);
            $badProtocol = preg_match('#^(javascript|data|vbscript):#i', $url);
            $allowedScheme = preg_match('#^(https?://|//)#i', $url);
            if ($hasBadChars || $badProtocol || !$allowedScheme) {
                $_POST['url'] = '';
            } else {
                $_POST['url'] = $url;
            }
        }
    }
    // 清理 author / mail：移除引号与尖括号，避免属性注入
    foreach (['author', 'mail'] as $k) {
        if (isset($_POST[$k])) {
            $_POST[$k] = preg_replace('/[\"\<\>]/', '', trim((string)$_POST[$k]));
        }
    }
    // 同步清理$_REQUEST
    foreach (['author','mail','url','text'] as $k) {
        if (isset($_REQUEST[$k])) {
            $_REQUEST[$k] = $_POST[$k] ?? $_REQUEST[$k];
        }
    }
}
oneblog_comment_submit();

// 评论者链接新窗口打开 2026.1.13
function comment_author_link($comments) {
    $author = htmlspecialchars($comments->author, ENT_QUOTES, 'UTF-8');
    // 获取 url（兼容对象/数组）
    $url = '';
    if (is_object($comments) && isset($comments->url)) {
        $url = trim($comments->url);
    } elseif (is_array($comments) && isset($comments['url'])) {
        $url = trim($comments['url']);
    }
    if ($url !== '') {
        $href = htmlspecialchars($url, ENT_QUOTES, 'UTF-8');
        return '<a href="' . $href . '" target="_blank">' . $author . '</a>';
    }
    return $author;
}


// Ajax / 普通请求 通用错误输出
function oneblog_abort($msg) {
    if (
        !empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&
        strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest'
    ) {
        header('Content-Type: application/json; charset=UTF-8');
        echo json_encode([
            'success' => false,
            'message' => $msg
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }
    exit('<script>alert(' . json_encode($msg) . ');history.back();</script>');
}

/* Cloudflare Turnstile 验证 */
function verify_cf_token($token, $secret, $remoteIp = null) {
    if (empty($token)) return ['success' => false];

    $post = [
        'secret'   => $secret,
        'response' => $token
    ];
    if ($remoteIp) $post['remoteip'] = $remoteIp;

    $ch = curl_init('https://challenges.cloudflare.com/turnstile/v0/siteverify');
    curl_setopt_array($ch, [
        CURLOPT_POST           => true,
        CURLOPT_POSTFIELDS     => http_build_query($post),
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT        => 5,
        CURLOPT_SSL_VERIFYPEER => true,
        CURLOPT_SSL_VERIFYHOST => 2,
    ]);
    $resp = curl_exec($ch);
    curl_close($ch);

    return json_decode($resp, true) ?: ['success' => false];
}

/* Ajax 评论验证码校验 */
function oneblog_check_captcha($comment, $post, $result) {
    $options = Helper::options();
    $user = Typecho_Widget::widget('Widget_User');

    // 已登录用户跳过验证
    if ($user->hasLogin()) {
        return $comment;
    }

    /* ===== Cloudflare ===== */
    if (! empty($options->CFSiteKey) && !empty($options->CFSecret)) {
        if (empty($_POST['cf_token'])) {
            oneblog_abort('请先完成安全验证');
        }

        $res = verify_cf_token(
            $_POST['cf_token'],
            $options->CFSecret,
            $_SERVER['REMOTE_ADDR'] ?? null
        );

        if (empty($res['success'])) {
            oneblog_abort('安全验证未通过，请重试');
        }

        return $comment;
    }

    /* ===== Geetest ===== */
    if (! empty($options->GeetestID) && !empty($options->GeetestKEY)) {
        foreach (['lot_number','captcha_output','pass_token','gen_time'] as $k) {
            if (empty($_POST[$k])) {
                oneblog_abort('请完成极验验证');
            }
        }

        $sign = hash_hmac('sha256', $_POST['lot_number'], $options->GeetestKEY);
        $query = [
            'lot_number'     => $_POST['lot_number'],
            'captcha_output' => $_POST['captcha_output'],
            'pass_token'     => $_POST['pass_token'],
            'gen_time'       => $_POST['gen_time'],
            'sign_token'     => $sign
        ];

        $url = 'https://gcaptcha4.geetest.com/validate?captcha_id=' . $options->GeetestID;
        $ctx = stream_context_create([
            'http' => [
                'method'  => 'POST',
                'header'  => 'Content-Type: application/x-www-form-urlencoded',
                'content' => http_build_query($query),
                'timeout' => 5
            ]
        ]);

        $resp = @file_get_contents($url, false, $ctx);
        $json = json_decode($resp, true);

        if (empty($json['result']) || $json['result'] !== 'success') {
            oneblog_abort('极验验证未通过');
        }

        return $comment;
    }

    return $comment;
}

/* Hook 注册 */
Typecho_Plugin::factory('Widget_Feedback')->comment = 'oneblog_check_captcha';



// 从分类描述中提取封面图片和文本描述
function CatInfo($description, $defaultImage = '') {
    // 设置默认图片路径
    if (empty($defaultImage)) {
        $defaultImage = Helper::options()->themeUrl . '/static/img/bg.jpg';
    }
    
    $imageUrl = $defaultImage;
    $textDescription = '';
    
    if (!empty($description)) {
        // 提取第一个图片URL
        if (preg_match('/https?:\/\/[^\s]+/', $description, $matches)) {
            $imageUrl = htmlspecialchars($matches[0]);
        }
        
        // 移除所有图片URL后获取文本描述
        $textDescription = trim(preg_replace('/https?:\/\/[^\s]+/', '', $description));
        
        // 处理空文本描述的情况
        if (empty($textDescription)) {
            $textDescription = '暂无关于该分类的介绍';
        }
    } else {
        $textDescription = '暂无关于该分类的介绍';
    }
    
    return [
        'img' => $imageUrl,
        'info' => $textDescription
    ];
}

//附件页面和作者页面重定向到404页面
function redirect_404(){
    $request = Typecho_Request::getInstance();
    $pathInfo = $request->getPathInfo();
    // 使用正则表达式匹配路径
    if (preg_match('/^\/(attachment\/\d+|author\/\w+)/i', $pathInfo)) {
        // 调用 404 页面
        $options = Typecho_Widget::widget('Widget_Options');
        $url = $options->siteUrl . '404';
        header("Location: $url");
        exit;
    }
}

// 在页面加载之前调用
Typecho_Plugin::factory('Widget_Archive')->beforeRender = 'redirect_404';
