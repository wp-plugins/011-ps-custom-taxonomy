<?php
/*
Plugin Name: 011 PS Custom Taxonomy
Plugin URI: http://wordpress.org/extend/plugins/011-ps-custom-taxonomy/
Description: Manager Wordpress Custom Taxonomy(タクソノミーのカスタマイズ、項目の追加する)
Author: Wang Bin (oh@prime-strategy.co.jp)
Version: 1.3
Author URI: http://www.prime-strategy.co.jp/about/staff/oh/
*/

/**
 * Ps_Custom_Taxonomy
 *
 * Main Custom Taxonomy Plugin Class
 *
 * @package Ps_Custom_Taxonomy
 */

class Ps_Custom_Taxonomy{
	
	/*
	 * タクソノミーに追加する項目
	 */
	var $_items;
	
	/*
	 * ヘッダの文言
	 */
	var $explain_text;
	
	/*
	*	Start Manager Custom  on plugins loaded
	*/
	function Ps_Custom_Taxonomy( ){
		$this->__construct( );
	}

	/*
	 * コンストラクタ.
	 */
	function __construct( ) {
		$this->init( );
	}

	/*
	 * initializing
	 */
	function init( ){
		
		if(defined('DOCUMENTROOT')):
			define( 'DOCUMENTROOT' , $_SERVER['DOCUMENT_ROOT'] );
		endif;
		
		if( defined('HOMEDIR') ):
			define( 'HOMEDIR' , dirname($_SERVER['DOCUMENT_ROOT']) );
		endif;
		
	    define( 'CUSTOM_TAXONOMY_DIR' , dirname(__FILE__) );
	    
	    if ( !file_exists( CUSTOM_TAXONOMY_DIR . '/config/config.php') ){
	    	add_action('admin_notices', array(&$this,'custom_taxonomy_admin_notices'));
	    	return;
	    }
	    
		if ( is_multisite( ) ){
	    	global $blog_id;
	    	if ( file_exists( CUSTOM_TAXONOMY_DIR . '/config/config-'.$blog_id.'.php' ) ){
	    		include_once ( CUSTOM_TAXONOMY_DIR . '/config/config-'.$blog_id.'.php' );	
	    	}else{
	    		include_once ( CUSTOM_TAXONOMY_DIR . '/config/config.php' );
	    	}
		}else{
	    	include_once ( CUSTOM_TAXONOMY_DIR . '/config/config.php' );
		}

	    $this->Start( );

	}

	/*
	 *プラグインの機能実行をスタート 
	 */
	function Start(){

		if ( is_admin( ) ){

			//タクソノミーの編集画面のカスタマイズ（表示する場合）
			add_action( $_GET['taxonomy'] . '_add_form_fields'  		, array( &$this, 'admin_custom_taxonomy_add' ) );
			add_action( $_GET['taxonomy'] . '_edit_form_fields'  		, array( &$this, 'admin_custom_taxonomy_edit' ) );
			add_action( $_GET['taxonomy'] . '_pre_add_form'				, array( &$this, 'taxonomy_explain_text' ) );

			//タクソノミーの編集、追加、削除
			add_action( 'created_term' 									, array( &$this, 'update_taxonomy_item' ) );
			add_action( 'edited_terms' 									, array( &$this, 'update_taxonomy_item' ) );//edited_term_taxonomy
			add_action( 'delete_term'									, array( &$this, 'delete_taxonomy_item' ) );//delete_term_taxonomy

			//表示する項目（item）
			add_filter( 'manage_edit-' . $_GET['taxonomy'] . '_columns'	, array( &$this, 'custom_taxonomy_columns' ) );
			add_filter( 'manage_'. $_GET['taxonomy'] .'_custom_column'	, array( &$this, 'display_custom_taxonomy_column' ), 10, 3 );
			
			add_action( 'admin_print_styles-edit-tags.php'					, array( &$this, 'add_admin_footer_scripts' ) );
			
			//選択カテゴリをTOPにしない、カテゴリ一覧のソートを維持
			add_filter( 'wp_terms_checklist_args', array(&$this,'ps_wp_terms_checklist_args' ),10,2);
		}else{
			add_filter( 'get_terms' 									, array( &$this, 'ps_custom_taxonomy_terms') , 10 , 3);
			add_filter( 'get_the_terms' 								, array( &$this, 'ps_custom_taxonomy_the_terms') , 10 , 3);
			add_filter( 'get_term' 										, array( &$this, 'ps_custom_taxonomy_term') , 10 , 2);
		}	
	}
	
	/**
	* ファンクション名：add_admin_footer_scripts
	* 機能概要：javascripts css
	* 作成：プライム・ストラテジー株式会社 王 濱
	* 変更：
	* @param resource
	* @param int
	* @param string
	* @return
	*/
	function add_admin_footer_scripts(){
		//カスタマイズ処理
		//css
		
		wp_register_style( 'prefix-style-' . __CLASS__ , plugins_url('css/prefix-style.css', __FILE__) );
		
		wp_enqueue_style( 'prefix-style-' . __CLASS__ );

		wp_enqueue_script( 'prefix-js-' . strtolower(__CLASS__) , plugins_url('js/prefix-js.js', __FILE__) ,  array( 'jquery' ) );
		//JS 

		
	}

	/**
	* ファンクション名：admin_custom_taxonomy_add
	* 機能概要：タクソノミーを追加する時、カスタマイズ項目を表示する
	* 作成：プライム・ストラテジー株式会社 王 濱
	* 変更：
	* @param string $taxonomy(category)
	* @return なし
	*/
	function admin_custom_taxonomy_add( $taxonomy ){
		
		if ( $this->_items ):
			foreach ( $this->_items as $key => $field ):
			
				$func = 'make_' . $field['type'];
					
				$this->$func($taxonomy, $key , null );	
					
			endforeach;
			$this->make_hidden_taxonomy_verify_key( );
		endif;
		
	}
	
	/**
	* ファンクション名：admin_custom_taxonomy_edit
	* 機能概要：タクソノミーを編集する場合、カスタマイズ項目を表示する
	* 作成：プライム・ストラテジー株式会社 王 濱
	* 変更：
	* @param string $taxonomy(category)
	* @return
	*/
	function admin_custom_taxonomy_edit( $taxonomy ){
		if ( $this->_items ):
			foreach ( $this->_items as $key => $field ):
			
				$func = 'make_' . $field['type'];
				
				$value = get_option( $taxonomy->taxonomy . '-' . $key );
				
				$term_id = $taxonomy->term_id;
				
				$this->$func( $taxonomy->taxonomy, $key , $value[$term_id] , true );	
					
			endforeach;
			$this->make_hidden_taxonomy_verify_key( );
		endif;
	}
	
	/**
	* ファンクション名：admin_custom_taxonomy_edit
	* 機能概要：タクソノミーを編集する場合、カスタマイズ項目を表示する
	* 作成：プライム・ストラテジー株式会社 王 濱
	* 変更：
	* @param string $taxonomy(category)
	* @return
	*/
	function make_hidden_taxonomy_verify_key(  ){
		$hidden = '<div><input type="hidden" name="taxonomy-verify-key" id="taxonomy-verify-key" value="'. wp_create_nonce('taxonomy_verify_key') .'" /></div>';
		echo $hidden;	
	}
	/**
	* ファンクション名：make_textfield
	* 機能概要：textfieldの項目を作成
	* 作成：プライム・ストラテジー株式会社 王 濱
	* 変更：
	* @param string $taxonomy(category)
	* @param string $key(item key)
	* @param string $value
	* @return
	*/
	function make_textfield($taxonomy, $key, $value , $edit = false ){
		$field = $this->_items[$key];
		if ( is_array($field['taxonomy']) && in_array($taxonomy,$field['taxonomy'])):
			if ( $edit === true ):
?>
				<tr class="form-field">
					<th scope="row" valign="top">
						<label for="<?php echo $taxonomy . '-' . $key ?>"><?php echo $field['name']; ?></label>
					</th>
					<td>
						<input type="text" name="<?php echo $key;?>" id="<?php echo $taxonomy . '-' . $key ?>" size=40 value="<?php echo isset( $value ) ? esc_attr( $value ) : ''; ?>" />
						<?php echo $field['description'];?> 	
					</td> 
				</tr>
			<?php else:?>
				<div class="form-field">
					<label for="<?php echo $taxonomy . '-' . $key ?>"><?php echo $field['name']; ?></label>
					<input type="text" name="<?php echo $key;?>" id="<?php echo $taxonomy . '-' . $key ?>" size=40 value="<?php echo isset( $value ) ? esc_attr( $value ) : ''; ?>" />
					<?php echo $field['description'];?> 				 
				</div>
<?php
			endif;
		endif;

	}

	/**
	* ファンクション名：make_textarea
	* 機能概要：make_textareaの項目を作成
	* 作成：プライム・ストラテジー株式会社 王 濱
	* 変更：
	* @param string $taxonomy(category)
	* @param string $key(item key)
	* @param string $value
	* @return
	*/
	function make_textarea($taxonomy, $key, $value, $edit = false ){
		$field = $this->_items[$key];
		if ( is_array($field['taxonomy']) && in_array($taxonomy,$field['taxonomy'])):
			if ( $edit === true ):
?>
				<tr class="form-field">
					<th scope="row" valign="top">
						<label for="<?php echo $taxonomy . '-' . $key ?>"><?php echo $field['name']; ?></label>
					</th>
					<td>
						<textarea cols="<?php echo $field['cols'];?>" rows="<?php echo $field['rows'];?>" name="<?php echo $key;?>" id="<?php echo $taxonomy . '-' . $key ?>"><?php echo isset( $value ) ? esc_attr( $value ) : ''; ?></textarea>
						<?php echo $field['description'];?> 				
					</td>
				</tr>
			<?php else: ?>
				<div class="form-field">
					<label for="<?php echo $taxonomy . '-' . $key ?>"><?php echo $field['name']; ?></label>
					<textarea cols="<?php echo $field['cols'];?>" rows="<?php echo $field['rows'];?>" name="<?php echo $key;?>" id="<?php echo $taxonomy . '-' . $key ?>"><?php echo isset( $value ) ? esc_attr( $value ) : ''; ?></textarea>
					<?php echo $field['description'];?> 				
				</div>						
<?php
			endif;
		endif;
	}

	/**
	* ファンクション名：make_radio
	* 機能概要：make_radioの項目を作成
	* 作成：プライム・ストラテジー株式会社 王 濱
	* 変更：
	* @param string $taxonomy(category)
	* @param string $key(item key)
	* @param string $value
	* @return
	*/
	function make_radio($taxonomy, $key, $value, $edit = false){
		$field = $this->_items[$key];
		if ( is_array($field['taxonomy']) && in_array($taxonomy,$field['taxonomy'])):
			if ( $edit === true ):
?>
				<tr class="form-field">
					<th scope="row" valign="top">
						<label for="<?php echo $taxonomy . '-' . $key ?>"><?php echo $field['name']; ?></label>
					</th>
					<td>
				<?php foreach( $field['values'] as $key2 => $val):
						$checked = ( trim($val) == $value ) ? ' checked="checked"' : ' ';	?>					
						<label for="<?php echo $taxonomy . '-' . $key . '-' . $key2; ?>">
							<input class="type_check" type="radio" name="<?php echo $key;?>" id="<?php echo $taxonomy . '-' . $key . '-' . $key2 ?>" value="<?php echo $val; ?>" <?php echo $checked; ?> />
							<?php echo $val; ?>
						</label><br />
				<?php endforeach; ?>				
					<?php echo $field['description'];?>
					</td>
				</tr>
			<?php else: ?>
				<div class="form-field">
					<label for="<?php echo $taxonomy . '-' . $key ?>"><?php echo $field['name']; ?></label>
				<?php foreach( $field['values'] as $key2 => $val):
						$checked = ( trim($val) ==  $field['checked'] ) ? ' checked="checked"' : ' '; ?>					
						<label for="<?php echo $taxonomy . '-' . $key . '-' . $key2; ?>">
							<input class="type_check" type="radio" name="<?php echo $key;?>" id="<?php echo $taxonomy . '-' . $key . '-' . $key2 ?>" value="<?php echo $val; ?>" <?php echo $checked; ?> />
							<?php echo $val; ?>
						</label>
				<?php endforeach; ?>				
					<?php echo $field['description'];?>
				</div>
<?php
			endif;
		endif;
	}	

	/**
	* ファンクション名：make_checkbox
	* 機能概要：make_checkboxの項目を作成
	* 作成：プライム・ストラテジー株式会社 王 濱
	* 変更：
	* @param string $taxonomy(category)
	* @param string $key(item key)
	* @param string $value
	* @return
	*/
	function make_checkbox($taxonomy, $key, $value, $edit = false){
		$field = $this->_items[$key];
		if ( is_array($field['taxonomy']) && in_array($taxonomy,$field['taxonomy'])):
			if ( $edit === true ):
?>
			<tr class="form-field">
				<th scope="row" valign="top">
					<label for="<?php echo $taxonomy . '-' . $key ?>"><?php echo $field['name']; ?></label>
				</th>
				<td>
			<?php foreach( $field['values'] as $key2 => $val):
					$value = (isset($value) && is_array($value)) ? $value : array($value);
					$checked = ( is_array($value) && in_array( trim($val), $value) ) ? ' checked="checked"' : ' ';?>					
					<label for="<?php echo $taxonomy . '-' . $key . '-' . $key2; ?>">
						<input class="type_check" type="checkbox" name="<?php echo $key;?>[]" id="<?php echo $taxonomy . '-' . $key . '-' . $key2 ?>" value="<?php echo $val; ?>" <?php echo $checked; ?> />
						<?php echo $val; ?>
					</label><br />
			<?php endforeach; ?>				
				<?php echo $field['description'];?>
				</td>
			</tr>
		<?php else: ?>
			<div class="form-field">
				<label for="<?php echo $taxonomy . '-' . $key ?>"><?php echo $field['name']; ?></label>
			<?php foreach( $field['values'] as $key2 => $val):
					$value = (isset($value) && is_array($value)) ? $value : array($value);
					$checked = ( is_array($field['checked']) && in_array(trim($val), $field['checked'] ) ) ? ' checked="checked"' : ' '; ?>					
					<label for="<?php echo $taxonomy . '-' . $key . '-' . $key2; ?>">
						<input class="type_check" type="checkbox" name="<?php echo $key;?>[]" id="<?php echo $taxonomy . '-' . $key . '-' . $key2 ?>" value="<?php echo $val; ?>" <?php echo $checked; ?> />
						<?php echo $val; ?>
					</label>
			<?php endforeach; ?>				
				<?php echo $field['description'];?>
			</div>
<?php
			endif;
		endif;	
	}	

	/**
	* ファンクション名：make_select
	* 機能概要：make_selectの項目を作成
	* 作成：プライム・ストラテジー株式会社 王 濱
	* 変更：
	* @param string $taxonomy(category)
	* @param string $key(item key)
	* @param string $value
	* @return
	*/
	function make_select($taxonomy, $key, $value , $edit = false ){
		$field = $this->_items[$key];
		if ( is_array($field['taxonomy']) && in_array($taxonomy,$field['taxonomy'])):
			if ( $edit === true ):
?>
				<tr class="form-field">
					<th scope="row" valign="top">
						<label for="<?php echo $taxonomy . '-' . $key ?>"><?php echo $field['name']; ?></label>
					</th>
					<td>
						<select name="<?php echo $key;?>" id="<?php echo $taxonomy . '-' . $key ?>" class="postform" >
							<option value="">なし</option>
					<?php	foreach( $field['values'] as $key2 => $val):
								$checked = ( trim($val) == $value ) ? ' selected="selected"' : ' ';	?>
								<option class="level-0" value="<?php echo $val;?>" <?php echo $checked; ?>><?php echo $val?></option>
						<?php endforeach; ?>
						</select>			
					<?php echo $field['description'];?>
					</td>
				</tr>
			<?php else:?>
				<div class="form-field">
					<label for="<?php echo $taxonomy . '-' . $key ?>"><?php echo $field['name']; ?></label>
					<select name="<?php echo $key;?>" id="<?php echo $taxonomy . '-' . $key ?>" class="postform" >
						<option value="">なし</option>
						<?php foreach( $field['values'] as $key2 => $val):
							$checked =  ( trim($val) ==  $field['selected'] ) ? ' selected="selected"' : ' ';
						?>
							<option class="level-0" value="<?php echo $val;?>" <?php echo $checked; ?>><?php echo $val?></option>
						<?php endforeach; ?>
					</select>			
					<?php echo $field['description'];?>
				</div>
<?php
			endif;
		endif;
	}		
	
	/**
	* ファンクション名：taxonomy_explain_text
	* 機能概要：説明を追加する
	* 作成：プライム・ストラテジー株式会社 王 濱
	* 変更：
	* @param なし
	* @return なし
	*/
	function taxonomy_explain_text( ){
		if ( is_array( $this->explain_text['taxonomy'] ) && in_array($_GET['taxonomy'],$this->explain_text['taxonomy']) ){
			if ( $this->explain_text['title'] ):
				echo $this->explain_text['title'];
			endif;
			if ( $this->explain_text['p'] ):
				echo $this->explain_text['p'];
			endif;
		}
	}
	/**
	* ファンクション名：update_taxonomy_item
	* 機能概要：タクソノミーの追加する項目を編集する
	* 作成：プライム・ストラテジー株式会社 王 濱
	* 変更：
	* @param int $term_id
	* @return なし
	*/
	function update_taxonomy_item( $term_id ){
		$nonce = isset($_REQUEST['taxonomy-verify-key']) ? $_REQUEST['taxonomy-verify-key']: '';
	    if (!wp_verify_nonce($nonce, 'taxonomy_verify_key')) {
       		return false;
		}
		foreach ( $this->_items as $key => $val ):

			if ( is_array( $val['taxonomy']) && in_array( $_POST['taxonomy'] , $val['taxonomy'] ) ):

				$post_option = stripslashes_deep( $_POST[$key] );
	
				$current_option = get_option( $_POST['taxonomy'] . '-' . $key );
			
				if ( ! isset( $current_option[$term_id] ) || $current_option[$term_id] != $post_option ):
				
					$current_option[$term_id] = $post_option;

					update_option( $_POST['taxonomy'] . '-' . $key, $current_option );
					
				endif;
			endif;
		endforeach;
	}	
	
	/**
	* ファンクション名： delete_taxonomy_item
	* 機能概要：タクソノミーの削除とおともに、追加す項目を削除する
	* 作成：プライム・ストラテジー株式会社 王 濱
	* 変更：
	* @param int $term_id
	* @return なし
	*/
	function delete_taxonomy_item( $term_id ){

		foreach ( $this->_items as $key => $val ):
		
			if ( is_array( $val['taxonomy']) && in_array( $_POST['taxonomy'] , $val['taxonomy'] ) ):
				$current_option = get_option( $_POST['taxonomy'] . '-' . $key );	
				unset( $current_option[$term_id] );
			endif;
			
			update_option( $_POST['taxonomy'] . '-' . $key, $current_option );
			
		endforeach;
	}
	
	/**
	* ファンクション名：display_custom_taxonomy_column
	* 機能概要：タクソノミー一覧に表示を追加する
	* 作成：プライム・ストラテジー株式会社 王 濱
	* 変更：
	* @param array $columns
	* @return
	*/
	function custom_taxonomy_columns( $columns ){

		foreach ( $columns as $key => $column ):
			if ( $key == 'posts' ):
				foreach ( $this->_items as $key2 => $val):
					if ( is_array( $val['taxonomy'] ) && in_array( $_GET['taxonomy'], $val['taxonomy'] ) && ( $val['disply'] || $val['display'] )):
						$sort_columns[$key2] = $val['name'];
					endif;
				endforeach;
			endif;
			$sort_columns[$key] = $column;
		endforeach;

		$columns = $sort_columns;
		
		return $columns;
		
	}
	
	/**
	* ファンクション名：display_custom_taxonomy_column
	* 機能概要：タクソノミー一覧に表示を追加する（表示データ）
	* 作成：プライム・ストラテジー株式会社 王 濱
	* 変更：
	* @param string $output
	* @param string $column_name
	* @param int $term_id
	* @return
	*/
	function display_custom_taxonomy_column( $output, $column_name, $term_id ){

		foreach ( $this->_items as $key => $val):
			if ( is_array( $val['taxonomy'] ) && in_array( $_GET['taxonomy'], $val['taxonomy'] ) && ( $val['disply'] || $val['display']) && $column_name == $key ):
				$current_option = get_option( $_GET['taxonomy'] . '-' . $key );
				
				$current_option[$term_id] = ( is_array( $current_option[$term_id] ) ) ? join(',',$current_option[$term_id] ) : $current_option[$term_id] ;

				$output = isset( $current_option[$term_id] ) ? esc_html( $current_option[$term_id] ) : '&nbsp;';
				
				esc_html($output);
			endif;
		endforeach;

		return $output;
	}
	
	/**
	* ファンクション名：ps_custom_taxonomy_terms
	* 機能概要：タクソノミーを取得するObjectにカスタマイズデータを追加する
	* 作成：プライム・ストラテジー株式会社 王 濱
	* 変更：
	* @param object $terms
	* @param array $taxonomies
	* @param array $args
	* @return
	*/
	function ps_custom_taxonomy_terms ( $terms, $taxonomies, $args ){

		foreach ( $this->_items as $key => $val):
			if ( is_array( $val['taxonomy']) && in_array( $taxonomies[0] , $val['taxonomy'] ) ):
				$current_option[$key] = get_option( $taxonomies[0] . '-' . $key);
			endif;
		endforeach;
		
		foreach ( $terms as $key => $term ):
			foreach ( $this->_items as $key2 => $val):
				$taxonomy_val = $current_option[$key2][$term->term_id];
				if ( $taxonomy_val ):
					$terms[$key]->$key2 = $taxonomy_val;
				endif;
			endforeach;
		endforeach;

		return $terms;
	}

	/**
	* ファンクション名：ps_custom_taxonomy_the_terms
	* 機能概要：タクソノミーを取得するObjectにカスタマイズデータを追加する
	* 作成：プライム・ストラテジー株式会社 王 濱
	* 変更：
	* @param resource $terms
	* @param int $id
	* @param string $taxonomy
	* @return
	*/
	function ps_custom_taxonomy_the_terms( $terms, $id, $taxonomy ){
		
		foreach ( $this->_items as $key => $val):
			if ( is_array( $val['taxonomy']) && in_array( $taxonomy , $val['taxonomy'] ) ):
				$current_option[$key] = get_option( $taxonomy . '-' . $key);
			endif;
		endforeach;
		
		if ( ! $terms ){
			return $terms;
		}
		
		foreach ( $terms as $key => $term ):
			foreach ( $this->_items as $key2 => $val):
				$taxonomy_val = $current_option[$key2][$term->term_id];
				if ( $taxonomy_val ):
					$terms[$key]->$key2 = $taxonomy_val;
				endif;
			endforeach;
		endforeach;

		return $terms;	
	}
	
	/**
	* ファンクション名：ps_custom_taxonomy_term
	* 機能概要：タクソノミーを取得するObjectにカスタマイズデータを追加する
	* 作成：プライム・ストラテジー株式会社 王 濱
	* 変更：
	* @param onject $term
	* @param string $taxonomy
	* @return
	*/	
	function ps_custom_taxonomy_term( $term, $taxonomy ){
		
		foreach ( $this->_items as $key => $val):
		
			if ( is_array( $val['taxonomy'] ) && in_array( $taxonomy , $val['taxonomy'] ) ):
				$current_option = get_option( $taxonomy . '-' . $key );
				if ( $current_option[$term->term_id] ){
					$term->$key = $current_option[$term->term_id];
				}			
			endif;
			
		endforeach;
		
		return $term;

	}
	
     /** 
     * ファンクション名：ps_wp_terms_checklist_args
     * 機能概要：選択カテゴリをTOPにしない、カテゴリ一覧のソートを維持
     * 作成：プライム・ストラテジー株式会社 王 濱
     * 変更：
     * @param なし
     * @return  なし
     */
    function ps_wp_terms_checklist_args( $args, $post_id  ){
    	if ( $args['checked_ontop'] !== false ){
   			$args['checked_ontop'] = false;
    	}
    	return $args;
    	 	
    }
    
	/**
	* ファンクション名：custom_taxonomy_admin_notices
	* 機能概要：設定ファイルなし、警告メッセージ
	* 作成：プライム・ストラテジー株式会社 王 濱
	* 変更：
	*/	
	function custom_taxonomy_admin_notices(){
		echo '<div class="error" style="text-align: center;"><p style="color: red; font-size: 14px; font-weight: bold;">プラグイン011PS Custom Taxonomy ： 設定ファイル<strong>_config.php</strong>の名前を<strong>config.php</strong>に変更し、<strong>config.php</strong>の設定を行ってください。</p></div>';
	}
	
	
	
}//class end

$Ps_Custom_Taxonomy = new Ps_Custom_Taxonomy();

?>
