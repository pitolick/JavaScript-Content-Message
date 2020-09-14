"use strict";
var jcm_content = document.getElementById('jcm_content_message');
var jcm_post_time = document.getElementById('jcm_content_time');
var jcm_reference_date = document.getElementById('jcm_reference_date');
var jcm_reference_type = document.getElementById('jcm_reference_type');
var jcm_modified_date = document.getElementById('jcm_modified_date');
var jcm_post_date = document.getElementById('jcm_post_date');
if (jcm_post_time !== null && jcm_reference_date !== null && jcm_reference_type !== null && jcm_modified_date !== null && jcm_post_date !== null) {
    var jcm_post_time_text = jcm_post_time.value;
    var jcm_reference_date_text = Number(jcm_reference_date.value);
    var jcm_reference_type_text = jcm_reference_type.value;
    var jcm_modified_date_text = jcm_modified_date.value;
    var jcm_post_date_text = jcm_post_date.value;
    if (jcm_post_time_text !== null) {
        // 記事の日付
        var post_date = new Date(jcm_post_time_text.toString());
        // 現在の日付
        var date = new Date();
        // 現在と記事の差分
        var diff_date = date.getTime() - post_date.getTime();
        var diff_year = Math.floor(diff_date / (1000 * 60 * 60 * 24 * 365));
        var diff_month = Math.floor(diff_date / (1000 * 60 * 60 * 24 * 30));
        var diff_day = Math.floor(diff_date / (1000 * 60 * 60 * 24));
        // 表示条件比較
        var compare_time = jcm_reference_type_text === 'year' ? diff_year : diff_day;
        if (compare_time >= jcm_reference_date_text) {
            // メッセージを取得
            if (jcm_content !== null) {
                var jcm_text = jcm_content.innerHTML;
                // 正規表現置換
                if (jcm_text !== null) {
                    jcm_text = jcm_text.replace(/%year%/, diff_year.toString());
                    jcm_text = jcm_text.replace(/%monthnum%/, diff_month.toString());
                    jcm_text = jcm_text.replace(/%day%/, diff_day.toString());
                    jcm_text = jcm_text.replace(/%post_date%/, jcm_modified_date_text.toString());
                    jcm_text = jcm_text.replace(/%modified_date%/, jcm_post_date_text.toString());
                    // DOM書き換え
                    jcm_content.innerText = '';
                    jcm_content.insertAdjacentHTML('afterbegin', jcm_text);
                    // メッセージ表示
                    jcm_content.style.display = "block";
                }
            }
        }
    }
}
