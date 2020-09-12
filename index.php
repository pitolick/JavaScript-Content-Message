<?php
/*
Plugin Name: JavaScript Content Message
Plugin URI: https://github.com/pitolick/JavaScript-Content-Message
Description: 特定の期間が経過した投稿・固定ページにメッセージを出力します
Version: {release version}
Author: ぴいた
License: GPL2
*/

/**
 * CSS・Javascript読み込み設定
 */
add_action('wp_enqueue_scripts', 'jcm_load_scripts');
function jcm_load_scripts() {
	if(is_single() || is_page()) {
		$plugin_url = plugin_dir_url( __FILE__ );
		wp_enqueue_style('jcm-css', $plugin_url.'dist/css/jcm.css');
		wp_enqueue_script('jcm-js', $plugin_url.'dist/js/jcm.js','','',true);
	}
}

/**
 * the_contentにDOMを追加
 * %year%:記事と現在の年差
 * %monthnum%:記事と現在の月差（1ヶ月あたり30日で計算のため多少ブレあり）
 * %day%:記事と現在の日差
 */
function jcm_add_content($content) {
	// 設定
	$content_time = get_the_date('Y/m/d'); // 投稿日
	// $content_time = get_the_modified_date('Y/m/d'); // 更新日
	$reference_date = '1';
	$reference_type = 'year';
	$message_text = 'この記事は%year%年以上前に書かれたものです。<br>情報が古い可能性があります。';

	// DOM構築
	$message = '<input type="hidden" id="jcm_content_time" value="'.$content_time.'" style="display:none;">';
	$message .= '<input type="hidden" id="jcm_reference_date" value="'.$reference_date.'" style="display:none;">';
	$message .= '<input type="hidden" id="jcm_reference_type" value="'.$reference_type.'" style="display:none;">';
	$message .= '<div id="jcm_content_message" style="display:none;">'.$message_text.'</div>';

	return $message.$content;
}
add_filter('the_content', 'jcm_add_content','10');
