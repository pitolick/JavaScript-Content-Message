<?php
/*
Plugin Name: JavaScript Content Message
Plugin URI: https://github.com/pitolick/JavaScript-Content-Message
Description: 特定の期間が経過した投稿・固定ページにメッセージを出力します
Version: {release version}
Author: ぴいた
License: GPL2
参考記事：https://webcake.stars.ne.jp/wp-posts-date-alert.html
　　　　　https://techmemo.biz/wordpress/wp-posts-date-alert/
　　　　　https://blog.kamata-net.com/archives/6867.html
*/

/**
 * CSS・JavaScript読み込み設定
 */
add_action('wp_enqueue_scripts', 'jcm_load_scripts');
function jcm_load_scripts() {
	// 設定項目にチェックのある投稿タイプだけ表示
	if (get_option('jcm_option_post_type_single') === false) {
		$jcm_is_single = true;
	} elseif(get_option('jcm_option_post_type_single') === 'single') {
		$jcm_is_single = is_single();
	} else {
		$jcm_is_single = false;
	}
	if (get_option('jcm_option_post_type_page') === false) {
		$jcm_is_page = true;
	} elseif(get_option('jcm_option_post_type_page') === 'page') {
		$jcm_is_page = is_page();
	} else {
		$jcm_is_page = false;
	}

	// CSS・JavaScript読み込み
	if($jcm_is_single || $jcm_is_page) {
		$plugin_url = plugin_dir_url( __FILE__ );
		if(get_option( 'jcm_option_css' ) !== 'custom') {
			wp_enqueue_style('jcm-css', $plugin_url.'dist/css/jcm.css');
		}
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
	// 比較基準タイプ設定
	if (get_option('jcm_option_reference') === 'modified_date') {
		$content_time = get_the_modified_date('Y/m/d'); // 最終更新日
	} else {
		$content_time = get_the_date('Y/m/d'); // 投稿日
	}
	// 比較基準日数設定
	if (get_option('jcm_reference_date') === false) {
		$reference_date = '1';
	} else {
		$reference_date = get_option('jcm_reference_date');
	}
	// 比較基準日or年設定
	if (get_option('jcm_reference_type')) {
		$reference_type = get_option('jcm_reference_type');
	} else {
		$reference_type = 'year';
	}
	// 表示メッセージ設定
	if (get_option( 'jcm_option_message' )) {
		$message_text = get_option( 'jcm_option_message' );
	} else {
		$message_text = 'この記事は%year%年以上前に書かれたものです。<br>情報が古い可能性があります。';
	}

	// DOM構築
	$message = '<input type="hidden" id="jcm_content_time" value="'.$content_time.'" style="display:none;">';
	$message .= '<input type="hidden" id="jcm_reference_date" value="'.$reference_date.'" style="display:none;">';
	$message .= '<input type="hidden" id="jcm_reference_type" value="'.$reference_type.'" style="display:none;">';
	$message .= '<div id="jcm_content_message" style="display:none;">'.$message_text.'</div>';
	if (get_option('jcm_option_css') === 'custom') {
		$message .= '<style>'.get_option('jcm_option_css_custom').'</style>';
	}

	return $message.$content;
}
add_filter('the_content', 'jcm_add_content','10');

/**
 * 管理画面に設定項目追加
 * 参考：https://www.nxworld.net/wordpress/wp-add-settings-field.html
 * 　　　https://qiita.com/diconran/items/bfdc093b083a2ee530c9
 */
function jcm_field() {
	/* 管理画面に項目追加 */
	add_settings_section( 'jcm_option_section', 'JavaScript-Content-Message', function(){echo '<p>投稿・固定ページに表示させるメッセージの設定を行います。</p>';}, 'writing' );
	// 適用ページ
	add_settings_field( 'jcm_option_post_type', '適用ページ', 'jcm_option_post_type', 'writing', 'jcm_option_section' );
	register_setting( 'writing', 'jcm_option_post_type_single' );
	register_setting( 'writing', 'jcm_option_post_type_page' );
	// 比較基準日
	add_settings_field( 'jcm_option_reference', '比較基準日', 'jcm_option_reference', 'writing', 'jcm_option_section' );
	register_setting( 'writing', 'jcm_option_reference' );
	register_setting( 'writing', 'jcm_reference_date' );
	register_setting( 'writing', 'jcm_reference_type' );
	// 表示メッセージ
	add_settings_field( 'jcm_option_message', '表示メッセージ', 'jcm_option_message', 'writing', 'jcm_option_section' );
	register_setting( 'writing', 'jcm_option_message' );
	// CSS設定
	add_settings_field( 'jcm_option_css', 'CSS設定', 'jcm_option_css', 'writing', 'jcm_option_section' );
	register_setting( 'writing', 'jcm_option_css' );
	register_setting( 'writing', 'jcm_option_css_custom' );
}
add_filter( 'admin_init', 'jcm_field' );
/* 管理画面設定項目DOM */
function jcm_option_post_type() {
	// 適用ページ
	?>
	<fieldset>
		<label><input name="jcm_option_post_type_single" type="checkbox" value="single" <?php echo get_option( 'jcm_option_post_type_single' ) === false ? 'checked="checked"' : checked( 'single', get_option( 'jcm_option_post_type_single' ) ); ?> />投稿</label>
		<label><input name="jcm_option_post_type_page" type="checkbox" value="page" <?php echo get_option( 'jcm_option_post_type_page' ) === false ? 'checked="checked"' : checked( 'page', get_option( 'jcm_option_post_type_page' ) ); ?> />固定ページ</label><br>
	</fieldset>
  <?php
}
function jcm_option_reference() {
	// 比較基準日
	?>
	<fieldset>
		<label><input name="jcm_option_reference" type="radio" value="posted_date" <?php echo get_option( 'jcm_option_reference' ) === false ? 'checked="checked"' : checked( 'posted_date', get_option( 'jcm_option_reference' ) ); ?> />投稿日</label>
		<label><input name="jcm_option_reference" type="radio" value="modified_date" <?php checked( 'modified_date', get_option( 'jcm_option_reference' ) ); ?> />最終更新日</label><br>
		<input type="number" name="jcm_reference_date" min="0" max="9999" value="<?php echo get_option( 'jcm_reference_date' ) === false ? '1' : get_option( 'jcm_reference_date' ); ?>">
		<select name="jcm_reference_type">
			<option value="year" <?php echo get_option( 'jcm_reference_type' ) === false ? 'selected' : selected( 'year', get_option( 'jcm_reference_type' ) ); ?>>年</option>
			<option value="day" <?php selected( 'day', get_option( 'jcm_reference_type' ) ); ?>>日</option>
		</select> 以上経過した記事にメッセージを表示
	</fieldset>
  <?php
}
function jcm_option_message() {
	// 表示メッセージ
	?>
	<fieldset>
		<p>表示させたいメッセージを入力してください。（HTML使用可能）</p>
		<p>例）<?php echo esc_html('この記事は%year%年以上前に書かれたものです。<br>情報が古い可能性があります。'); ?></p>
		<textarea name="jcm_option_message" class="large-text code" rows="3"><?php echo get_option( 'jcm_option_message' ) === false ? 'この記事は%year%年以上前に書かれたものです。<br>情報が古い可能性があります。' : get_option( 'jcm_option_message' ); ?></textarea>
		<p>利用可能なタグ</p>
		<ul>
			<li><code>%year%</code>：記事と現在の年差</li>
			<li><code>%monthnum%</code>：記事と現在の月差（1ヶ月あたり30日で計算のため多少ブレあり）</li>
			<li><code>%day%</code>：記事と現在の日差</li>
		</ul>
	</fieldset>
  <?php
}
function jcm_option_css() {
	// CSS設定
	?>
	<style>
		.jcm_option_css_custom {
			display: none;
		}
		#jcm_option_css_custom:checked ~ .jcm_option_css_custom {
			display: block;
		}
	</style>
	<fieldset>
		<input id="jcm_option_css_default" name="jcm_option_css" type="radio" value="default" <?php echo get_option( 'jcm_option_css' ) === false ? 'checked="checked"' : checked( 'default', get_option( 'jcm_option_css' ) ); ?> />
		<label for="jcm_option_css_default">プラグイン付属のCSSを使う</label>
		<input id="jcm_option_css_custom" name="jcm_option_css" type="radio" value="custom" <?php checked( 'custom', get_option( 'jcm_option_css' ) ); ?> />
		<label for="jcm_option_css_custom">オリジナルCSSを使う</label>
		<div class="jcm_option_css_custom">
			<p>表示されるメッセージは<code>#jcm_content_message</code>でラップされています。</p>
			<p>記述されたCSSはstyleタグに括られて出力されます。</p>
			<textarea name="jcm_option_css_custom" class="large-text code" rows="3"><?php echo get_option( 'jcm_option_css_custom' ) === false ? '#jcm_content_message{}' : get_option( 'jcm_option_css_custom' ); ?></textarea>
		</div>
	</fieldset>
  <?php
}