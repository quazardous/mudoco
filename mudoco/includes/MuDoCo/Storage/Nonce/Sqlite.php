<?php

require_once "MuDoCo/Storage/Nonce/Interface.php";

class MuDoCo_Storage_Nonce_Sqlite implements MuDoCo_Storage_Nonce_Interface {
  
  public function __construct() {
    global $mudoco_conf;
    $mudoco_conf += array (
      'MUDOCO_STORAGE_NONCE_SALT' => time(),
      'MUDOCO_STORAGE_NONCE_SQLITE_FILE' => null,
      'MUDOCO_STORAGE_NONCE_LIFETIME' => 600,
    );
  }
  
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
          $this->_db->exec('CREATE TABLE IF NOT EXISTS nonce (cnonce VARCHAR(32) PRIMARY KEY, nonce VARCHAR(32), expire INTEGER, fingerprint VARCHAR(32));');
          $this->_db->exec('CREATE INDEX IF NOT EXISTS idx_expire ON nonce(expire);');
          $this->_db->exec('CREATE INDEX IF NOT EXISTS idx_nonce ON nonce(nonce);');
        }
        // !important : do not release busyTimeout()
      }
    }
    return $this->_db;
  }
  
  public function register($cnonce, $fingerprint = null) {
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
      $this->db()->exec("INSERT OR REPLACE INTO nonce (cnonce, nonce, expire, fingerprint) VALUES ('$cnonce', '$nonce', $expire, '$fingerprint');");
    }
    $this->db()->busyTimeout(0);
    return $nonce;
  }

  public function get($cnonce, $fingerprint = null) {
    $nonce = false;
    if ($this->db()->busyTimeout(2000)) {
      $cnonce = $this->db()->escapeString($cnonce);
      $now = time();
      if ($fingerprint)
        $nonce = $this->db()->querySingle("SELECT nonce FROM nonce WHERE cnonce = '$cnonce' AND fingerprint = '$fingerprint' AND expire >= $now;");
      else
        $nonce = $this->db()->querySingle("SELECT nonce FROM nonce WHERE cnonce = '$cnonce' AND expire >= $now;");
    }
    $this->db()->busyTimeout(0);
    return $nonce;
  }

  protected function lifetime() {
    global $mudoco_conf;
    return $mudoco_conf['MUDOCO_STORAGE_NONCE_LIFETIME'];
  }
  
  protected function salt() {
    global $mudoco_conf;
    return $mudoco_conf['MUDOCO_STORAGE_NONCE_SALT'];
  }
}