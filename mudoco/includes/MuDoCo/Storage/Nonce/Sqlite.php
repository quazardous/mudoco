<?php

require_once "MuDoCo/Storage/Nonce/Interface.php";

class MuDoCo_Storage_Nonce_Sqlite implements MuDoCo_Storage_Nonce_Interface {
  
  /**
   * @var SQLite3
   */
  protected $_db = null;
  
  /**
   * @return SQLite3
   */
  protected function db() {
    global $mudoco_conf;
    if (!$this->_db) {
      $this->_db = new SQLite3($mudoco_conf['MUDOCO_STORAGE_NONCE_SQLITE_FILE']);
      if ($this->_db) {
        if ($this->_db->busyTimeout(2000)) {
          $this->_db->exec('CREATE TABLE IF NOT EXISTS nonce (cnonce VARCHAR(32) PRIMARY KEY, nonce VARCHAR(32), expire INTEGER);');
          $this->_db->exec('CREATE INDEX IF NOT EXISTS idx_expire ON nonce(expire);');
          $this->_db->exec('CREATE INDEX IF NOT EXISTS idx_nonce ON nonce(nonce);');
        }
        // !important : do not release busyTimeout()
      }
    }
    return $this->_db;
  }
  
  public function register($cnonce) {
    $nonce = md5($this->salt() . time() . rand() . uniqid());
    if ($this->db()->busyTimeout(2000)) {
      $nonce = $this->db()->escapeString($nonce);
      $cnonce = $this->db()->escapeString($cnonce);
      $now = time();
      $expire = $now + $this->lifetime();
      $query = "";
      if (rand(0, 99) == 0) {
        // basic garbage collector
        $this->db()->exec("DELETE FROM nonce WHERE expire < $now;");
  
      }
      $this->db()->exec("INSERT OR REPLACE INTO nonce (cnonce, nonce, expire) VALUES ('$cnonce', '$nonce', $expire);");
    }
    $this->db()->busyTimeout(0);
    return $nonce;
  }

  public function get($cnonce) {
    $nonce = false;
    if ($this->db()->busyTimeout(2000)) {
      $cnonce = $this->db()->escapeString($cnonce);
      $now = time();
      $nonce = $this->db()->querySingle("SELECT nonce FROM nonce WHERE cnonce = '$cnonce' AND expire >= $now;");
    }
    $this->db()->busyTimeout(0);
    return $nonce;
  }

  protected function lifetime() {
    global $mudoco_conf;
    if (isset($mudoco_conf['MUDOCO_STORAGE_NONCE_LIFETIME'])) {
      return $mudoco_conf['MUDOCO_STORAGE_NONCE_LIFETIME'];
    }
    return 600; // 10 minutes lifetime
  }
  
  protected function salt() {
    global $mudoco_conf;
    if (isset($mudoco_conf['MUDOCO_STORAGE_NONCE_SALT'])) {
      return $mudoco_conf['MUDOCO_STORAGE_NONCE_SALT'];
    }
    static $salt;
    if (empty($salt)) $salt = time();
    return $salt;
  }
}