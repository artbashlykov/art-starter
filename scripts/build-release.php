<?php
/**
 * Build GitHub Release zip for ART Starter.
 *
 * Usage: php scripts/build-release.php [output-path]
 *
 * @package Art_Starter
 */

if ( 'cli' === PHP_SAPI && ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ );
}

defined( 'ABSPATH' ) || exit;

/**
 * Write a message to STDERR in CLI mode.
 *
 * @param string $art_starter_message Message text.
 */
function art_starter_build_release_stderr( $art_starter_message ) {
	// phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fwrite -- CLI build script only.
	fwrite( STDERR, $art_starter_message );
}

/**
 * Build release zip archive.
 *
 * @param array<int, string> $art_starter_argv CLI arguments.
 * @return int Exit code.
 */
function art_starter_build_release( array $art_starter_argv ) {
	if ( ! class_exists( 'ZipArchive' ) ) {
		art_starter_build_release_stderr( "ZipArchive is required.\n" );
		return 1;
	}

	$art_starter_plugin_dir = dirname( __DIR__ );
	$art_starter_slug       = basename( $art_starter_plugin_dir );
	$art_starter_output     = $art_starter_argv[1] ?? ( sys_get_temp_dir() . DIRECTORY_SEPARATOR . $art_starter_slug . '.zip' );

	$art_starter_exclude_dirs          = array( '.git', '.cursor', '.idea', '.vscode', 'node_modules', 'scripts' );
	$art_starter_exclude_file_patterns = array(
		'*.zip',
		'*.log',
		'tmp-*.php',
		'local-*.php',
	);

	/**
	 * Whether a path should be excluded from the release archive.
	 *
	 * @param string $art_starter_relative_path Path relative to plugin root.
	 */
	$art_starter_should_exclude = static function ( $art_starter_relative_path ) use ( $art_starter_exclude_dirs, $art_starter_exclude_file_patterns ) {
		$art_starter_relative_path = str_replace( '\\', '/', $art_starter_relative_path );
		$art_starter_parts         = explode( '/', $art_starter_relative_path );

		foreach ( $art_starter_parts as $art_starter_part ) {
			if ( in_array( $art_starter_part, $art_starter_exclude_dirs, true ) ) {
				return true;
			}
		}

		$art_starter_basename = basename( $art_starter_relative_path );
		foreach ( $art_starter_exclude_file_patterns as $art_starter_pattern ) {
			if ( fnmatch( $art_starter_pattern, $art_starter_basename ) ) {
				return true;
			}
		}

		return false;
	};

	$art_starter_zip    = new ZipArchive();
	$art_starter_opened = $art_starter_zip->open( $art_starter_output, ZipArchive::OVERWRITE | ZipArchive::CREATE );

	if ( true !== $art_starter_opened ) {
		art_starter_build_release_stderr( 'Cannot create zip: ' . $art_starter_output . "\n" );
		return 1;
	}

	$art_starter_iterator = new RecursiveIteratorIterator(
		new RecursiveDirectoryIterator( $art_starter_plugin_dir, RecursiveDirectoryIterator::SKIP_DOTS )
	);

	foreach ( $art_starter_iterator as $art_starter_file_info ) {
		/**
		 * SplFileInfo instance for the current archive entry.
		 *
		 * @var SplFileInfo $art_starter_file_info
		 */
		$art_starter_absolute_path = $art_starter_file_info->getPathname();
		$art_starter_relative_path = substr( $art_starter_absolute_path, strlen( $art_starter_plugin_dir ) + 1 );

		if ( $art_starter_should_exclude( $art_starter_relative_path ) ) {
			continue;
		}

		$art_starter_zip_path = $art_starter_slug . '/' . str_replace( '\\', '/', $art_starter_relative_path );

		if ( $art_starter_file_info->isDir() ) {
			$art_starter_zip->addEmptyDir( rtrim( $art_starter_zip_path, '/' ) );
			continue;
		}

		$art_starter_zip->addFile( $art_starter_absolute_path, $art_starter_zip_path );
	}

	$art_starter_zip->close();

	// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- CLI outputs a local filesystem path.
	echo $art_starter_output, PHP_EOL;

	return 0;
}

if ( 'cli' !== PHP_SAPI ) {
	exit;
}

// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- CLI exit code, not rendered output.
exit( art_starter_build_release( $argv ) );
