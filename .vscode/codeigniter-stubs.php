<?php
/**
 * CodeIgniter 3.x Stubs for Intelephense
 * This file provides type hints for CodeIgniter classes and methods
 * to reduce false positives in IDE analysis.
 */

// Base Controller Class
class CI_Controller {
    public $load;
    public $input;
    public $output;
    public $uri;
    public $router;
    public $config;
    public $lang;
    public $session;
    public $db;
    public $dbutil;
    public $dbforge;
    public $zip;
    public $ftp;
    public $form_validation;
    public $upload;
    public $image_lib;
    public $pagination;
    public $parser;
    public $profiler;
    public $table;
    public $trackback;
    public $typography;
    public $unit_test;
    public $user_agent;
    public $xmlrpc;
    public $xmlrpcs;
    public $encrypt;
    public $calendar;
    public $email;
    public $javascript;
    public $security;
    public $cache;
    
    public function __construct() {}
}

// Base Model Class
class CI_Model {
    public $load;
    public $input;
    public $output;
    public $uri;
    public $router;
    public $config;
    public $lang;
    public $session;
    public $db;
    public $dbutil;
    public $dbforge;
    public $table;
    public $zip;
    public $ftp;
    public $form_validation;
    public $upload;
    public $image_lib;
    public $pagination;
    public $parser;
    public $profiler;
    public $trackback;
    public $typography;
    public $unit_test;
    public $user_agent;
    public $xmlrpc;
    public $xmlrpcs;
    public $encrypt;
    public $calendar;
    public $email;
    public $javascript;
    public $security;
    public $cache;
    
    public function __construct() {}
}

// Loader Class
class CI_Loader {
    /**
     * @param string $view
     * @param array $vars
     * @param bool $return
     * @return string|void
     */
    public function view($view, $vars = array(), $return = FALSE) {}
    
    /**
     * @param string $model
     * @param string $name
     * @param bool $db_conn
     * @return CI_Loader
     */
    public function model($model, $name = '', $db_conn = FALSE) {}
    
    /**
     * @param string $library
     * @param array $params
     * @param string $object_name
     * @return CI_Loader
     */
    public function library($library, $params = NULL, $object_name = NULL) {}
    
    /**
     * @param string|array $helpers
     * @return CI_Loader
     */
    public function helper($helpers) {}
    
    /**
     * @param string $file
     * @param array $vars
     * @param bool $return
     * @return mixed
     */
    public function file($file, $vars = array(), $return = FALSE) {}
    
    /**
     * @param string $path
     * @return CI_Loader
     */
    public function add_package_path($path) {}
    
    /**
     * @param string $path
     * @return CI_Loader
     */
    public function remove_package_path($path = '') {}
    
    /**
     * @param string $group
     * @param string $language
     * @return CI_Loader
     */
    public function language($group, $language = '') {}
    
    /**
     * @param array $vars
     * @param string $val
     * @return CI_Loader
     */
    public function vars($vars, $val = '') {}
    
    /**
     * @param string $params
     * @param object $object
     * @return mixed
     */
    public function database($params = '', $return = FALSE, $query_builder = NULL) {}
    
    /**
     * @param string $driver
     * @param array $config
     * @param bool $return
     * @return mixed
     */
    public function driver($driver, $config = array(), $return = FALSE) {}
    
    /**
     * @return array
     */
    public function get_vars() {}
    
    /**
     * @return CI_Loader
     */
    public function clear_vars() {}
}

// Input Class
class CI_Input {
    /**
     * @param string $index
     * @param bool $xss_clean
     * @return mixed
     */
    public function post($index = NULL, $xss_clean = NULL) {}
    
    /**
     * @param string $index
     * @param bool $xss_clean
     * @return mixed
     */
    public function get($index = NULL, $xss_clean = NULL) {}
    
    /**
     * @param string $index
     * @param bool $xss_clean
     * @return mixed
     */
    public function cookie($index = NULL, $xss_clean = NULL) {}
    
    /**
     * @param string $index
     * @return mixed
     */
    public function server($index = NULL) {}
    
    /**
     * @param string $index
     * @param bool $xss_clean
     * @return mixed
     */
    public function input_stream($index = NULL, $xss_clean = NULL) {}
    
    /**
     * @param string $name
     * @param string $value
     * @param int $expire
     * @param string $domain
     * @param string $path
     * @param string $prefix
     * @param bool $secure
     * @param bool $httponly
     * @return CI_Input
     */
    public function set_cookie($name, $value = '', $expire = 0, $domain = '', $path = '/', $prefix = '', $secure = NULL, $httponly = NULL) {}
    
    /**
     * @return string
     */
    public function ip_address() {}
    
    /**
     * @param string $ip
     * @return bool
     */
    public function valid_ip($ip) {}
    
    /**
     * @return string
     */
    public function user_agent() {}
    
    /**
     * @return string
     */
    public function method() {}
    
    /**
     * @return bool
     */
    public function is_ajax_request() {}
    
    /**
     * @return bool
     */
    public function is_cli_request() {}
}

// Database Class
class CI_DB_query_builder {
    /**
     * @param string $select
     * @param bool $escape
     * @return CI_DB_query_builder
     */
    public function select($select = '*', $escape = NULL) {}
    
    /**
     * @param string $from
     * @return CI_DB_query_builder
     */
    public function from($from) {}
    
    /**
     * @param string $table
     * @param string $cond
     * @param string $type
     * @param bool $escape
     * @return CI_DB_query_builder
     */
    public function join($table, $cond, $type = '', $escape = NULL) {}
    
    /**
     * @param mixed $key
     * @param mixed $value
     * @param bool $escape
     * @return CI_DB_query_builder
     */
    public function where($key, $value = NULL, $escape = NULL) {}
    
    /**
     * @param mixed $key
     * @param mixed $value
     * @param bool $escape
     * @return CI_DB_query_builder
     */
    public function or_where($key, $value = NULL, $escape = NULL) {}
    
    /**
     * @param string $field
     * @param string $match
     * @param string $side
     * @param bool $escape
     * @return CI_DB_query_builder
     */
    public function like($field, $match = '', $side = 'both', $escape = NULL) {}
    
    /**
     * @param string $field
     * @param string $match
     * @param string $side
     * @param bool $escape
     * @return CI_DB_query_builder
     */
    public function or_like($field, $match = '', $side = 'both', $escape = NULL) {}
    
    /**
     * @param string $by
     * @param string $direction
     * @param bool $escape
     * @return CI_DB_query_builder
     */
    public function order_by($orderby, $direction = '', $escape = NULL) {}
    
    /**
     * @param mixed $by
     * @param bool $escape
     * @return CI_DB_query_builder
     */
    public function group_by($by, $escape = NULL) {}
    
    /**
     * @param int $value
     * @param int $offset
     * @return CI_DB_query_builder
     */
    public function limit($value, $offset = 0) {}
    
    /**
     * @param string $table
     * @param array $set
     * @return bool
     */
    public function insert($table = '', $set = NULL) {}
    
    /**
     * @param string $table
     * @param array $set
     * @param mixed $where
     * @param int $limit
     * @return bool
     */
    public function update($table = '', $set = NULL, $where = NULL, $limit = NULL) {}
    
    /**
     * @param string $table
     * @param mixed $where
     * @param int $limit
     * @param bool $reset_data
     * @return mixed
     */
    public function delete($table = '', $where = '', $limit = NULL, $reset_data = TRUE) {}
    
    /**
     * @return CI_DB_result
     */
    public function get($table = '', $limit = NULL, $offset = NULL) {}
    
    /**
     * @param string $table
     * @return int
     */
    public function count_all($table = '') {}
    
    /**
     * @return int
     */
    public function insert_id() {}
    
    /**
     * @return CI_DB_query_builder
     */
    public function group_start() {}
    
    /**
     * @return CI_DB_query_builder
     */
    public function group_end() {}
}

// Database Result Class
class CI_DB_result {
    /**
     * @return array
     */
    public function result_array() {}
    
    /**
     * @return array
     */
    public function row_array() {}
    
    /**
     * @return object
     */
    public function result() {}
    
    /**
     * @return object
     */
    public function row() {}
    
    /**
     * @return int
     */
    public function num_rows() {}
}

// Session Class
class CI_Session {
    /**
     * @param mixed $userdata
     * @param string $value
     * @return void
     */
    public function set_userdata($userdata, $value = '') {}
    
    /**
     * @param string $key
     * @return mixed
     */
    public function userdata($key = NULL) {}
    
    /**
     * @param mixed $keys
     * @return void
     */
    public function unset_userdata($keys) {}
    
    /**
     * @return void
     */
    public function sess_destroy() {}
    
    /**
     * @return void
     */
    public function sess_regenerate($destroy = FALSE) {}
    
    /**
     * @param mixed $flashdata
     * @param string $value
     * @return void
     */
    public function set_flashdata($flashdata, $value = '') {}
    
    /**
     * @param string $key
     * @return mixed
     */
    public function flashdata($key = NULL) {}
    
    /**
     * @param mixed $keys
     * @return void
     */
    public function keep_flashdata($keys) {}
}

// URI Class
class CI_URI {
    /**
     * @param int $n
     * @param mixed $no_result
     * @return string
     */
    public function segment($n, $no_result = NULL) {}
    
    /**
     * @param int $n
     * @param mixed $no_result
     * @return string
     */
    public function rsegment($n, $no_result = NULL) {}
    
    /**
     * @return string
     */
    public function uri_string() {}
    
    /**
     * @return array
     */
    public function segment_array() {}
    
    /**
     * @return array
     */
    public function rsegment_array() {}
    
    /**
     * @return int
     */
    public function total_segments() {}
    
    /**
     * @return int
     */
    public function total_rsegments() {}
}

// Pagination Class
class CI_Pagination {
    /**
     * @param array $params
     * @return void
     */
    public function initialize($params = array()) {}
    
    /**
     * @return string
     */
    public function create_links() {}
}

// Global Functions
if (!function_exists('show_404')) {
    /**
     * @param string $page
     * @param bool $log_error
     * @return void
     */
    function show_404($page = '', $log_error = TRUE) {}
}

if (!function_exists('show_error')) {
    /**
     * @param string $message
     * @param int $status_code
     * @param string $heading
     * @return void
     */
    function show_error($message, $status_code = 500, $heading = 'An Error Was Encountered') {}
}

if (!function_exists('log_message')) {
    /**
     * @param string $level
     * @param string $message
     * @return void
     */
    function log_message($level, $message) {}
}

if (!function_exists('base_url')) {
    /**
     * @param string $uri
     * @return string
     */
    function base_url($uri = '') {}
}

if (!function_exists('site_url')) {
    /**
     * @param string $uri
     * @return string
     */
    function site_url($uri = '') {}
}

if (!function_exists('current_url')) {
    /**
     * @return string
     */
    function current_url() {}
}

if (!function_exists('redirect')) {
    /**
     * @param string $uri
     * @param string $method
     * @param int $code
     * @return void
     */
    function redirect($uri = '', $method = 'auto', $code = NULL) {}
}

if (!function_exists('anchor')) {
    /**
     * @param string $uri
     * @param string $title
     * @param mixed $attributes
     * @return string
     */
    function anchor($uri = '', $title = '', $attributes = '') {}
}

if (!function_exists('form_open')) {
    /**
     * @param string $action
     * @param mixed $attributes
     * @param array $hidden
     * @return string
     */
    function form_open($action = '', $attributes = array(), $hidden = array()) {}
}

if (!function_exists('form_close')) {
    /**
     * @return string
     */
    function form_close() {}
}

if (!function_exists('form_input')) {
    /**
     * @param mixed $data
     * @param string $value
     * @param mixed $extra
     * @return string
     */
    function form_input($data = '', $value = '', $extra = '') {}
}

if (!function_exists('form_textarea')) {
    /**
     * @param mixed $data
     * @param string $value
     * @param mixed $extra
     * @return string
     */
    function form_textarea($data = '', $value = '', $extra = '') {}
}

if (!function_exists('form_submit')) {
    /**
     * @param mixed $data
     * @param string $value
     * @param mixed $extra
     * @return string
     */
    function form_submit($data = '', $value = '', $extra = '') {}
}

if (!function_exists('validation_errors')) {
    /**
     * @param string $prefix
     * @param string $suffix
     * @return string
     */
    function validation_errors($prefix = '', $suffix = '') {}
}

if (!function_exists('set_value')) {
    /**
     * @param string $field
     * @param string $default
     * @return string
     */
    function set_value($field, $default = '') {}
}

if (!function_exists('element')) {
    /**
     * @param string $item
     * @param array $array
     * @param mixed $default
     * @return mixed
     */
    function element($item, $array, $default = NULL) {}
}

if (!function_exists('elements')) {
    /**
     * @param array $items
     * @param array $array
     * @param mixed $default
     * @return array
     */
    function elements($items, $array, $default = NULL) {}
}

// Constants
if (!defined('APPPATH')) {
    define('APPPATH', '');
}

if (!defined('BASEPATH')) {
    define('BASEPATH', '');
}

if (!defined('FCPATH')) {
    define('FCPATH', '');
}

if (!defined('SYSDIR')) {
    define('SYSDIR', '');
}

if (!defined('ENVIRONMENT')) {
    define('ENVIRONMENT', 'development');
}

if (!defined('UPLOAD_ERR_OK')) {
    define('UPLOAD_ERR_OK', 0);
}
