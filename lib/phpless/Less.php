<?php

/**
 * PHPLess
 *
 * Less Asset Management
 *
 * @author Kostas Bariotis <konmpar@gmail.com>
 * @licence MIT Licence
 */

namespace phpless;

/**
 * Class represents a Less physical file
 */
class Less
{

  private
    /**
     * @var string $file Full path of the original file
     */
    $file,
    /**
     * @var string $cache Full path of the cached file
     */
    $cache;

  /**
   * @param string $lessFile       Full path of the original file
   * @param string $cacheDirectory Directory of Cached file
   */
  public function __construct($lessFile, $cacheDirectory)
  {
    $this->file = $lessFile;
    $this->cache = $cacheDirectory . basename($lessFile);
  }

  /**
   * File compilation to CSS
   * @return string Compiled CSS
   */
  public function compile()
  {
    $compiler = new \lessc();
    try {

      file_put_contents($this->getCacheFile(),
        $compiler->compileFile($this->file));

    } catch (exception $e) {
      throw new RuntimeException();
    }
  }

  public function getFileName()
  {
    return basename($this->file);
  }

  public function getOriginFile()
  {
    return $this->file;
  }

  public function getCacheFile()
  {
    return $this->cache;
  }

  /**
   * Validate the cache
   * @return boolean Whether a cached version exists
   */
  public function validate()
  {
    $compiler = new \lessc();

    if(!file_exists($this->cache))
    {
      /* Create directories for use later */
      $assetDirectory = dirname($this->getCacheFile());
      if (!is_dir($assetDirectory)) @mkdir($assetDirectory, 0777, true);
      if (!is_dir($assetDirectory)) throw new \RuntimeException();

      return false;
    }
    else
    {
      /* Recompile if it's been modified */
      if(!$compiler->checkedCompile($this->file, $this->cache))
        $this->compile();
      return true;
    }
  }

  /**
   * Dump contents of the file
   */
  public function dump()
  {
    echo file_get_contents($this->getCacheFile());
  }

}