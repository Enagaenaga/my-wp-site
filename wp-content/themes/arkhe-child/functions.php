<?php
/**
 * Arkhe Child Theme Functions
 * エナガブログ用カスタマイズ
 */

if (!defined('ABSPATH')) exit;

/************************************
** スタイル・スクリプト読み込み
************************************/

// 親テーマと子テーマのスタイル読み込み
add_action('wp_enqueue_scripts', function() {
    // 親テーマのスタイル
    wp_enqueue_style(
        'arkhe-parent-style',
        get_template_directory_uri() . '/style.css',
        array(),
        wp_get_theme('arkhe')->get('Version')
    );
    
    // 子テーマのスタイル
    wp_enqueue_style(
        'arkhe-child-style',
        get_stylesheet_uri(),
        array('arkhe-parent-style'),
        wp_get_theme()->get('Version')
    );
}, 10);

// Google Fonts読み込み
add_action('wp_enqueue_scripts', function() {
    wp_enqueue_style(
        'google-fonts',
        'https://fonts.googleapis.com/css2?family=Noto+Sans+JP:wght@400;500;700&family=Zen+Kaku+Gothic+New:wght@500;700&display=swap',
        array(),
        null
    );
}, 11);

// ヘッダー用JavaScript読み込み
add_action('wp_enqueue_scripts', function() {
    wp_enqueue_script(
        'arkhe-child-header',
        get_stylesheet_directory_uri() . '/js/header.js',
        array(),
        wp_get_theme()->get('Version'),
        true
    );
}, 12);

/************************************
** Font Awesome 読み込み
************************************/
add_action('wp_enqueue_scripts', function() {
    wp_enqueue_style(
        'font-awesome',
        'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css',
        array(),
        '6.5.1'
    );
}, 11);

/************************************
** サイドバー表示設定
************************************/
// Arkheテーマのサイドバーを強制的に表示
add_filter('arkhe_is_show_sidebar', function($is_show) {
    return true; // 常にサイドバーを表示
});
