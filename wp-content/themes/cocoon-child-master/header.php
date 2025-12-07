<?php
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit; ?>
<!doctype html>
<html <?php language_attributes(); ?>>

<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
<meta name="referrer" content="<?php echo apply_filters('cocoon_meta_referrer_content', get_meta_referrer_content()); ?>">
<meta name="format-detection" content="telephone=no">

<?php //ヘッドタグ内挿入用のアクセス解析用テンプレート
cocoon_template_part('tmp/head-analytics'); ?>
<?php //AMPの案内タグを出力
if ( has_amp_page() ): ?>
<link rel="amphtml" href="<?php echo get_amp_permalink(); ?>">
<?php endif ?>
<?php //Google Search Consoleのサイト認証IDの表示
if ( get_google_search_console_id() ): ?>
<!-- Google Search Console -->
<meta name="google-site-verification" content="<?php echo get_google_search_console_id() ?>" />
<!-- /Google Search Console -->
<?php endif;//Google Search Console終了 ?>
<?php //preconnect dns-prefetch
$domains = list_text_to_array(get_pre_acquisition_list());
if ($domains) {
  echo '<!-- preconnect dns-prefetch -->'.PHP_EOL;
}
foreach ($domains as $domain): ?>
<link rel="preconnect dns-prefetch" href="//<?php echo $domain; ?>">
<?php endforeach; ?>

<!-- Preload -->
<link rel="preload" as="font" type="font/woff" href="<?php echo FONT_ICOMOON_WOFF_URL; ?>" crossorigin>
<?php if (is_site_icon_font_font_awesome_4()): ?>
<link rel="preload" as="font" type="font/woff2" href="<?php echo FONT_AWESOME_4_WOFF2_URL; ?>" crossorigin>
<?php else: ?>
<link rel="preload" as="font" type="font/woff2" href="<?php echo FONT_AWESOME_5_BRANDS_WOFF2_URL; ?>" crossorigin>
<link rel="preload" as="font" type="font/woff2" href="<?php echo FONT_AWESOME_5_REGULAR_WOFF2_URL; ?>" crossorigin>
<link rel="preload" as="font" type="font/woff2" href="<?php echo FONT_AWESOME_5_SOLID_WOFF2_URL; ?>" crossorigin>
<?php endif; ?>
<?php //WordPressが出力するヘッダー情報
wp_head();
?>

<?php //カスタムフィールドの挿入（カスタムフィールド名：head_custom
cocoon_template_part('tmp/head-custom-field'); ?>

<?php //headで読み込む必要があるJavaScript
cocoon_template_part('tmp/head-javascript'); ?>

<?php //PWAスクリプト
cocoon_template_part('tmp/head-pwa'); ?>

<?php //ヘッドタグ内挿入用のユーザー用テンプレート
cocoon_template_part('tmp-user/head-insert'); ?>

<!-- ENAGA HEADER FORCE STYLE -->
<style id="enaga-header-hardcoded">
    /* ヘッダー高さ30px強制適用 - Hardcoded Override */
    :root {
        --header-height: 30px;
    }
    html, body {
        --header-height: 30px;
    }
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
        min-height: 30px !important;
        max-height: 30px !important;
        padding: 0 !important;
        display: flex !important;
        flex-direction: row !important;
        flex-wrap: nowrap !important;
        align-items: center !important;
        justify-content: center !important;
        overflow: hidden !important;
        background: transparent !important; /* 背景透明化 */
    }
    /* ロゴ周りの余白を完全削除 */
    .logo-header,
    .logo-image,
    .site-name-text-link {
        height: 30px !important;
        max-height: 30px !important;
        margin: 0 !important;
        padding: 0 !important;
        display: flex !important;
        align-items: center !important;
        line-height: 1 !important;
    }
    .logo-header img {
        height: auto !important;
        max-height: 26px !important; /* 上下2pxの余裕 */
        width: auto !important;
        margin: 0 !important;
        padding: 0 !important;
    }
    .site-name-text {
        font-size: 1.2rem !important; /* 少し大きめに */
        line-height: 1 !important;
        margin: 0 !important;
        padding: 0 !important;
        display: inline-block !important;
    }
    .tagline {
        font-size: 0.7rem !important;
        line-height: 1 !important;
        margin: 0 0 0 10px !important;
        padding: 0 !important;
        display: inline-block !important;
        white-space: nowrap !important;
        color: #333 !important; /* 視認性確保 */
    }
    /* グローバルメニュー直上の余白を削除 */
    nav#navi,
    #navi {
        margin-top: 0 !important;
        padding-top: 0 !important;
    }
    /* モバイルなどの調整 */
    @media screen and (max-width: 768px) {
        .tagline {
            display: none !important; /* モバイルではtaglineを隠す */
        }
    }
    
    /* アフェリエイト広告通知 - 背景色をクラフトベージュに統一 */
    .text-mobile,
    .widget_mobile_text,
    .widget_mobile_text .text-mobile,
    #mobile_text-2,
    #mobile_text-2 .text-mobile,
    aside.widget_mobile_text,
    .widget.widget_mobile_text,
    .content-top .widget_mobile_text,
    #content-top .widget_mobile_text,
    .widget-content-top.widget_mobile_text {
        background-color: #f5f0e8 !important;
        background: #f5f0e8 !important;
        border: none !important;
        color: #5a5045 !important;
    }
    /* ダークモード */
    .dark-mode .text-mobile,
    .dark-mode .widget_mobile_text,
    .dark-mode #mobile_text-2,
    html.dark-mode .text-mobile,
    html.dark-mode .widget_mobile_text,
    body.dark-mode .text-mobile,
    body.dark-mode .widget_mobile_text {
        background-color: #1a1a25 !important;
        background: #1a1a25 !important;
        color: #a0a0b0 !important;
    }
</style>
<!-- /ENAGA HEADER FORCE STYLE -->

</head>

<body <?php body_class(); ?> itemscope itemtype="https://schema.org/WebPage">

<?php //body最初に挿入するアクセス解析ヘッダータグの取得
  cocoon_template_part('tmp/body-top-analytics'); ?>

<?php //サイトヘッダーからコンテンツまでbodyタグ最初のHTML
cocoon_template_part('tmp/body-top'); ?>
