<?php

namespace EllisLab\ExpressionEngine\Cli\Commands\Upgrade;

use EllisLab\ExpressionEngine\Library\Filesystem\Filesystem;

class UpgradeUtility
{

	public static function run()
	{
		// self::install_modules();
		self::remove_installer_directory();
	}

	protected static function install_modules()
	{
		$required_modules = [
			'channel',
			'comment',
			'consent',
			'member',
			'stats',
			'rte',
			'file',
			'filepicker',
			'relationship',
			'search'
		];

		ee()->load->library('addons');

		if( ! ee()->addons ) {
			ee()->addons->install_modules($required_modules);
		}

		$consent = ee('Addon')->get('consent');
		if($consent) {
			$consent->installConsentRequests();
		}
	}

	protected static function remove_installer_directory()
	{
		$installerPath = SYSPATH . 'ee/installer';

		if(is_dir($installerPath)) {
			self::rmrf($installerPath);
		}
	}

	private static function rmrf($dir) {
	    foreach (glob($dir) as $file) {
	        if (is_dir($file)) { 
	            self::rmrf("$file/*");
	            @rmdir($file);
	        } else {
	            @unlink($file);
	        }
	    }
	}

}