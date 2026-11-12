<?php
/**
 * 孜然超级外链工具 —— 首页（V1 经典服务端版）
 * 作者：孜然  QQ：2937978586  微信：ZR293797
 */

require __DIR__ . '/lib/common.php';
require __DIR__ . '/lib/layout.php';

$links = zr_links();
$total = count($links);

$hosts = [];
foreach ($links as $tpl) {
    $h = zr_host_of($tpl);
    if ($h !== '') {
        $hosts[$h] = true;
    }
}
$hostTotal = count($hosts);

$pageSize = (int)zr('page_size', 12);
$maxSize  = (int)zr('max_size', 30);
$wait     = (int)zr('page_wait', 12);
$batches  = $pageSize > 0 ? (int)ceil($total / $pageSize) : 0;

zr_page_start('在线批量外链工具 · 免费SEO外链发布', zr('site_desc'), 'index');
?>
<main class="wrap">

  <section class="hero">
    <span class="hero-badge"><i></i> 外链库已收录 <?php echo number_format($total); ?> 条 · 覆盖 <?php echo number_format($hostTotal); ?> 个站点</span>
    <h1>把网址丢进来，<em>剩下的交给蜘蛛</em></h1>
    <p class="lead">
      自动向大量站长查询类站点提交你的域名，留下查询记录供百度、谷歌、搜狗抓取，
      无需手动逐个访问，一条外链就是一次被收录的机会。
    </p>

    <form class="launch" id="launchForm" action="data.php" method="get" novalidate>
      <input type="hidden" name="p" value="1">
      <input type="hidden" name="size" value="<?php echo $pageSize; ?>">
      <div class="launch-field">
        <span class="launch-prefix">https://</span>
        <input type="text" id="dn" name="dn" maxlength="253" autocomplete="off" spellcheck="false"
               placeholder="www.example.com（只填域名，不要带 http:// 和末尾的 /）">
      </div>
      <button class="btn" type="submit" id="launchBtn">开始推广</button>
    </form>

    <p class="launch-hint">
      <span>每批 <?php echo (int)$pageSize; ?> 条，<?php echo (int)$wait; ?> 秒自动翻页</span>
      <span>支持二级目录部署，免数据库</span>
      <span>手机端同样可用</span>
    </p>

    <div class="hero-metrics">
      <div class="metric"><b><?php echo number_format($total); ?></b>外链模板</div>
      <div class="metric"><b><?php echo number_format($hostTotal); ?></b>目标站点</div>
      <div class="metric"><b><?php echo number_format($batches); ?></b>预计批次</div>
      <div class="metric"><b>0</b>元 / 免费</div>
    </div>
  </section>

  <div class="layout">
    <div class="stack">

      <section class="card">
        <div class="card-head">
          <span class="icon">原</span>
          <h2>工具原理</h2>
          <span class="spacer">为什么访问一遍就有外链？</span>
        </div>
        <div class="card-body">
          <p>　　网络上几乎所有站长查询站（爱站、站长之家、chinaz 的 SEO 查询、权重查询、友链检测等）都会把「谁查过什么域名」记录下来并生成查询结果页。搜索引擎抓取这些页面时，就会读到你的网址，于是形成一条外链。</p>
          <p>　　本工具做的事情很简单：把你填写的域名，批量填入这些查询站的查询参数里，逐条在浏览器中打开，相当于替你手工查了几千次，省掉几个小时的人工点击。</p>
          <ol>
            <li>你在首页输入域名，工具按批次生成对应的查询网址；</li>
            <li>浏览器在后台逐条打开这些查询地址，站点记录下你的域名；</li>
            <li>搜索引擎蜘蛛爬取这些查询页时，抓到你的链接，外链就这样慢慢积累起来。</li>
          </ol>
        </div>
      </section>

      <section class="card">
        <div class="card-head">
          <span class="icon">用</span>
          <h2>使用方法</h2>
          <span class="spacer">三步搞定</span>
        </div>
        <div class="card-body">
          <ol>
            <li><strong>输入域名</strong>：只填域名，例如 <code>www.example.com</code>，不要带 <code>http://</code>，末尾不要加 <code>/</code>。</li>
            <li><strong>点击开始推广</strong>：页面会自动逐页提交，每批之间倒计时，倒计时结束后自动进入下一批。</li>
            <li><strong>保持页面打开</strong>：中途切走或关闭页面，后台提交就会停止，进度也会丢失。</li>
          </ol>
          <p class="tips">提示：新站每天 1~2 次、老站每天 1 次即可；一次跑完整库大约需要 <?php echo (int)$batches; ?> 批，耗时约 <?php echo (int)ceil($batches * $wait / 60); ?> 分钟。</p>
        </div>
      </section>

      <section class="card">
        <div class="card-head">
          <span class="icon">注</span>
          <h2>注意事项</h2>
          <span class="spacer">先说清楚，别踩坑</span>
        </div>
        <div class="card-body">
          <ul>
            <li>外链工具只是<strong>辅助</strong>手段，用于让蜘蛛更快发现你的新页面，它不等于高质量外链。</li>
            <li>新站坚持每天做一到两次，大约一周左右能看到收录变化；老站请把精力放在内容与优质友链上。</li>
            <li>如果浏览器开启了拦截混合内容（HTTPS 页面加载 HTTP 资源），部分使用 <code>http://</code> 的查询站可能无法加载，属于正常现象，不影响其他链接。</li>
            <li>本工具不收集、不上传你的任何数据，域名只在当前浏览器与本站服务器之间传递。</li>
          </ul>
        </div>
      </section>

      <section class="card panel-tint">
        <div class="card-head">
          <span class="icon">问</span>
          <h2>常见问题</h2>
        </div>
        <div class="card-body">
          <h3>会不会被搜索引擎惩罚？</h3>
          <p>　　不会。工具模拟的是正常人工查询，产生的是查询结果页上的记录，并不是群发垃圾评论或站群外链。它的作用仅限于「引蜘蛛」，风险极低，但也不要把收录提升全部押在它身上。</p>
          <h3>为什么进度到一半停住了？</h3>
          <p>　　两个原因：一是浏览器拦截了部分 HTTP 资源；二是页面被切到后台，浏览器限制了定时器。把标签页放回前台、或用「立即下一批」手动推进即可。</p>
          <h3>可以放到二级目录吗？</h3>
          <p>　　可以。上传到 <code>/link/</code> 这样的二级目录后直接访问 <code>/link/</code> 即可，所有资源都使用相对路径。</p>
        </div>
      </section>

    </div>

    <aside class="side">
      <div class="stack">

        <section class="card">
          <div class="card-body author-card">
            <div class="author-avatar">孜</div>
            <div class="author-name"><?php echo zr_e(zr('author')); ?></div>
            <div class="author-role">开发者 / 站长 / 工具人</div>
            <div class="author-list">
              <div class="author-row"><span>QQ</span><a href="<?php echo zr_e(zr('qq_chat_url')); ?>" target="_blank" rel="nofollow noopener"><b><?php echo zr_e(zr('qq')); ?></b></a></div>
              <div class="author-row"><span>微信</span><b><?php echo zr_e(zr('wechat')); ?></b></div>
              <div class="author-row"><span>邮箱</span><a href="mailto:<?php echo zr_e(zr('email')); ?>"><?php echo zr_e(zr('email')); ?></a></div>
            </div>
          </div>
        </section>

        <section class="card">
          <div class="card-head">
            <span class="icon">码</span>
            <h2>扫码关注</h2>
          </div>
          <div class="card-body">
            <div class="qr-grid">
              <?php
                zr_qr_card(zr('qr_wechat'), zr('qr_wechat_text'), zr('qr_wechat_tip'));
                zr_qr_card(zr('qr_group'), zr('qr_group_text'), zr('qr_group_tip'));
              ?>
            </div>
          </div>
        </section>

        <section class="card panel-tint">
          <div class="card-head">
            <span class="icon">数</span>
            <h2>运行参数</h2>
          </div>
          <div class="card-body">
            <div class="author-list">
              <div class="author-row"><span>每批条数</span><b><?php echo (int)$pageSize; ?> 条</b></div>
              <div class="author-row"><span>批次间隔</span><b><?php echo (int)$wait; ?> 秒</b></div>
              <div class="author-row"><span>总批次</span><b><?php echo (int)$batches; ?> 批</b></div>
              <div class="author-row"><span>程序版本</span><b>V<?php echo zr_e(zr('version')); ?></b></div>
            </div>
            <p class="tips" style="margin-top:12px;margin-bottom:0">这些参数在 <code>config.php</code> 里改，改完立即生效。</p>
          </div>
        </section>

      </div>
    </aside>
  </div>

</main>

<script>
(function () {
    var form = document.getElementById('launchForm');
    var input = document.getElementById('dn');
    var btn = document.getElementById('launchBtn');

    function toast(msg, type) {
        var box = document.getElementById('toastBox');
        if (!box) { alert(msg); return; }
        var el = document.createElement('div');
        el.className = 'toast' + (type ? ' ' + type : '');
        el.textContent = msg;
        box.appendChild(el);
        setTimeout(function () { el.remove(); }, 2600);
    }

    function checkDomain(raw) {
        var d = String(raw || '').trim().replace(/^[a-z][a-z0-9+.\-]*:\/\//i, '').replace(/\/+$/, '');
        if (!d) return '请输入需要推广的域名';
        if (/[\/\\?#@\s]/.test(d)) return '只填域名即可，不要带路径、参数或空格';
        var labels = d.split('.');
        if (labels.length < 2) return '域名格式不正确，例如：www.example.com';
        for (var i = 0; i < labels.length; i++) {
            if (!labels[i] || labels[i].length > 63 || !/^[a-z0-9]([a-z0-9\-]*[a-z0-9])?$/i.test(labels[i])) {
                return '域名只能包含字母、数字和短横线';
            }
        }
        if (!/^(?:[a-z]{2,24}|xn--[a-z0-9\-]{2,59})$/i.test(labels[labels.length - 1])) {
            return '域名后缀不正确，例如：.com / .cn';
        }
        return '';
    }

    form.addEventListener('submit', function (e) {
        var msg = checkDomain(input.value);
        if (msg) {
            e.preventDefault();
            toast(msg, 'err');
            input.focus();
            return;
        }
        input.value = String(input.value).trim().replace(/^[a-z][a-z0-9+.\-]*:\/\//i, '').replace(/\/+$/, '').toLowerCase();
        btn.disabled = true;
        btn.textContent = '正在启动…';
    });

    input.addEventListener('keydown', function (e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            form.dispatchEvent(new Event('submit', { cancelable: true }));
        }
    });
})();
</script>

<?php zr_page_end(); ?>
