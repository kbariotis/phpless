<?php

namespace phpless;

/**
 *
 */
class PHPLess
{

  private
    $lessFile,
    $lessDirectory,
    $cacheDirectory,
    $cacheExpiration = 8000;

  /**
   * @param [type] $options [description]
   */
  public function __construct($options)
  {

    foreach($options as $key=>$option){
      $this->{"set".ucfirst($key)}($option);
    }

    $this->serve($this->loadAssetFromRequest());

  }

  public function setCacheExpiration($duration)
  {

    if(isset($duration) && $duration!=NULL)
      $this->cacheExpiration = $duration;

    return $this;
  }

  /**
   * @throwns InvalidArgumentException when no directory specified
   */
  public function setCacheDirectory($dir)
  {
    if(isset($dir) && $dir!=NULL)
      $this->cacheDirectory = $dir;
    else
      throw new \InvalidArgumentException("Specify a cache directory");

    return $this;
  }

  /**
   * @throwns InvalidArgumentException when no directory specified
   * and doesn't exists
   */
  public function setLessDirectory($dir)
  {
    if(isset($dir) && is_dir($dir))
      $this->lessDirectory = $dir;
    else
      throw new \InvalidArgumentException("Specify a valid directory where LESS files are located");

    return $this;
  }

  /**
   * Loads asset's full path from REQUEST
   *
   * @return string Asset's full path
   */
  public function loadAssetFromRequest()
  {
    $asset = $this->lessDirectory .
              basename($_SERVER['REQUEST_URI']);

    if(!file_exists($asset))
      header("HTTP/1.0 404 Not Found");

    return $asset;
  }

  /**
   * [serve description]
   * @param  [type] $asset [description]
   * @return [type]        [description]
   */
  public function serve($asset)
  {

    $this->lessFile = new Less($asset, $this->cacheDirectory);

    if(!$this->lessFile->validate())
    {
      $finalFileContents = $this->lessFile->compile();
      file_put_contents($this->lessFile->getCacheFile(), $finalFileContents);

      echo $finalFileContents;
    }
    else
    {
      echo file_get_contents($this->lessFile->getCacheFile());
    }

  }

}