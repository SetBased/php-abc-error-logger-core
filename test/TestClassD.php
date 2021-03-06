<?php
declare(strict_types=1);

namespace Plaisio\ErrorLogger\Test;

/**
 * A class with uninitialized non-nullable type property.
 */
class TestClassD
{
  //--------------------------------------------------------------------------------------------------------------------
  /**
   * @var string
   */
  public string $qwerty;

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Does not count.
   */
  public function __construct()
  {
    // Nothing to do.
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Throws an exception.
   *
   * @throws \Exception
   */
  public function exception()
  {
    throw new \Exception();
  }

  //--------------------------------------------------------------------------------------------------------------------
}

//----------------------------------------------------------------------------------------------------------------------
