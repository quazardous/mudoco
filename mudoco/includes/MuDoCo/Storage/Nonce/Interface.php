<?php

/**
 * Interface for accessing nonce.
 *
 */
interface MuDoCo_Storage_Nonce_Interface {
  
  /**
   * Register (or register again) cnonce and generate an associated nonce.
   * The registration should have a short lifetime.
   * The shorter is the better but it will trigger nonce generation more often.
   * A client nonce should be a unique key.
   *
   * @param string $cnonce client nonce
   * 
   * @return string server nonce
   */
  public function register($cnonce);

  /**
   * Check if cnonce if registered and return the corresponding nonce.
   * The function should return false if the cnonce is not registered or if it is too old.
   *
   * @param string $nonce
   *
   * @return string|false
   */
  public function get($cnonce);

  /**
   * Delete the nonce
   *
   * @param string $nonce
   */
  //public function delete($nonce);
}