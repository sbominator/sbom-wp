<?php
/**
 * Plugin Name:     Sbominator
 * Text Domain:     sbominator
 * Domain Path:     /languages
 * Version:         0.1.0
 *
 * @package         Sbominator
 */

// Your code starts here.

include 'vendor/autoload.php';

add_filter('debug_information', 'sbominator_debug_info');
add_action('admin_init', 'sbominator_check_for_download');

use SBOMinator\Scanner\FileScanner;

function sbominator_check_for_download(){

	if (isset($_GET['sbom']) && $_GET['sbom'] == 'plugins_themes')
		sbominator_get_plugins_themes_scan();
	if (isset($_GET['sbom']) && $_GET['sbom'] == 'wordpress')
		sbominator_get_wordpress_sbom();
}

function sbomniator_array_to_json_download($data =[], $name = '') {

	if (!is_array($data))
		return '';

	header('Content-Type: application/json');
	header('Content-Disposition: attachment; filename=SBOM'.$name.'.json');
	header('Pragma: no-cache');

	echo json_encode($data);
	die();
}

function sbominator_get_wordpress_sbom(){
	$scanner = new \Scanninator\Scanninator('https://github.com/WordPress/WordPress');
	sbomniator_array_to_json_download($scanner->get_sbom(),'WordPress');
}

function sbominator_get_plugins_themes_scan(){
	$scanner = new \SBOMinator\Scanner\FileScanner(10, ['json', 'lock']);
	sbomniator_array_to_json_download($scanner->scanForDependencies(WP_CONTENT_DIR),'WordPress Plugins/Themes');
}

function sbominator_debug_info($info) {

	$info["sbominator"] = [
		"label" => "SBOM",
		"description" => "SBOM output for the WordPress installation",
		"fields" => [
				"WordPress" => [
					'label' => 'WordPress',
					'value' => '<a href="'.add_query_arg( ['sbom' => 'wordpress']) .'">Download SBOM of WordPress</a>'
				],
				"Plugin/Themes" => [
					'label' => 'Plugin/Themes',
					'value' => '<a href="'.add_query_arg( ['sbom' => 'plugins_themes']) .'">Download SBOM of Plugins and Themes</a>'
				]
		]
	];

	return $info;
}
