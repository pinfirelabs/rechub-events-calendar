<?php

namespace threelakessoftware\sharedEventsCalendar;

use Psr\SimpleCache\CacheInterface;
use Cache\Adapter\Void\VoidCachePool;

class CalendarMaker {
	private static $_cache;
	private static $_ttl = 600;

    public function __construct($api, $searchStr = '', CacheInterface $cache = null, int $cacheTtl = 600)
    {
        global $cmServer;
        global $cmApiServer;
        global $search;

        $cmServer = $cmApiServer = $api;

		$search = $searchStr;

		$this->_cache = $cache;
		if (!isset($this->_cache))
		{
			$this->_cache = new VoidCachePool;
		}

		$this->ttl = $cacheTtl;
    }

    public $scripts = [
        'bower-asset/moment/min/moment.min.js',
        'bower-asset/fullcalendar/dist/fullcalendar.js',
        'bower-asset/select2/dist/js/select2.min.js',
    ];

    public $styles = [
        'bower-asset/fullcalendar/dist/fullcalendar.css',
        'bower-asset/select2/dist/css/select2.min.css',
	];

    public function getConScripts()
    {
        ob_start();
        include(__DIR__ . '/../parts/con-scripts.php');
        return ob_get_clean();
    }

    public function getFilterRow() 
    {
        ob_start();
        include(__DIR__ . '/../parts/filter-row.php');
        return ob_get_clean();
    }

    public function getCalendarRow() 
    {
        ob_start();
        include(__DIR__ . '/../parts/cal.php');
        return ob_get_clean();
    }
	
	public static function getCache() : CacheInterface
	{
		return self::$_cache;
	}

	public static function getCacheTtl() : int
	{
		return self::$_ttl;
	}
}
