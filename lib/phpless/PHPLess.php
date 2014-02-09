<?php

namespace phpless;

/**
 *
 */
class PHPLess
{

  private
    $asset,
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

    if(isset($duration))
      $this->cacheExpiration = $duration;

    return $this;
  }

  /**
   * @throwns InvalidArgumentException when no directory specified
   */
  public function setCacheDirectory($dir)
  {
    if(isset($dir))
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
    /* Object Generalization?!?! */
    switch(pathinfo($asset, PATHINFO_EXTENSION))
    {
      case "less" :
      {

        $assetType = "less";
        $assetDirectory = $this->cacheDirectory .
          $assetType . '/';
        $class = 'Asset\\'.ucfirst($assetType);

        $this->asset = new Asset\Less($asset);

        if(!$this->validateAssetInCache($assetDirectory .
          $this->asset->getFileName()))
        {
          $finalFileContents = $this->asset->compile();
          file_put_contents($assetDirectory . $this->asset->getFileName(), $finalFileContents);

          echo $finalFileContents;
        }
        else
        {
          echo file_get_contents($assetDirectory . $this->asset->getFileName());
        }


      }
    }
  }

  public function validateAssetInCache($asset)
  {
    if(!file_exists($asset))
    {
      $assetDirectory = dirname($asset);
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