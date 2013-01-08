<?php
/**
 * MuDoCo - A Multi Domain Cookie
 * 
 * Server side classes.
 * 
 * @author berliozdavid@gmail.com
 *
 */

/**
 * Interface for handling session storage.
 *
 */
interface MuDoCo_Storage_Session_Interface {
  
  /**
   * Read the session data.
   * 
   * @return mixed data
   */
  public function read();
  
  /**
   * Write the session data.
   * 
   * @param mixed $data
   * 
   * @return mixed data
   */
  public function write($data);
  
  /**
   * Set the session cookie.
   *
   * @param int $lifespan
   */
  public function cookie();
  
  /**
   * Get the session ID.
   *
   * @return string $mdcid
   */
  public function id();
}
