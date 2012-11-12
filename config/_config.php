<?php
/*
 * Description: Custom Taxonomy Manager Config
 * Author: Wangbin
*/

	/*
	 * タクソノミーに追加する項目
	 */
	/*
 	* 予約キー：object_id, term_id, name, slug, term_group, term_order, 
 	* 			term_taxonomy_id, taxonomy,description,parent,count
 	* 予約キーを使ってはいけません。カスタマイズのタグがWP本来のタグと重なっているからです。
 	*/

	$this->_items = array(
	
			//テキストの設定
			'textfield' => array(
				'name' 			=> 'テキスト',//表示名
				'taxonomy' 		=> array('category','post_tag','sample-category'),//カスタムのタクソノミー
				'type' 			=> 'textfield',//表示htmlタグの種類(textfield,checkbox,radio,select,textarea)fileが対象外です。
				'size'			=> 40,//input(textfield)の長さ
				'display' 		=> true,//一覧に表示するかどうか
				'description'	=> '<p class="description">補足説明</p>',//<p class="description">補足説明</p>//補足説明
			),
			//テキストの設定
			'textarea' => array(
				'name' 			=> 'テキストエリア',//表示名
				'taxonomy' 		=> array('category','post_tag','sample-category'),//カスタムのタクソノミー
				'type' 			=> 'textarea',//表示htmlタグの種類(textfield,checkbox,radio,select,textarea)fileが対象外です。
				'rows' 			=> 5,//textarea用
				'cols' 			=> 20,//textarea用
				'display' 		=> false,//一覧に表示するかどうか
				'description'	=> '',//<p class="description"></p>//補足説明
			),
			//テキストの設定
			'checkbox' => array(
				'name' 			=> 'チェックボックス',//表示名
				'taxonomy' 		=> array('category','post_tag','sample-category'),//カスタムのタクソノミー
				'type' 			=> 'checkbox',//表示htmlタグの種類(textfield,checkbox,radio,select,textarea)fileが対象外です。
				'values' 		=> array('checkbox_value1','checkbox_value2','checkbox_value3','checkbox_value4' ),//checkbox,radioの項目データ
				'checked' 		=> array('checkbox_value2','checkbox_value4'),//デフォルトのチェック
				'display' 		=> false,//一覧に表示するかどうか
				'description'	=> '',//<p class="description"></p>//補足説明
			),
				//テキストの設定
			'radio' => array(
				'name' 			=> 'ラジオボタン',//表示名
				'taxonomy' 		=> array('category','post_tag','sample-category'),//カスタムのタクソノミー
				'type' 			=> 'radio',//表示htmlタグの種類(textfield,checkbox,radio,select,textarea)fileが対象外です。
				'values' 		=> array('radio_value1','radio_value2','radio_value3','radio_value4' ),//checkbox,radioの項目データ
				'checked' 		=> 'radio_value3',//デフォルトのチェック
				'display' 		=> false,//一覧に表示するかどうか
				'description'	=> '',//<p class="description"></p>//補足説明
			),
				//テキストの設定
			'select' => array(
				'name' 			=> 'セレクト',//表示名
				'taxonomy' 		=> array('category','post_tag','sample-category'),//カスタムのタクソノミー
				'type' 			=> 'select',//表示htmlタグの種類(textfield,checkbox,radio,select,textarea)fileが対象外です。
				'values' 		=> array('select_value1','select_value2','select_value3','select_value4' ),//checkbox,radioの項目データ
				'selected' 		=> 'select_value3',//デフォルトのセレクト
				'display' 		=> true,//一覧に表示するかどうか
				'description'	=> '<p class="description">セレクト補足説明</p>',//<p class="description">セレクト補足説明</p>,//補足説明
			),
	
	);
	
	$this->explain_text = array(
			'title' 			=> '<h3>追加方法について</h3>',
			'p'					=>'<p>スラッグが通用です。<strong>各国語の名前</strong>を入力してください。<br />説明の項目は、データの構造上存在するだけで入力しなくとも表示などへの影響は一切ありません。</p>',
			'taxonomy' 			=> array('category','post_tag'),
	);
	
?>
