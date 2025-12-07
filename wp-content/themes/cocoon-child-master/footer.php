<?php
/**
 * Cocoon Child Theme - Custom Footer
 * 3カラムレイアウト、SNSリンク、ダークテーマ対応
 */
if ( !defined( 'ABSPATH' ) ) exit; ?>
          </main>

        <?php get_sidebar(); ?>

      </div>

    </div>

    <?php
    ////////////////////////////
    //コンテンツ下部ウィジェット
    ////////////////////////////
    if ( is_active_sidebar( 'content-bottom' ) ) : ?>
    <div id="content-bottom" class="content-bottom wwa">
      <div id="content-bottom-in" class="content-bottom-in wrap">
        <?php dynamic_sidebar( 'content-bottom' ); ?>
      </div>
    </div>
    <?php endif; ?>

    <?php //投稿パンくずリストがフッター手前の場合
    if (is_single_breadcrumbs_position_footer_before()){
      cocoon_template_part('tmp/breadcrumbs');
    } ?>

    <?php //固定ページパンくずリストがフッター手前の場合
    if (is_page_breadcrumbs_position_footer_before()){
      cocoon_template_part('tmp/breadcrumbs-page');
    } ?>

    <?php //フッター前アクションフック
    do_action('cocoon_footer_before'); ?>

    <!-- ========================================
         カスタムフッター - 3カラムレイアウト
    ======================================== -->
    <footer id="footer" class="footer footer-container nwa enaga-footer" itemscope itemtype="https://schema.org/WPFooter">

      <div id="footer-in" class="footer-in wrap cf">

        <!-- カスタム3カラムフッター -->
        <div class="enaga-footer-columns">
          
          <!-- 左カラム: サイト情報 -->
          <div class="enaga-footer-column enaga-footer-about">
            <h3 class="enaga-footer-title">エナガブログについて</h3>
            <p class="enaga-footer-description">
              Kickstarterやクラウドファンディングに関する最新情報をお届けするブログです。
              海外のクリエイティブなプロジェクトを日本語で紹介しています。
            </p>
            <div class="enaga-footer-social">
              <a href="https://twitter.com/shimagiro50" class="social-icon" aria-label="Twitter" target="_blank" rel="noopener">
                <i class="fab fa-twitter"></i>
              </a>
              <a href="#" class="social-icon" aria-label="Instagram" target="_blank" rel="noopener">
                <i class="fab fa-instagram"></i>
              </a>
              <a href="#" class="social-icon" aria-label="Facebook" target="_blank" rel="noopener">
                <i class="fab fa-facebook"></i>
              </a>
              <a href="<?php echo esc_url(get_feed_link()); ?>" class="social-icon" aria-label="RSS">
                <i class="fas fa-rss"></i>
              </a>
            </div>
          </div>
          
          <!-- 中央カラム: カテゴリ一覧 -->
          <div class="enaga-footer-column enaga-footer-categories">
            <h3 class="enaga-footer-title">カテゴリ</h3>
            <ul class="enaga-footer-menu">
              <?php
              $categories = get_categories(array(
                'orderby' => 'count',
                'order' => 'DESC',
                'number' => 5
              ));
              foreach ($categories as $category) {
                echo '<li><a href="' . esc_url(get_category_link($category->term_id)) . '">' . esc_html($category->name) . '</a></li>';
              }
              ?>
              <li><a href="<?php echo esc_url(home_url('/archives')); ?>">記事一覧</a></li>
            </ul>
          </div>
          
          <!-- 右カラム: サイトリンク -->
          <div class="enaga-footer-column enaga-footer-links">
            <h3 class="enaga-footer-title">サイトリンク</h3>
            <ul class="enaga-footer-menu">
              <li><a href="<?php echo esc_url(home_url('/about')); ?>">このサイトについて</a></li>
              <li><a href="<?php echo esc_url(home_url('/contact')); ?>">お問い合わせ</a></li>
              <li><a href="<?php echo esc_url(home_url('/privacy-policy')); ?>">プライバシーポリシー</a></li>
              <li><a href="<?php echo esc_url(home_url('/sitemap')); ?>">サイトマップ</a></li>
            </ul>
          </div>
          
        </div>

        <!-- フッター下部 -->
        <div class="enaga-footer-bottom">
          <p>&copy; <?php echo date('Y'); ?> エナガブログ. All rights reserved.</p>
          <p>Powered by WordPress &amp; Cocoon</p>
        </div>

      </div>

    </footer>

    <?php //フッター後アクションフック
    do_action('cocoon_footer_after'); ?>

    <?php //管理者用パネル
    cocoon_template_part('tmp/admin-panel'); ?>

    <?php //モバイルヘッダーメニューボタン
    cocoon_template_part('tmp/mobile-header-menu-buttons'); ?>

    <?php //モバイルフッターメニューボタン
    cocoon_template_part('tmp/mobile-footer-menu-buttons'); ?>

    <?php //トップへ戻るボタンテンプレート
    cocoon_template_part('tmp/button-go-to-top'); ?>

    <?php if (!is_amp()) {
      //再利用用にフッターコードを取得
      global $_WP_FOOTER;
      ob_start();
      wp_footer();
      $f = ob_get_clean();
      echo $f;
      $_WP_FOOTER = $f;
    } ?>

    <?php //フッターで読み込むscriptをまとめたもの
    cocoon_template_part('tmp/footer-scripts');?>

  </div><!-- #container -->

</body>

</html>
