<?php
/**
 * Twenty Twenty-Four Child Theme - Enaga Blog
 * Functions and definitions
 */

if (!defined('ABSPATH')) {
    exit;
}

/************************************
** Google Fontsの読み込み
************************************/
function enaga_child_enqueue_fonts() {
    wp_enqueue_style(
        'google-fonts',
        'https://fonts.googleapis.com/css2?family=Noto+Sans+JP:wght@400;500;700&family=Zen+Kaku+Gothic+New:wght@500;700&display=swap',
        array(),
        null
    );
}
add_action('wp_enqueue_scripts', 'enaga_child_enqueue_fonts');

/************************************
** カスタムスクリプトとスタイルの読み込み
************************************/
function enaga_child_enqueue_scripts() {
    // 子テーマスタイル（依存関係なしで読み込み）
    wp_enqueue_style('twentytwentyfour-child-style', get_stylesheet_uri());
    
    // カスタムCSS
    wp_enqueue_style(
        'enaga-custom-css',
        get_stylesheet_directory_uri() . '/assets/css/custom.css',
        array('twentytwentyfour-child-style'),
        '1.0.0'
    );
    
    // ヘッダーJS
    wp_enqueue_script(
        'enaga-header',
        get_stylesheet_directory_uri() . '/assets/js/header.js',
        array(),
        '1.0.0',
        true
    );
    
    // 検索JS
    wp_enqueue_script(
        'enaga-search',
        get_stylesheet_directory_uri() . '/assets/js/search.js',
        array(),
        '1.0.0',
        true
    );
}
add_action('wp_enqueue_scripts', 'enaga_child_enqueue_scripts');

/************************************
** ロゴサイズのカスタマイズ
************************************/
function enaga_child_custom_logo_setup() {
    add_theme_support('custom-logo', array(
        'height' => 180,
        'width' => 600,
        'flex-height' => true,
        'flex-width' => true,
    ));
}
add_action('after_setup_theme', 'enaga_child_custom_logo_setup');

/************************************
** パンくずリスト関数
************************************/
function enaga_blog_breadcrumbs() {
    if (is_home() || is_front_page()) {
        return;
    }
    
    echo '<nav class="breadcrumbs" aria-label="パンくずリスト">';
    echo '<ol class="breadcrumb-list" itemscope itemtype="https://schema.org/BreadcrumbList">';
    
    // ホームリンク
    echo '<li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">';
    echo '<a href="' . home_url('/') . '" itemprop="item"><span itemprop="name">ホーム</span></a>';
    echo '<meta itemprop="position" content="1" />';
    echo '</li>';
    
    $position = 2;
    
    if (is_category() || is_single()) {
        $categories = get_the_category();
        
        if (!empty($categories)) {
            $category = $categories[0];
            
            if ($category->parent != 0) {
                $parent = get_category($category->parent);
                echo '<li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">';
                echo '<a href="' . get_category_link($parent->term_id) . '" itemprop="item">';
                echo '<span itemprop="name">' . $parent->name . '</span></a>';
                echo '<meta itemprop="position" content="' . $position . '" />';
                echo '</li>';
                $position++;
            }
            
            echo '<li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">';
            echo '<a href="' . get_category_link($category->term_id) . '" itemprop="item">';
            echo '<span itemprop="name">' . $category->name . '</span></a>';
            echo '<meta itemprop="position" content="' . $position . '" />';
            echo '</li>';
            $position++;
        }
        
        if (is_single()) {
            echo '<li class="breadcrumb-current" itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">';
            echo '<span itemprop="name">' . get_the_title() . '</span>';
            echo '<meta itemprop="position" content="' . $position . '" />';
            echo '</li>';
        }
    } elseif (is_page()) {
        echo '<li class="breadcrumb-current" itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">';
        echo '<span itemprop="name">' . get_the_title() . '</span>';
        echo '<meta itemprop="position" content="2" />';
        echo '</li>';
    }
    
    echo '</ol>';
    echo '</nav>';
}
add_shortcode('enaga_breadcrumbs', 'enaga_blog_breadcrumbs');

/************************************
** サイドバーの登録
************************************/
function enaga_child_widgets_init() {
    register_sidebar( array(
        'name'          => 'メインサイドバー',
        'id'            => 'sidebar-1',
        'description'   => '記事ページのサイドバーエリア',
        'before_widget' => '<div id="%1$s" class="widget %2$s">',
        'after_widget'  => '</div>',
        'before_title'  => '<h3 class="widget-title">',
        'after_title'   => '</h3>',
    ) );
}
add_action( 'widgets_init', 'enaga_child_widgets_init' );

/************************************
** カスタムロゴの強制適用
************************************/
add_action('wp_head', function() {
    ?>
    <style id="enaga-logo-override">
        .wp-block-site-logo img,
        .custom-logo {
            content: url('<?php echo esc_url(get_stylesheet_directory_uri() . '/assets/images/logo.jpg'); ?>') !important;
            max-height: 180px !important;
            width: auto !important;
        }
    </style>
    <?php
}, 100);
