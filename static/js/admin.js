/**
 * Updated: 2026-01-21
 * Author: ©彼岸临窗 oneblog.net
 *
 * 注释含命名规范，开源不易，如需引用请注明来源:彼岸临窗 https://oneblog.net。
 * 本主题已取得软件著作权（登记号：2025SR0334142）和外观设计专利（专利号：第7121519号），请严格遵循GPL-2.0协议使用本主题及源码。
 */
 
/**分类tab**/
document.addEventListener('DOMContentLoaded', function () {
    // Tab 配置
    const tabs = [
        { id: 'tab1', label: '主题说明', selector: null }, // 第一个 Tab 是静态内容
        { id: 'base', label: '基础设置', selector: '[id*="logoStyle"],[id*="slogan"],[id*="logo"],[id*="logowhite"],[id*="MenuSet"],[id*="Favicon"],[id*="switch"],[id*="Banner"],[id*="Menu"],[id*="Tagbg"],[id*="Webthumb"],[id*="Webtime"],[id*="ICP"],[id*="WA"]'},
        { id: 'pro', label: '高级设置', selector: '[id*="dnsPrefetch"],[id*="imgSmall"],[id*="BeCode"],[id*="RandomIMG"],[id*="CFSiteKey"],[id*="CFSecret"],[id*="GeetestID"],[id*="GeetestKEY"]' },
        { id: 'social', label: '社交按钮', selector: '[id*="QQ"],[id*="Weixin"],[id*="Email"],[id*="Github"]' },
        { id: 'DIY', label: '样式定制', selector: '[id*="CSS"],[id*="JS"],[id*="themeColor"]' },
    ];
    const form = document.querySelector('form'); 
    const tabContainer = document.getElementById('tab-container');
    const tabNav = document.getElementById('tab-nav');
    const tabContent = document.getElementById('tab-content');

    // 将 Tab 容器移动到表单中
    form.insertBefore(tabContainer, form.firstChild);
    // 生成 Tab 导航
    tabs.forEach((tab, index) => {
        const li = document.createElement('li');
        const a = document.createElement('a');
        a.href = `#${tab.id}`;
        a.textContent = tab.label;
        a.addEventListener('click', (e) => {
            e.preventDefault();
            switchTab(tab.id);
        });
        li.appendChild(a);
        tabNav.appendChild(li);

        // 生成 Tab 内容区域（第一个 Tab 已经存在，跳过）
        if (tab.id !== 'tab1') {
            const pane = document.createElement('div');
            pane.id = tab.id;
            pane.classList.add('tab-pane');
            tabContent.appendChild(pane);
        }
    });

    // 将字段移动到对应的 Tab 内容区域
    tabs.forEach(tab => {
        if (tab.selector) {
            const fields = document.querySelectorAll(tab.selector);
            const pane = document.getElementById(tab.id);
            fields.forEach(field => {
                pane.appendChild(field.closest('.typecho-option')); // 将整个字段容器移动到 Tab 内容区域
            });
        }
    });

    // 默认显示第一个 Tab
    switchTab(tabs[0].id);

    // 切换 Tab 的函数
    function switchTab(tabId) {
        // 隐藏所有 Tab 内容
        document.querySelectorAll('.tab-pane').forEach(pane => {
            pane.classList.remove('active');
        });

        // 显示当前 Tab 内容
        document.getElementById(tabId).classList.add('active');

        // 更新 Tab 导航的激活状态
        document.querySelectorAll('#tab-nav li a').forEach(a => {
            a.classList.remove('active');
        });
        document.querySelector(`#tab-nav a[href="#${tabId}"]`).classList.add('active');
    }
});

/**图标依赖**/
// 动态加载 iconfont.css
function loadIconfont() {
      const link = document.createElement('link');
      link.rel = 'stylesheet';
      link.href = '//at.alicdn.com/t/c/font_3940454_lp08yxn46sl.css'; // ONEBLOG图标库
      document.head.appendChild(link);
    }

// 动态解析 iconfont.css
async function loadAndParseIconfont() {
  const response = await fetch('//at.alicdn.com/t/c/font_3940454_lp08yxn46sl.css');// ONEBLOG图标库
  const cssContent = await response.text();
  const iconRegex = /\.(icon-[^:]+):before\s*{\s*content:\s*"([^"]+)"/g;
  const icons = [];
  let match;

  while ((match = iconRegex.exec(cssContent)) !== null) {
    icons.push({
      className: match[1],
      unicode: match[2]
    });
  }

  return icons;
}

// 渲染图标
function renderIcons(icons) {
  const iconList = document.getElementById('iconList');

  icons.forEach(icon => {
    const iconItem = document.createElement('div');
    iconItem.className = 'icon-item';

    const iconElement = document.createElement('i');
    iconElement.className = `iconfont ${icon.className}`;

    const classNameElement = document.createElement('span');
    classNameElement.textContent = icon.className;

    iconItem.appendChild(iconElement);
    iconItem.appendChild(classNameElement);
    iconList.appendChild(iconItem);
  });
}

// 初始化
loadIconfont();
loadAndParseIconfont().then(icons => {
  renderIcons(icons);
});

// 主题色
document.addEventListener('DOMContentLoaded', function(){
  var inp = document.querySelector('input[name="themeColor"]');
  if(inp) inp.type = "color";
});

// 主题设置备份与恢复
jQuery(function($){
    const ajaxUrl = location.href.split('#')[0];
    function showResult(msg, icon, reload){
        if (typeof layer !== 'undefined') {
            layer.msg(msg, {icon: icon, time: 1200}, function(){
                if(reload){ location.reload(); }
            });
        } else {
            alert(msg);
            if (reload) location.reload();
        }
    }
    $(document).on('click', '#backupbtn', function(){
        if(typeof layer !== 'undefined'){
            layer.confirm('确定要备份当前主题配置吗？', {icon:3, title:'确认'}, function(index){
                layer.close(index);
                doBackup();
            });
        } else {
            if(confirm('确定要备份当前主题配置吗？')) doBackup();
        }
        function doBackup(){
            $.post(ajaxUrl, {action:'oneblog_theme_backup', _ajax:1}, function(res){
                if(res && res.success){
                    showResult(res.message, 1, true);
                }else{
                    showResult(res.message||'备份失败', 2);
                }
            }, 'json').fail(function(){
                showResult('请求失败，请检查网络', 2);
            });
        }
    });

    $(document).on('click', '#restorebtn', function(){
        if(typeof layer !== 'undefined'){
            layer.confirm('确定要恢复主题配置吗？<br><span style="color:red">恢复后会覆盖当前设置项，请谨慎！</span>', {icon:3, title:'确认'}, function(index){
                layer.close(index);
                doRestore();
            });
        } else {
            if(confirm('确定要恢复主题配置吗？恢复操作不可逆，请谨慎！')) doRestore();
        }
        function doRestore(){
            $.post(ajaxUrl, {action:'oneblog_theme_restore', _ajax:1}, function(res){
                if(res && res.success){
                    showResult(res.message, 1, true);
                }else{
                    showResult(res.message||'恢复失败', 2);
                }
            }, 'json').fail(function(){
                showResult('请求失败，请检查网络', 2);
            });
        }
    });

    function ensureUploadStyles(){
        if (document.getElementById('ob-upload-style')) return;
        const style = document.createElement('style');
        style.id = 'ob-upload-style';
        style.textContent = `
.ob-upload-input-wrap{position:relative;display:block;width:100%;}
.ob-upload-input-wrap>input[type="text"]{padding-right:78px;box-sizing:border-box;}
.ob-upload-btn{position:absolute;right:6px;top:50%;transform:translateY(-50%);height:28px;padding:0 10px;border:1px solid #dcdcdc;background:#f3f3f3;color:#333;border-radius:3px;font-size:12px;line-height:26px;cursor:pointer;}
.ob-upload-btn:hover{background:#ededed;}
.ob-upload-btn:disabled{opacity:.6;cursor:not-allowed;}
        `.trim();
        document.head.appendChild(style);
    }

    function bindUpload(fieldName){
        const $input = $(`input[name="${fieldName}"]`);
        if (!$input.length) return;
        if ($input.data('obUploadBind')) return;
        $input.data('obUploadBind', 1);

        ensureUploadStyles();

        $input.wrap('<span class="ob-upload-input-wrap"></span>');
        const $wrap = $input.parent();
        const $btn = $('<button type="button" class="ob-upload-btn">上传</button>');
        const $file = $('<input type="file" class="ob-upload-file" accept="image/*,.svg,.ico" style="display:none;">');
        $wrap.append($btn, $file);

        $btn.on('click', function(){
            $file.trigger('click');
        });

        $file.on('change', function(){
            const file = this.files && this.files[0];
            if (!file) return;

            const formData = new FormData();
            formData.append('action', 'oneblog_theme_upload_image');
            formData.append('_ajax', '1');
            formData.append('key', fieldName);
            formData.append('file', file);

            $btn.prop('disabled', true).text('上传中');
            $.ajax({
                url: ajaxUrl,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                dataType: 'json'
            }).done(function(res){
                if (res && res.success && res.url) {
                    $input.val(res.url).trigger('change');
                    showResult(res.message || '上传成功', 1);
                } else {
                    showResult((res && res.message) ? res.message : '上传失败', 2);
                }
            }).fail(function(){
                showResult('请求失败，请检查网络', 2);
            }).always(function(){
                $btn.prop('disabled', false).text('上传');
                $file.val('');
            });
        });
    }

    ['logo','logoWhite','Favicon','Tagbg','Webthumb','Weixin'].forEach(bindUpload);
});
    
