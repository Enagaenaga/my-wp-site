<?php //子テーマのfunctions.php
if ( !defined( 'ABSPATH' ) ) exit;

error_log('DEBUG: Cocoon Child functions.php loaded at ' . date('Y-m-d H:i:s'));

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

// 子テーマstyle.cssのキャッシュ破棄（バージョン番号更新）
function enqueue_child_theme_style() {
    wp_enqueue_style(
        'cocoon-child-style',
        get_stylesheet_uri(),
        array('cocoon-style'),
        '2.0.' . time()  // 毎回新しいバージョンを生成してキャッシュを破棄
    );
}
add_action( 'wp_enqueue_scripts', 'enqueue_child_theme_style', 20 );

// ヘッダー高さ30px強制適用（インラインスタイル - 最高優先度）
function enaga_header_inline_styles() {
    ?>
    <style id="enaga-header-override">
        /* ヘッダー高さ30px強制適用 */
        #header,
        .header,
        header#header,
        .header-container #header {
            height: 30px !important;
            min-height: 30px !important;
            max-height: 30px !important;
            overflow: hidden !important;
        }
        .header-in,
        #header-in {
            height: 30px !important;
            padding: 0 !important;
            display: flex !important;
            flex-direction: row !important;
            align-items: center !important;
            justify-content: center !important;
            overflow: hidden !important;
        }
        .site-name-text {
            font-size: 0.9rem !important;
            line-height: 1 !important;
            margin: 0 !important;
            padding: 0 !important;
        }
        .tagline {
            font-size: 0.5rem !important;
            line-height: 1 !important;
            margin: 0 0 0 10px !important;
            padding: 0 !important;
        }
    </style>
    <?php
}
add_action( 'wp_head', 'enaga_header_inline_styles', 9999 );

// 検索機能強化スクリプト読み込み
function enqueue_search_scripts() {
    wp_enqueue_script( 
        'search-js', 
        get_stylesheet_directory_uri() . '/js/search.js', 
        array(), 
        '1.0.0', 
        true 
    );
}
add_action( 'wp_enqueue_scripts', 'enqueue_search_scripts' );

// アニメーションスクリプト読み込み
function enqueue_animation_scripts() {
    wp_enqueue_script( 
        'animations-js', 
        get_stylesheet_directory_uri() . '/js/animations.js', 
        array(), 
        '1.0.0', 
        true 
    );
}
add_action( 'wp_enqueue_scripts', 'enqueue_animation_scripts' );

// ダークモードスクリプト読み込み
function enqueue_dark_mode_scripts() {
    wp_enqueue_script( 
        'dark-mode-js', 
        get_stylesheet_directory_uri() . '/js/dark-mode.js', 
        array(), 
        '1.0.0', 
        true 
    );
}
add_action( 'wp_enqueue_scripts', 'enqueue_dark_mode_scripts' );

// 画像遅延読み込みスクリプト
function enqueue_lazy_load_scripts() {
    wp_enqueue_script( 
        'lazy-load-js', 
        get_stylesheet_directory_uri() . '/js/lazy-load.js', 
        array(), 
        '1.0.0', 
        true 
    );
}
add_action( 'wp_enqueue_scripts', 'enqueue_lazy_load_scripts' );

// ========================================
// パフォーマンス最適化 - Section 5.3
// ========================================

// WebPサポートの追加
function enaga_blog_add_webp_support( $mimes ) {
    $mimes['webp'] = 'image/webp';
    return $mimes;
}
add_filter( 'upload_mimes', 'enaga_blog_add_webp_support' );

// 不要なスクリプトを削除してパフォーマンス向上
function enaga_blog_remove_unnecessary_scripts() {
    // 不要な絵文字スクリプトを削除
    remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
    remove_action( 'wp_print_styles', 'print_emoji_styles' );
    remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
    remove_action( 'admin_print_styles', 'print_emoji_styles' );
    
    // RSSリンクを削除（不要な場合）
    // remove_action( 'wp_head', 'feed_links_extra', 3 );
    
    // Windows Live Writer用リンクを削除
    remove_action( 'wp_head', 'wlwmanifest_link' );
    
    // WordPressバージョン情報を削除（セキュリティ対策）
    remove_action( 'wp_head', 'wp_generator' );
    
    // 短縮URLリンクを削除
    remove_action( 'wp_head', 'wp_shortlink_wp_head' );
}
add_action( 'init', 'enaga_blog_remove_unnecessary_scripts' );

// スクリプトの非同期/遅延読み込み
function enaga_blog_async_defer_scripts( $tag, $handle ) {
    // 非同期読み込みするスクリプト
    $async_scripts = array( 'search-js', 'animations-js', 'lazy-load-js' );
    
    // 遅延読み込みするスクリプト
    $defer_scripts = array( 'dark-mode-js' );
    
    if ( in_array( $handle, $async_scripts ) ) {
        return str_replace( ' src', ' async src', $tag );
    }
    
    if ( in_array( $handle, $defer_scripts ) ) {
        return str_replace( ' src', ' defer src', $tag );
    }
    
    return $tag;
}
add_filter( 'script_loader_tag', 'enaga_blog_async_defer_scripts', 10, 2 );

// 画像にloading="lazy"を追加
function enaga_blog_add_lazy_loading( $attr, $attachment, $size ) {
    $attr['loading'] = 'lazy';
    return $attr;
}
add_filter( 'wp_get_attachment_image_attributes', 'enaga_blog_add_lazy_loading', 10, 3 );

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

// ============================================
// サイドバーウィジェットエリア
// ============================================

/**
 * カスタムウィジェットエリアを登録
 */
function enaga_blog_register_sidebars() {
    // メインサイドバー
    register_sidebar(array(
        'name'          => 'エナガブログ サイドバー',
        'id'            => 'enaga-main-sidebar',
        'description'   => 'ブログ記事ページのサイドバー',
        'before_widget' => '<div id="%1$s" class="enaga-sidebar-widget %2$s">',
        'after_widget'  => '</div>',
        'before_title'  => '<h3 class="enaga-widget-title">',
        'after_title'   => '</h3>',
    ));
    
    // フッターウィジェット
    register_sidebar(array(
        'name'          => 'フッターウィジェット',
        'id'            => 'enaga-footer-widget',
        'description'   => 'フッターに表示されるウィジェット',
        'before_widget' => '<div id="%1$s" class="enaga-footer-widget %2$s">',
        'after_widget'  => '</div>',
        'before_title'  => '<h3 class="enaga-widget-title">',
        'after_title'   => '</h3>',
    ));
}
add_action('widgets_init', 'enaga_blog_register_sidebars');

/**
 * プロフィールウィジェット
 */
class Enaga_Profile_Widget extends WP_Widget {
    
    public function __construct() {
        parent::__construct(
            'enaga_profile_widget',
            'エナガ プロフィール',
            array('description' => 'ブログのプロフィール情報を表示')
        );
    }
    
    public function widget($args, $instance) {
        $title = !empty($instance['title']) ? $instance['title'] : 'プロフィール';
        $description = !empty($instance['description']) ? $instance['description'] : '';
        $avatar_url = !empty($instance['avatar_url']) ? $instance['avatar_url'] : '';
        
        echo $args['before_widget'];
        ?>
        <div class="enaga-profile-widget">
            <?php if ($avatar_url): ?>
            <div class="profile-avatar">
                <img src="<?php echo esc_url($avatar_url); ?>" alt="<?php echo esc_attr($title); ?>">
            </div>
            <?php endif; ?>
            <h3 class="profile-name"><?php echo esc_html($title); ?></h3>
            <?php if ($description): ?>
            <p class="profile-description"><?php echo esc_html($description); ?></p>
            <?php endif; ?>
            <div class="profile-social">
                <a href="https://twitter.com/" class="social-link" aria-label="Twitter"><i class="fab fa-x-twitter"></i></a>
                <a href="https://instagram.com/" class="social-link" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
            </div>
        </div>
        <?php
        echo $args['after_widget'];
    }
    
    public function form($instance) {
        $title = !empty($instance['title']) ? $instance['title'] : 'エナガブログ';
        $description = !empty($instance['description']) ? $instance['description'] : '';
        $avatar_url = !empty($instance['avatar_url']) ? $instance['avatar_url'] : '';
        ?>
        <p>
            <label for="<?php echo $this->get_field_id('title'); ?>">名前:</label>
            <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>">
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('description'); ?>">自己紹介:</label>
            <textarea class="widefat" id="<?php echo $this->get_field_id('description'); ?>" name="<?php echo $this->get_field_name('description'); ?>" rows="4"><?php echo esc_textarea($description); ?></textarea>
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('avatar_url'); ?>">アバター画像URL:</label>
            <input class="widefat" id="<?php echo $this->get_field_id('avatar_url'); ?>" name="<?php echo $this->get_field_name('avatar_url'); ?>" type="url" value="<?php echo esc_url($avatar_url); ?>">
        </p>
        <?php
    }
    
    public function update($new_instance, $old_instance) {
        $instance = array();
        $instance['title'] = sanitize_text_field($new_instance['title']);
        $instance['description'] = sanitize_textarea_field($new_instance['description']);
        $instance['avatar_url'] = esc_url_raw($new_instance['avatar_url']);
        return $instance;
    }
}

/**
 * 人気記事ウィジェット
 */
class Enaga_Popular_Posts_Widget extends WP_Widget {
    
    public function __construct() {
        parent::__construct(
            'enaga_popular_posts_widget',
            'エナガ 人気記事',
            array('description' => '人気の記事を表示')
        );
    }
    
    public function widget($args, $instance) {
        $title = !empty($instance['title']) ? $instance['title'] : '人気記事';
        $number = !empty($instance['number']) ? absint($instance['number']) : 5;
        
        echo $args['before_widget'];
        echo $args['before_title'] . esc_html($title) . $args['after_title'];
        
        // 人気記事を取得（コメント数で判定、または閲覧数プラグインがあればそれを使用）
        $popular_posts = new WP_Query(array(
            'posts_per_page' => $number,
            'orderby' => 'comment_count',
            'order' => 'DESC',
            'ignore_sticky_posts' => true,
        ));
        
        if ($popular_posts->have_posts()): ?>
        <ul class="enaga-popular-posts-list">
            <?php while ($popular_posts->have_posts()): $popular_posts->the_post(); ?>
            <li class="enaga-popular-post-item">
                <?php if (has_post_thumbnail()): ?>
                <a href="<?php the_permalink(); ?>" class="popular-post-thumbnail">
                    <?php the_post_thumbnail('thumbnail'); ?>
                </a>
                <?php endif; ?>
                <div class="popular-post-content">
                    <a href="<?php the_permalink(); ?>" class="popular-post-title"><?php the_title(); ?></a>
                    <span class="popular-post-date"><?php echo get_the_date(); ?></span>
                </div>
            </li>
            <?php endwhile; ?>
        </ul>
        <?php 
        wp_reset_postdata();
        endif;
        
        echo $args['after_widget'];
    }
    
    public function form($instance) {
        $title = !empty($instance['title']) ? $instance['title'] : '人気記事';
        $number = !empty($instance['number']) ? absint($instance['number']) : 5;
        ?>
        <p>
            <label for="<?php echo $this->get_field_id('title'); ?>">タイトル:</label>
            <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>">
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('number'); ?>">表示件数:</label>
            <input id="<?php echo $this->get_field_id('number'); ?>" name="<?php echo $this->get_field_name('number'); ?>" type="number" min="1" max="10" value="<?php echo esc_attr($number); ?>">
        </p>
        <?php
    }
    
    public function update($new_instance, $old_instance) {
        $instance = array();
        $instance['title'] = sanitize_text_field($new_instance['title']);
        $instance['number'] = absint($new_instance['number']);
        return $instance;
    }
}

/**
 * カスタムウィジェットを登録
 */
function enaga_blog_register_widgets() {
    register_widget('Enaga_Profile_Widget');
    register_widget('Enaga_Popular_Posts_Widget');
}
add_action('widgets_init', 'enaga_blog_register_widgets');