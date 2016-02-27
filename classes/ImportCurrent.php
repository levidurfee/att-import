<?php namespace levidurfee\AttImport;

/**
 * @author levi
 */
class ImportCurrent extends AttImport implements ImportInterface {
    public $filedata;
    
    public function __construct() {
        parent::__construct();
    }
    
    public function insertTextRecords() {
        for($i=0;$i<count($this->filedata);$i++) {
            if(strlen($this->filedata[$i] == 0)) {
                continue;
            }
            
            $values = explode("|", $this->filedata[$i]);
            
            $occured = $this->getDateTime($values[1], $values[2]);
            $query = 'INSERT INTO texts '
                    . '(count, occured, contact, usageType, providerName, phoneUsage, charges) '
                    . 'VALUES '
                    . '(:count, :occured, :contact, :usageType, :providerName, :phoneUsage, :charges)';
            try {
                $stmt = $this->db->prepare($query);
                $stmt->bindParam(':count', $values[0], \PDO::PARAM_INT);
                $stmt->bindParam(':occured', $occured, \PDO::PARAM_STR);
                $stmt->bindParam(':contact', $values[3], \PDO::PARAM_STR);
                $stmt->bindParam(':usageType', $values[4], \PDO::PARAM_STR);
                $stmt->bindParam(':providerName', $values[5], \PDO::PARAM_STR);
                $stmt->bindParam(':phoneUsage', $values[6], \PDO::PARAM_STR);
                $stmt->bindParam(':charges', $values[7], \PDO::PARAM_STR);
                $stmt->execute();
                echo "Record inserted\r\n";
            } catch (\PDOException $ex) {
                echo $ex->getMessage() . "\r\n";
            }
            
        }
    }
    
    public function loadFile($filename) {
        $filedata = file_get_contents($filename);
        $fileArray = explode("\n", $filedata);
        $this->filedata = array_slice($fileArray, 5);
    }
    
    public function getDateTime($date, $time) {
        # CSV doesn't have year
        $datestr = $date . '/' . date("Y") . ' ' . $time;
        $datetime = date("Y-m-d H:i:s", strtotime($datestr));
        return $datetime;
    }
}