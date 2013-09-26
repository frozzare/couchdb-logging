<?php

/**
 * CouchDB Logging
 *
 * @copyright Copyright 2013 Fredrik Forsmo (http://forsmo.me)
 * @license The MIT License
 */

/**
 * Plugin Name: CouchDB Logging
 * Description: Log to CouchDB
 * Author: Fredrik Forsmo
 * Author URI: http://forsmo.me/
 * Version: 1.0
 * Plugin URI: https://github.com/frozzare/couchdb-logging
 */

// Exit if accessed directly.
if (!defined('ABSPATH')) exit;

// Check so class don't exists before we creat it.
if (!class_exists('CouchDB_Logging')):

class CouchDB_Logging {

  /**
   * The instance of CouchDB Logging
   *
   * @since 1.0
   *
   * @var object
   */

  private static $instance;

  /**
   * Main CouchDB Logging instance.
   *
   * @since 1.0
   *
   * @return The instance of CouchDB Logging
   */

  public static function instance () {
    if (!isset(self::$instance)) {
      self::$instance = new CouchDB_Logging;
      self::$instance->constants();
      self::$instance->setup_globals();
      self::$instance->includes();
    }
    return self::$instance;
  }

  /**
   * Construct. Nothing to see.
   *
   * @since 1.0
   * @access private
   */

  private function __construct () {}

  /**
   * Bootstrap constants
   *
   * @since 1.0
   * @access private
   */

  private function constants () {
    // Path to CouchDB Logging plugin directory
    if (!defined('CB_PLUGIN_DIR')) {
      define('CB_PLUGIN_DIR', trailingslashit(WP_PLUGIN_DIR . '/couchdb-logging'));
    }

    // URL to CouchDB Logging plugin directory
    if (!defined('CB_PLUGIN_URL')) {
      $plugin_url = plugin_dir_url(__FILE__);

      if (is_ssl()) {
        $plugin_url = str_replace('http://', 'https://', $plugin_url);
      }

      define('CB_PLUGIN_URL', $plugin_url);
    }
  }

  /**
   * Include files.
   *
   * @since 1.0
   * @access private
   */

  private function includes () {
    require ($this->plugin_dir . 'cb-core/cb-sag.php');
    require ($this->plugin_dir . 'cb-core/cb-functions.php');
  }

  /**
   * Setup globals.
   *
   * @since 1.0
   * @access private
   */

  private function setup_globals () {
    // Paths
    $this->file             = __FILE__;
    $this->basename         = plugin_basename($this->file);
    $this->plugin_dir       = CB_PLUGIN_DIR;
    $this->vendors_dir      = CB_PLUGIN_URL;
  }
}

/**
 * Returning the CouchDB Logging instance to everyone.
 *
 * @return CouchDB Logging instance
 */

function couchdb_logging () {
  return CouchDB_Logging::instance();
}

// Let's make it global too!
$GLOBALS['cb'] = &couchdb_logging();


endif;