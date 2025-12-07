<?php //子テーマ用関数
if ( !defined( 'ABSPATH' ) ) exit;

//子テーマ用のビジュアルエディタースタイルを適用
add_editor_style();

//以下に子テーマ用の関数を書く

// ヒーローセクション用スクリプト読み込み
function enqueue_hero_scripts() {
  if ( is_front_page() || is_home() ) {
    // particles.js (CDN)
    wp_enqueue_script( 'particles-js', 'https://cdn.jsdelivr.net/particles.js/2.0.0/particles.min.js', array(), '2.0.0', true );
    
    // 自作アニメーションスクリプト
    wp_enqueue_script( 'hero-js', get_stylesheet_directory_uri() . '/js/hero.js', array('particles-js'), '1.0.3', true );
  }
}
add_action( 'wp_enqueue_scripts', 'enqueue_hero_scripts' );

// ヘッダー機能スクリプト読み込み（全ページ）
function enqueue_header_scripts() {
    wp_enqueue_script( 
        'header-js', 
        get_stylesheet_directory_uri() . '/js/header.js', 
        array(), 
        '1.0.0', 
        true 
    );
}
add_action( 'wp_enqueue_scripts', 'enqueue_header_scripts' );

// ヒーローセクション表示（安全な直接出力版）
function add_hero_section() {
    // フロントページとホームでのみ表示
    if ( is_front_page() || is_home() ) {
        ?>
        <div id="hero-section" class="hero-section">
          <div class="hero-bg" style="background-image: url('<?php echo get_stylesheet_directory_uri(); ?>/images/hero-bg.jpg');"></div>
          <div class="hero-overlay"></div>
          <div id="particles-js"></div>
        </div>
        <script>
        // レイアウト調整：グローバルメニューをヘッダーの直下に、ヒーローをその後に配置
        document.addEventListener('DOMContentLoaded', function() {
            var header = document.getElementById('header') || document.querySelector('.header-container');
            var navi = document.getElementById('navi');
            var hero = document.getElementById('hero-section');
            
            if (header && navi) {
                // グローバルメニューをヘッダーの直後に移動
                header.parentNode.insertBefore(navi, header.nextSibling);
                navi.classList.add('navi-moved');
            }
            
            if (hero && navi) {
                // ヒーローをグローバルメニューの直後に移動
                navi.parentNode.insertBefore(hero, navi.nextSibling);
                hero.classList.add('moved-to-top');
            }
        });

        // 既存のアニメーションスクリプト
        document.addEventListener('DOMContentLoaded', function() {
          // パララックス効果を無効化（通常スクロールに統一）
          // const heroBg = document.querySelector('.hero-bg');
          // if (heroBg) {
          //   window.addEventListener('scroll', function() {
          //     const scrollPosition = window.pageYOffset;
          //     heroBg.style.transform = 'translate3d(0, ' + (scrollPosition * 0.4) + 'px, 0)';
          //   });
          // }
          
          // particles.js
          if (typeof particlesJS !== 'undefined' && document.getElementById('particles-js')) {
            particlesJS('particles-js', {
              "particles": {
                "number": { "value": 30, "density": { "enable": true, "value_area": 800 } },
                "color": { "value": ["#ffffff", "#00C6FF", "#8E2DE2"] },
                "shape": { "type": ["circle", "triangle"], "stroke": { "width": 0, "color": "#000000" } },
                "opacity": { "value": 0.5, "random": true, "anim": { "enable": true, "speed": 1, "opacity_min": 0.1, "sync": false } },
                "size": { "value": 4, "random": true, "anim": { "enable": false, "speed": 40, "size_min": 0.1, "sync": false } },
                "line_linked": { "enable": true, "distance": 150, "color": "#ffffff", "opacity": 0.3, "width": 1 },
                "move": { "enable": true, "speed": 2, "direction": "none", "random": true, "straight": false, "out_mode": "out", "bounce": false, "attract": { "enable": false, "rotateX": 600, "rotateY": 1200 } }
              },
              "interactivity": {
                "detect_on": "canvas",
                "events": { "onhover": { "enable": true, "mode": "grab" }, "onclick": { "enable": true, "mode": "push" }, "resize": true },
                "modes": { "grab": { "distance": 140, "line_linked": { "opacity": 1 } }, "bubble": { "distance": 400, "size": 40, "duration": 2, "opacity": 8, "speed": 3 }, "repulse": { "distance": 200, "duration": 0.4 }, "push": { "particles_nb": 4 }, "remove": { "particles_nb": 2 } }
              },
              "retina_detect": true
            });
          }
        });
        </script>
        <?php
    }
}
// 確実に出るフッターに出力
add_action( 'wp_footer', 'add_hero_section', 1 );

// ============================================
// カスタムフッターセクション
// ============================================

/**
 * Google Fontsとアイコンフォントの読み込み
 */
function enaga_blog_enqueue_fonts() {
    // Google Fonts (Noto Sans JP, Zen Kaku Gothic New)
    wp_enqueue_style(
        'google-fonts',
        'https://fonts.googleapis.com/css2?family=Noto+Sans+JP:wght@400;500;700&family=Zen+Kaku+Gothic+New:wght@500;700&display=swap',
        array(),
        null
    );
    
    // Font Awesome 6
    wp_enqueue_style(
        'font-awesome',
        'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css',
        array(),
        '6.5.1'
    );
}
add_action('wp_enqueue_scripts', 'enaga_blog_enqueue_fonts');

/**
 * カスタムフッターセクションを追加
 */
function enaga_blog_custom_footer() {
    ?>
    <div class="enaga-footer-section">
        <div class="enaga-footer-container">
            
            <!-- 左カラム: サイト情報 -->
            <div class="enaga-footer-column enaga-footer-about">
                <h3 class="enaga-footer-title">エナガブログについて</h3>
                <p class="enaga-footer-description">
                    Kickstarterやクラウドファンディングに関する最新情報をお届けするブログです。
                    海外のクリエイティブなプロジェクトを日本語で紹介しています。
                </p>
                <div class="enaga-footer-social">
                    <a href="https://twitter.com/" class="enaga-social-icon" aria-label="X (Twitter)" target="_blank" rel="noopener noreferrer">
                        <i class="fab fa-x-twitter"></i>
                    </a>
                    <a href="https://instagram.com/" class="enaga-social-icon" aria-label="Instagram" target="_blank" rel="noopener noreferrer">
                        <i class="fab fa-instagram"></i>
                    </a>
                    <a href="https://facebook.com/" class="enaga-social-icon" aria-label="Facebook" target="_blank" rel="noopener noreferrer">
                        <i class="fab fa-facebook-f"></i>
                    </a>
                    <a href="<?php echo get_bloginfo('rss2_url'); ?>" class="enaga-social-icon" aria-label="RSS" target="_blank" rel="noopener noreferrer">
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
                        'number' => 6,
                        'hide_empty' => true,
                    ));
                    
                    if (!empty($categories)) {
                        foreach ($categories as $category) {
                            echo '<li><a href="' . esc_url(get_category_link($category->term_id)) . '">' . esc_html($category->name) . '</a></li>';
                        }
                    }
                    ?>
                </ul>
            </div>
            
            <!-- 右カラム: サイトリンク -->
            <div class="enaga-footer-column enaga-footer-links">
                <h3 class="enaga-footer-title">サイトリンク</h3>
                <ul class="enaga-footer-menu">
                    <li><a href="<?php echo home_url('/'); ?>">ホーム</a></li>
                    <?php
                    // 固定ページのリンクを動的に取得
                    $pages = array('about', 'contact', 'privacy-policy', 'sitemap');
                    foreach ($pages as $page_slug) {
                        $page = get_page_by_path($page_slug);
                        if ($page) {
                            echo '<li><a href="' . get_permalink($page->ID) . '">' . get_the_title($page->ID) . '</a></li>';
                        }
                    }
                    ?>
                </ul>
                
                <!-- ニュースレター登録 -->
                <div class="enaga-newsletter-signup">
                    <h4>最新記事をメールで受け取る</h4>
                    <form class="enaga-newsletter-form" action="#" method="post">
                        <input type="email" name="newsletter_email" placeholder="メールアドレス" required>
                        <button type="submit"><i class="fas fa-paper-plane"></i></button>
                    </form>
                </div>
            </div>
            
        </div>
    </div>
    <?php
}
add_action('cocoon_footer_before', 'enaga_blog_custom_footer');