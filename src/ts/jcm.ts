let jcm_content = document.getElementById('jcm_content_message');
let jcm_post_time = (<HTMLInputElement>document.getElementById('jcm_content_time'));
let jcm_reference_date = (<HTMLInputElement>document.getElementById('jcm_reference_date'));
let jcm_reference_type = (<HTMLInputElement>document.getElementById('jcm_reference_type'));
if(jcm_post_time !== null && jcm_reference_date !== null && jcm_reference_type !== null) {
	const jcm_post_time_text = jcm_post_time.value;
	const jcm_reference_date_text:number = Number(jcm_reference_date.value);
	const jcm_reference_type_text:string = jcm_reference_type.value;
	if(jcm_post_time_text !== null) {
		// 記事の日付
		const post_date = new Date(jcm_post_time_text.toString());
		// 現在の日付
		const date = new Date();
		// 現在と記事の差分
		const diff_date = date.getTime() - post_date.getTime();
		const diff_year:number = Math.floor(diff_date / (1000 * 60 * 60 * 24 * 365));
		const diff_month:number = Math.floor(diff_date / (1000 * 60 * 60 * 24 * 30));
		const diff_day:number = Math.floor(diff_date / (1000 * 60 * 60 * 24));

		// 表示条件比較
		let compare_time = jcm_reference_type_text === 'year' ? diff_year : diff_day;
		if( compare_time >= jcm_reference_date_text ) {
			// メッセージを取得
			if(jcm_content !== null) {
				let jcm_text = jcm_content.innerHTML;
				// 正規表現置換
				if(jcm_text !== null) {
					jcm_text = jcm_text.replace(/%year%/,diff_year.toString());
					jcm_text = jcm_text.replace(/%monthnum%/,diff_month.toString());
					jcm_text = jcm_text.replace(/%day%/,diff_day.toString());

					// DOM書き換え
					jcm_content.innerText = '';
					jcm_content.insertAdjacentHTML('afterbegin',jcm_text);
					// メッセージ表示
					jcm_content.style.display = "block";
				}
			}
		}
	}
}