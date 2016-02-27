<?php namespace levidurfee\AttImport;

/**
 * Description of AttImport
 *
 * @author levi
 */
class AttImport implements AttImportInterface {
    protected $db;
    
    public function __construct() {
        $d = self::loadConfig('db');
        try {
            $this->db = new \PDO("mysql:host=" . $d['hostname'] . ";dbname=" 
                    . $d['database'] . ";charset=utf8", 
                    $d['username'], $d['password']);
            $this->db->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
            $this->db->setAttribute(\PDO::ATTR_EMULATE_PREPARES, false);
        } catch (Exception $ex) {
            echo $ex->getMessage() . "\n";
            die();
        }
    }
    public static function loadConfig($class) {
        return include(ROOT . DS . 'config' . DS . $class . 'Config.php');
    }
}
