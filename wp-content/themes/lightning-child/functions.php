<?php
/**
 * Lightning Child Theme - Enaga Blog
 * Functions and definitions
 */

if (!defined('ABSPATH')) {
    exit;
}

/************************************
** 親テーマと子テーマのスタイル読み込み
************************************/
function lightning_child_enqueue_scripts() {
    // 親テーマのスタイル
    wp_enqueue_style('lightning-design-style', get_template_directory_uri() . '/style.css');
    
    // 子テーマのスタイル
    wp_enqueue_style('lightning-child-style', get_stylesheet_uri(), array('lightning-design-style'));
    
    // Google Fonts
    wp_enqueue_style(
        'google-fonts',
        'https://fonts.googleapis.com/css2?family=Noto+Sans+JP:wght@400;500;700&family=Zen+Kaku+Gothic+New:wght@500;700&display=swap',
        array(),
        null
    );
    
    // カスタムJS
    wp_enqueue_script(
        'enaga-search',
        get_stylesheet_directory_uri() . '/assets/js/search.js',
        array('jquery'),
        '1.0.0',
        true
    );
}
add_action('wp_enqueue_scripts', 'lightning_child_enqueue_scripts');

/************************************
** ロゴサイズのカスタマイズ（強制適用）
************************************/
add_action('wp_head', function() {
    ?>
    <style id="enaga-logo-override">
        /* ロゴ画像を強制的に表示 */
        .siteHeader_logo img {
            content: url('<?php echo esc_url(get_stylesheet_directory_uri() . '/assets/images/logo.jpg'); ?>') !important;
            max-height: 180px !important;
            width: auto !important;
        }
    </style>
    <?php
}, 100);

/************************************
** パンくずリスト関数（Lightning標準を使用するため、必要に応じてオーバーライド）
************************************/
// Lightningは標準でパンくずリストを持っていますが、カスタマイズが必要な場合はここに記述します。

/************************************
** サイドバーウィジェット（Lightning標準を使用）
************************************/
