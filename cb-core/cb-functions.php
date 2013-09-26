<?php

/**
 * CouchDB Logging
 *
 * @copyright Copyright 2013 Fredrik Forsmo (http://forsmo.me)
 * @license The MIT License
 */

// Exit if accessed directly.
if (!defined('ABSPATH')) exit;

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

function get_couchdb_logs ($limit = null, $endKey = null, $descending = false) {
  $sag = CouchDB_Sag::instance();
  return $sag->get_logs($limit, $endKey, $descending);
}

/**
 * Log to CouchDB.
 *
 * @param string $type
 * @param array|object|string $data
 *
 * @return mixed
 */

function couchdb_log ($type, $data) {
  $sag = CouchDB_Sag::instance();
  return $sag->insert(array(
    'type' => $type,
    'data' => $data
  ));
}