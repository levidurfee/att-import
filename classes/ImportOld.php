<?php namespace levidurfee\AttImport;

/**
 * @author levi
 */
class ImportOld extends AttImport implements ImportOldInterface {
    public $filedata;
    
    public function __construct() {
        parent::__construct();
    }
    
    public function loadFile($filename) {
        $filedata = file_get_contents($filename);
        $fileArray = explode("\n", $filedata);
        $this->getStartingLine($fileArray);
        $this->trimEmpty();
    }
    
    public function insertTextRecords() {
        for($i=0;$i<count($this->filedata);$i++) {
            if(strlen($this->filedata[$i] == 0)) {
                continue;
            }
            
            $values = explode(",", $this->filedata[$i]);
            
            $occured = $this->getDateTime($values[2], $values[3]);
            $query = 'INSERT INTO texts '
                    . '(count, occured, contact, usageType, inoutmsg) '
                    . 'VALUES '
                    . '(:count, :occured, :contact, :usageType, :inoutmsg)';
            try {
                $stmt = $this->db->prepare($query);
                $stmt->bindParam(':count', $values[0], \PDO::PARAM_INT);
                $stmt->bindParam(':occured', $occured, \PDO::PARAM_STR);
                $stmt->bindParam(':contact', $values[4], \PDO::PARAM_STR);
                $stmt->bindParam(':usageType', $values[5], \PDO::PARAM_STR);
                $stmt->bindParam(':inoutmsg', $values[10], \PDO::PARAM_STR);
                $stmt->execute();
                echo "Record inserted\r\n";
            } catch (\PDOException $ex) {
                echo $ex->getMessage() . "\r\n";
            }
            
        }
    }
    
    protected function getStartingLine($fileArray) {
        $x = 0;
        for($i=0;$i<count($fileArray);$i++) {
            #echo $fileArray[$i] . "\r\n";
            if(!strcmp($fileArray[$i], "Item,Day,Date,Time,To/From,Type,Msg/KB,Rate Code,Rate Pd,Feature,In/Out,Total Charge ")) {
                $x = $i + 1;
                break;
            }
        }
        $this->filedata = array_slice($fileArray, $x);
    }
    
    protected function trimEmpty() {
        $fileArray = $this->filedata;
        $cleanArray = [];
        for($i=0;$i<count($fileArray);$i++) {
            if(trim($fileArray[$i]) != "") {
                $cleanArray[] = $fileArray[$i];
            }
        }
        $this->filedata = $cleanArray;
    }
    
    public function getDateTime($date, $time) {
        # CSV doesn't have year
        $datestr = $date  . ' ' . $time;
        $datetime = date("Y-m-d H:i:s", strtotime($datestr));
        return $datetime;
    }
}
