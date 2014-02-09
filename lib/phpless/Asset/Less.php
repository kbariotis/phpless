<?php

namespace phpless\Asset;

class Less extends File
{

  private $file;

  public function __construct($lessFile)
  {
    $this->file = $lessFile;
  }

  public function compile()
  {
    $compiler = new \lessc();
    try {

      return $compiler->compileFile($this->file);

    } catch (exception $e) {
      throw new RuntimeException();
    }
  }

  public function getFileName()
  {
    return basename($this->file);
  }

}