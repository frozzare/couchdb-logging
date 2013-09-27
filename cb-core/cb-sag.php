<?php

/**
 * CouchDB Logging
 *
 * @copyright Copyright 2013 Fredrik Forsmo (http://forsmo.me)
 * @license The MIT License
 */

// Exit if accessed directly.
if (!defined('ABSPATH')) exit;

if (!class_exists('CouchDB_Sag')):

class CouchDB_Sag {

  /**
   * The instance of this class.
   *
   * @var object
   */

  private static $instance;

  /**
   * The instance of Sag.
   *
   * @var object
   */

  private static $sag;

  /**
   * Get the instance of this class.
   *
   * @since 1.0
   *
   * @return object
   */

  public function instance () {
    if (!isset(self::$instance)) {
      self::$instance = new CouchDB_Sag;
      self::$instance->setup_globals();
      self::$instance->includes();
      self::$instance->setup_sag();
    }
    return self::$instance;
  }

  /**
   * Setup globals.
   *
   * @since 1.0
   * @access private
   */

  private function setup_globals () {
    $this->vendor_dir       = CB_PLUGIN_DIR . '/vendor/';
  }

  /**
   * Include files.
   *
   * @since 1.0
   * @access private
   */

  private function includes () {
    require ($this->vendor_dir . 'sag/Sag.php');
  }

  /**
   * Construct. Nothing to see.
   *
   * @since 1.0
   * @access private
   */

  private function __construct () {}

  /**
   * Get the Sag instance or create it.
   *
   * @since 1.0
   *
   * @return object
   */

  private function setup_sag () {
    $host       = defined('CB_HOST') ? CB_HOST : '127.0.0.1';
    $port       = defined('CB_PORT') ? CB_PORT : '5984';
    $database   = defined('CB_NAME') ? CB_NAME : 'wordpress-logging';
    $user       = defined('CB_USER') ? CB_USER : null;
    $password   = defined('CB_PASSWORD') ? CB_PASSWORD : null;
    $type       = defined('CB_AUTH_TYPE') ? CB_AUTH_TYPE : null;

    if (!isset($this->sag)) {
      self::$sag = new Sag($host, $port);
      self::$sag->setDatabase($database, true);

      if (!is_null($user) && !is_null($password)) {
        if (!is_null($type)) $type = Sag::$AUTH_COOKIE;
        self::$sag->login($user, $password, $type);

      }

      $this->setup_view();
    }
    return self::$sag;
  }

  /**
   * Setup CouchDB design view for sorting via created time.
   *
   * @since 1.0
   * @access private
   *
   * @return bool
   */

  private function setup_view () {
    $doc = new stdClass();
    $doc->_id = '_design/log';
    $doc->views = new stdClass();
    $doc->views->byCreateTime = new stdClass();
    $doc->views->byCreateTime->map = 'function (doc) { emit([doc.createdAt], doc); }';

    try {
      self::$sag->put($doc->_id, $doc);
    } catch (Exception $e) {

      /*
       * A 409 status code means there was a conflict, so another client
       * already created the design doc for us. This is fine.
       */

      if ($e->getCode() != 409) {
        return false;
      }
    }

    return true;
  }

  /**
   * Insert new data into the the database.
   *
   * @param array|object|string $data
   * @param array $params
   *
   * @since 1.0
   *
   * @return mixed
   */

  public function insert ($type = '', $data = array(), $severity = null) {
    if (is_array($type)) {
      $data = $type;
      $type = 'info';
    }

    $doc = array(
      'key' => time(),
      'createdAt' => time(),
      'type' => $type,
      'user' => get_current_user()
    );

    if (isset($id)) {
      $doc['_id'] = $id;
    }

    if (!is_null($severity)) {
      $doc['severity'] = $severity;
    }

    if (is_array($data) || is_object($array)) {
      $doc['data'] = (array)$data['data'];
    } else if (is_string($data)) {
      $doc['data'] = $data;
    }

    try {
      $doc = self::$sag->post($doc);
    } catch (SagCouchException $e) {
      return false;
    }

    return $doc;
  }

  /**
   * Get logs from CouchDB.
   *
   * @param int $limit Limits the number of documents to return. Must be >= 0,
   * or null for no limit. Defaults to null (no limit).
   * @param string $endKey The endkey variable (valid JSON). Defaults to null.
   * @param bool $descending Whether to sort the results in descending order or not.
   *
   * @return mixed
   */

  public function get_logs ($limit = null, $endKey = null, $descending = false) {
    try {
      $url = '/_design/log/_view/byCreateTime?include_docs=true';

      if (!is_null($limit) && is_numeric($limit)) {
        $url .= '&limit=' . $limit;
      }

      if (!is_null($endKey) && is_string($endKey)) {
        $url .= '&endkey=' . $endKey;
      }

      $url .= '&descending=' . ($descending !== false ? 'true' : 'false');

      $respond = self::$sag->get($url);
      if ($respond->body->total_rows > 0) {
        $rows = $respond->body->rows;
        return array_map(function ($doc) {
          return $doc->doc;
        }, $respond->body->rows);
      } else {
        return null;
      }
    } catch (Exception $e) {
      return null;
    }
  }
}

endif;