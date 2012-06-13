=== 011 Ps Custom Taxonomy ===
Contributors: ouhinit
Tags: category, tag, taxonomy, custom taxonomy
Requires at least: 1.0
Tested up to: 3.3.2

カテゴリー、タグ、カスタム分類の項目（名前、と説明以外）を追加、編集するのが自由自在にできる

== Description ==
カテゴリー、タグ、カスタム分類の項目を追加、編集するのが自由自在にできる
追加する項目できるタグ（text,textarea,checkbox,radio,select）

= Functions =
1. カテゴリー、タグ、カスタム分類の項目を追加、編集、簡単にカスタマイズできる

= Usage =
* カテゴリー、タグ、カスタム分類に項目を追加、設定されます。
* 追加する項目を簡単に取得できる。例：$cat = get_term(1,'category'); $custom_item = $cat->追加項目のキー;
* カスタム分類（タグ、カテゴリー）の記事一覧への追加する項目が表示するこごができます。

== Installation ==

1. pluginsフォルダに、ダウンロードした 011 Ps Custom Taxonomy のフォルダをアップロードしてください。
2. サンプルファイル（_config.php）より、追加項目を設定し、config.phpにリネームしてください。
3. プラグイン一覧から"011 PS Custom Taxonomy"というプラグインを有効する。

== Changelog ==
= Version 1.2 (13-06-2012) =
* FIXED: クイック編集の不具合修正

