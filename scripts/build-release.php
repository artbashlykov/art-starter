<?php
/**
 * Build GitHub Release zip for ART Starter.
 *
 * Usage: php scripts/build-release.php [output-path]
 *
 * @package Art_Starter
 */

if ( PHP_SAPI !== 'cli' ) {
	fwrite( STDERR, "Run from CLI only.\n" );
	exit( 1 );
}

if ( ! class_exists( 'ZipArchive' ) ) {
	fwrite( STDERR, "ZipArchive is required.\n" );
	exit( 1 );
}

$plugin_dir = dirname( __DIR__ );
$slug       = basename( $plugin_dir );
$output     = $argv[1] ?? ( sys_get_temp_dir() . DIRECTORY_SEPARATOR . $slug . '.zip' );

$exclude_dirs = array( '.git', '.cursor', '.idea', '.vscode', 'node_modules' );
$exclude_file_patterns = array(
	'*.zip',
	'*.log',
	'tmp-*.php',
	'local-*.php',
);

/**
 * @param string $relative_path Path relative to plugin root.
 */
$should_exclude = static function ( $relative_path ) use ( $exclude_dirs, $exclude_file_patterns ) {
	$relative_path = str_replace( '\\', '/', $relative_path );
	$parts         = explode( '/', $relative_path );

	foreach ( $parts as $part ) {
		if ( in_array( $part, $exclude_dirs, true ) ) {
			return true;
		}
	}

	$basename = basename( $relative_path );
	foreach ( $exclude_file_patterns as $pattern ) {
		if ( fnmatch( $pattern, $basename ) ) {
			return true;
		}
	}

	return false;
};

$zip = new ZipArchive();
$opened = $zip->open( $output, ZipArchive::OVERWRITE | ZipArchive::CREATE );

if ( true !== $opened ) {
	fwrite( STDERR, "Cannot create zip: {$output}\n" );
	exit( 1 );
}

$iterator = new RecursiveIteratorIterator(
	new RecursiveDirectoryIterator( $plugin_dir, RecursiveDirectoryIterator::SKIP_DOTS )
);

foreach ( $iterator as $file_info ) {
	/** @var SplFileInfo $file_info */
	$absolute_path = $file_info->getPathname();
	$relative_path = substr( $absolute_path, strlen( $plugin_dir ) + 1 );

	if ( $should_exclude( $relative_path ) ) {
		continue;
	}

	$zip_path = $slug . '/' . str_replace( '\\', '/', $relative_path );

	if ( $file_info->isDir() ) {
		$zip->addEmptyDir( rtrim( $zip_path, '/' ) );
		continue;
	}

	$zip->addFile( $absolute_path, $zip_path );
}

$zip->close();

echo $output, PHP_EOL;
