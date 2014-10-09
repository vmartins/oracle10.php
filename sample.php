<?php
/**
  * Warning:
  *
  * This hash is not secure, and should not be used 
  * for any purposes.
  * This implementation has not been compared very 
  * carefully against the official implementation or
  * reference documentation, and its behavior may not
  * match under various border cases.
  */

require 'oracle10.php';

$oracle10 = new Oracle10();
echo $oracle10->encrypt('user', 'password');