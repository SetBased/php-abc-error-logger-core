<?php
declare(strict_types=1);

namespace Plaisio\ErrorLogger;

use Plaisio\Debug\VarWriter;
use Plaisio\Helper\Html;

/**
 * Writes a var dump in HTML to a stream.
 */
class HtmlVarWriter implements VarWriter
{
  //--------------------------------------------------------------------------------------------------------------------
  /**
   * The output handle.
   *
   * @var resource
   */
  protected $handle;

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Object constructor.
   *
   * @param resource $handle The handle to write the var dump to.
   */
  public function __construct($handle)
  {
    $this->handle = $handle;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Does nothing.
   */
  public function start(): void
  {
    fwrite($this->handle, '<table class="var-dump">');
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Does nothing.
   */
  public function stop(): void
  {
    fwrite($this->handle, '</table>');
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * {@inheritdoc}
   */
  public function writeArrayClose(int $id, $name): void
  {
    if ($name!==null)
    {
      fwrite($this->handle, '</table>');
      fwrite($this->handle, '</td>');
      fwrite($this->handle, '</tr>');
    }
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * {@inheritdoc}
   */
  public function writeArrayOpen(int $id, $name): void
  {
    if ($name!==null)
    {
      fwrite($this->handle, '<tr>');
      $this->writeName($name, $id);
      fwrite($this->handle, '<td>');
      fwrite($this->handle, Html::generateElement('div', ['class' => 'array'], 'array').'<br/>');
      fwrite($this->handle, '<table>');
    }
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * {@inheritdoc}
   */
  public function writeArrayReference(int $ref, $name): void
  {
    $html = Html::generateElement('span', ['class' => 'array'], 'array');
    $html .= ', ';
    $html .= Html::generateElement('a', ['href' => '#'.$ref], 'see '.$ref);

    fwrite($this->handle, '<tr>');
    $this->writeName($name);
    fwrite($this->handle, Html::generateElement('td', [], $html, true));
    fwrite($this->handle, '</tr>');
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * {@inheritdoc}
   */
  public function writeBool(?int $id, ?int $ref, bool &$value, $name): void
  {
    $this->writeScalar($id, $ref, $name, ($value) ? 'true' : 'false', 'keyword');
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * {@inheritdoc}
   */
  public function writeFloat(?int $id, ?int $ref, float &$value, $name): void
  {
    $this->writeScalar($id, $ref, $name, (string)$value, 'number');
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * {@inheritdoc}
   */
  public function writeInt(?int $id, ?int $ref, int &$value, $name): void
  {
    $this->writeScalar($id, $ref, $name, (string)$value, 'number');
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * {@inheritdoc}
   */
  public function writeNull(?int $id, ?int $ref, $name): void
  {
    $this->writeScalar($id, $ref, $name, 'null', 'keyword');
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * {@inheritdoc}
   */
  public function writeObjectClose(int $id, $name, string $class): void
  {
    if ($name!==null)
    {
      fwrite($this->handle, '</table>');
      fwrite($this->handle, '</td>');
      fwrite($this->handle, '</tr>');
    }
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * {@inheritdoc}
   */
  public function writeObjectOpen(int $id, $name, string $class): void
  {
    if ($name!==null)
    {
      fwrite($this->handle, '<tr>');
      $this->writeName($name, $id);
      fwrite($this->handle, '<td>');
      fwrite($this->handle, Html::generateElement('div', ['class' => 'class'], $class).'<br/>');
      fwrite($this->handle, '<table>');
    }
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * {@inheritdoc}
   */
  public function writeObjectReference(int $ref, $name, string $class): void
  {
    $html = Html::generateElement('span', ['class' => 'class'], $class);
    $html .= ', ';
    $html .= Html::generateElement('a', ['href' => '#'.(string)$ref], 'see '.(string)$ref);

    fwrite($this->handle, '<tr>');
    $this->writeName($name);
    fwrite($this->handle, Html::generateElement('td', [], $html, true));
    fwrite($this->handle, '</tr>');
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * {@inheritdoc}
   */
  public function writeResource(?int $id, ?int $ref, $name, string $type): void
  {
    $this->writeScalar($id, $ref, $name, $type, 'keyword');
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * {@inheritdoc}
   */
  public function writeString(?int $id, ?int $ref, string &$value, $name): void
  {
    $text  = mb_strimwidth($value, 0, 80, '...');
    $title = ($text!=$value) ? mb_strimwidth($value, 0, 512, '...') : null;

    $this->writeScalar($id, $ref, $name, $text, 'string', $title);
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Writes the name of a variable.
   *
   * @param string|int|null $name The name of the variable.
   * @param int|null        $id   The ID of the value.
   */
  private function writeName($name, ?int $id = null): void
  {
    if ($name===null || $name==='')
    {
      fwrite($this->handle, Html::generateElement('th', ['class' => 'id', 'id' => $id], $id));
    }
    else
    {
      $title = null;

      if (is_int($name))
      {
        $text  = (string)$name;
        $class = 'number';
      }
      elseif (is_string($name))
      {
        $class = 'string';
        $text  = mb_strimwidth((string)$name, 0, 20, '...');
        if ($text!=$name)
        {
          $title = mb_strimwidth((string)$name, 0, 512, '...');
        }
      }
      else
      {
        throw new \InvalidArgumentException(sprintf('$name has unexpected type %s', gettype($name)));
      }

      fwrite($this->handle, Html::generateElement('th', ['class' => 'id'], $id));

      fwrite($this->handle, Html::generateElement('th',
                                                  ['class' => $class,
                                                   'id'    => $id,
                                                   'title' => $title],
                                                  $text));
    }
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Dumps a scalar value.
   *
   * @param int|null        $id    The ID of the value.
   * @param int|null        $ref   The ID of the value if the variable is a reference to a value that has been dumped
   *                               already.
   * @param string|int|null $name  The name of the variable.
   * @param string          $text  The text for displaying the value.
   * @param string          $class The class of the value.
   * @param string|null     $title The title for the value.
   */
  private function writeScalar(?int $id, ?int $ref, $name, string $text, string $class, ?string $title = null)
  {
    $html = Html::generateElement('span', ['class' => $class, 'title' => $title], $text);
    if ($ref!==null)
    {
      $html .= ', ';
      $html .= Html::generateElement('a', ['href' => '#'.$ref], 'see '.$ref);
    }

    fwrite($this->handle, '<tr>');
    $this->writeName($name, $id);
    fwrite($this->handle, Html::generateElement('td', [], $html, true));
    fwrite($this->handle, '</tr>');
  }

  //--------------------------------------------------------------------------------------------------------------------
}

//----------------------------------------------------------------------------------------------------------------------
