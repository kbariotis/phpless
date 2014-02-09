<?php

namespace phpless;

class Less
{

  private
    $file,
    $cache;

  public function __construct($lessFile, $cacheDirectory)
  {
    $this->file = $lessFile;
    $this->cache = $cacheDirectory . basename($lessFile);
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

  public function getFile()
  {
    return $this->file;
  }

  public function getCacheFile()
  {
    return $this->cache;
  }

  public function validate()
  {
    if(!file_exists($this->cache))
    {
      $assetDirectory = dirname($this->getCacheFile());
      if (!is_dir($assetDirectory)) @mkdir($assetDirectory, 0777, true);
      if (!is_dir($assetDirectory)) throw new \RuntimeException();

      return false;
    }
    else
    {
      return true;
    }
  }

}