<?php
//----------------------------------------------------------------------------------------------------------------------
namespace SetBased\Abc\ErrorLogger\Test;

/**
 * Just a class.
 */
class TestClassA
{
  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Just a method.
   *
   * @param mixed $arg Just a argument.
   */
  public function methodA($arg=null)
  {
    $b = new TestClassB();
    $b->methodB($arg);
  }

  //--------------------------------------------------------------------------------------------------------------------
}

//----------------------------------------------------------------------------------------------------------------------