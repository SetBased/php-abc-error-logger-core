<?php
//----------------------------------------------------------------------------------------------------------------------
namespace SetBased\Abc\ErrorLogger;

/**
 * An error logger for development purposes. It will show all information of the error on the user's screen. Use this
 * error logger on development environments only.
 */
class DevelopmentErrorLogger extends CoreErrorLogger
{
  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Opens output.
   */
  protected function openStream()
  {
    $this->handle = fopen('php://output', 'wb');
  }

  //--------------------------------------------------------------------------------------------------------------------
}

//----------------------------------------------------------------------------------------------------------------------
