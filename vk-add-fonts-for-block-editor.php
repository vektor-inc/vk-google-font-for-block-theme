<?php
/**
 * Plugin Name: VK Add Fonts for Block Editor
 * Plugin URI:
 * Description: ブロックエディタのタイポグラフィーのフォント選択で Google Web Font の Noto Sans JP と Noto Serif JP を追加します。
 * Version: 0.2.0.4
 * Requires PHP: 7.4
 * Requires at least: 6.3
 * Author: Vektor,Inc.
 * Text Domain:
 * Domain Path: /languages
 * Author URI: https://vektor-inc.co.jp
 * License: GPL2
 *
 * @package vk-add-fonts-for-block-editor
 */

// Composer のファイルを読み込み ( composer install --no-dev ).
require_once plugin_dir_path( __FILE__ ) . 'vendor/autoload.php';
use YahnisElsts\PluginUpdateChecker\v5\PucFactory;

$my_update_checker = PucFactory::buildUpdateChecker(
	'https://github.com/vektor-inc/vk-add-fonts-for-block-editor/',
	__FILE__,
	'vk-add-fonts-for-block-editor'
);

// Set the branch that contains the stable release.
$my_update_checker->setBranch( 'main' );

// $my_update_checker->getVcsApi()->enableReleaseAssets();

class Vk_Add_Fonts_For_Block_Editor {
	/**
	 * Construct
	 */
	public function __construct() {
		// プラグインが有効化されたときに呼び出される関数.
		register_activation_hook( __FILE__, array( $this, 'deactivate_other_plugin' ) );

		// フィルターとアクションの登録.
		add_filter( 'wp_theme_json_data_theme', array( $this, 'add_font_theme_json_filter' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'load_web_fonts' ) );
		add_action( 'enqueue_block_editor_assets', array( $this, 'load_web_fonts' ) );
	}

	/**
	 * Deactivate old plugin
	 */
	public function deactivate_other_plugin() {
		// プラグインのパスを指定.
		$plugin = 'vk-google-font-for-block-themes/vk-google-font-for-block-themes.php';

		// プラグインが有効化されている場合は無効化.
		if ( is_plugin_active( $plugin ) ) {
			deactivate_plugins( $plugin );
		}
	}

	/**
	 * theme.json にフォントを追加するフィルター.
	 */
	public function add_font_theme_json_filter( $theme_json ) {
		// theme.json の内容を一旦配列で変数に格納.
		$get_data = $theme_json->get_data();
		// 追加するフォントの配列.
		$add_font_array = array(
			array(
				'fontFamily' => 'Noto Sans JP, sans-serif',
				'name'       => 'Noto Sans JP',
				'slug'       => 'noto-sans-jp',
				'fontFace'   => array(
					array(
						'fontFamily' => 'Noto Sans JP, sans-serif',
						'fontStyle'  => 'normal',
						'fontWeight' => '700',
						'src'        => array( 'https://fonts.googleapis.com/css2?family=Noto+Sans+JP:wght@700&display=swap' ),
					),
					array(
						'fontFamily' => 'Noto Sans JP, sans-serif',
						'fontStyle'  => 'normal',
						'fontWeight' => '500',
						'src'        => array( 'https://fonts.googleapis.com/css2?family=Noto+Sans+JP:wght@500&display=swap' ),
					),
					array(
						'fontFamily' => 'Noto Sans JP, sans-serif',
						'fontStyle'  => 'normal',
						'fontWeight' => '400',
						'src'        => array( 'https://fonts.googleapis.com/css2?family=Noto+Sans+JP:wght@400&display=swap' ),
					),
					array(
						'fontFamily' => 'Noto Sans JP, sans-serif',
						'fontStyle'  => 'normal',
						'fontWeight' => '300',
						'src'        => array( 'https://fonts.googleapis.com/css2?family=Noto+Sans+JP:wght@300&display=swap' ),
					),
				),
			),
			array(
				'fontFamily' => 'Noto Serif JP, serif',
				'name'       => 'Noto Serif JP',
				'slug'       => 'noto-serif-jp',
				'fontFace'   => array(
					array(
						'fontFamily' => 'Noto Serif JP, serif',
						'fontStyle'  => 'normal',
						'fontWeight' => '700',
						'src'        => array( 'https://fonts.googleapis.com/css2?family=Noto+Serif+JP:wght@700&display=swap' ),
					),
					array(
						'fontFamily' => 'Noto Serif JP, serif',
						'fontStyle'  => 'normal',
						'fontWeight' => '500',
						'src'        => array( 'https://fonts.googleapis.com/css2?family=Noto+Serif+JP:wght@500&display=swap' ),
					),
					array(
						'fontFamily' => 'Noto Serif JP, serif',
						'fontStyle'  => 'normal',
						'fontWeight' => '400',
						'src'        => array( 'https://fonts.googleapis.com/css2?family=Noto+Serif+JP:wght@400&display=swap' ),
					),
					array(
						'fontFamily' => 'Noto Serif JP, serif',
						'fontStyle'  => 'normal',
						'fontWeight' => '300',
						'src'        => array( 'https://fonts.googleapis.com/css2?family=Noto+Serif+JP:wght@300&display=swap' ),
					),
				),
			),
		);
		// フォントをマージ.
		$font_families = isset( $get_data['settings']['typography']['fontFamilies']['theme'] ) && is_array( $get_data['settings']['typography']['fontFamilies']['theme'] )
			? array_merge(
				$get_data['settings']['typography']['fontFamilies']['theme'],
				$add_font_array
			)
			: $add_font_array; // もし配列でない場合は $add_font_array のみを使用.
		$new_data      = array(
			'version'  => 3,
			'settings' => array(
				'typography' => array(
					'fontFamilies' => $font_families,
				),
			),
		);
		// return $theme_json;
		return $theme_json->update_with( $new_data );
	}

	/**
	 * Google Web Font を読み込む
	 * theme.json の src での指定は 6.3 ではまだ効かないっぽいためここで手動で読み込む
	 *
	 * @return void
	 */
	public function load_web_fonts() {
		// Noto Sans JP 読み込み.
		wp_enqueue_style( 'load-font-noto-sans', 'https://fonts.googleapis.com/css2?family=Noto+Sans+JP:wght@300;400;500;700&display=swap', array(), null );
		// Noto Serif JP 読み込み.
		wp_enqueue_style( 'load-font-noto-serif', 'https://fonts.googleapis.com/css2?family=Noto+Serif+JP:wght@300;400;500;700&display=swap', array(), null );
	}
}

// クラスのインスタンスを作成
new Vk_Add_Fonts_For_Block_Editor();
