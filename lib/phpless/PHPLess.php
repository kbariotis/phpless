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

class PHPLess
{

  private
    /**
     * @var Less $lessFile
     */
    $lessFile,
    /**
     * @var string $lessDirectory Full path where
     * Less Files are located
     */
    $lessDirectory,
    /**
     * @var string $cacheDirectory Full path where
     * Cached files should stored
     */
    $cacheDirectory,
    /**
     * @var int $cacheExpiration Expiration time of
     * cached files
     */
    $cacheExpiration = 0; /* Three days */

  /**
   * @param array $options
   */
  public function __construct($options)
  {

    foreach($options as $key=>$option){
      $this->{"set".ucfirst($key)}($option);
    }

    $this->setLessFile()->serve();

  }

  public function setLessFile()
  {
    $this->lessFile = new Less($this->loadAssetFromRequest(), $this->cacheDirectory);
    return $this;
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
      throw new \InvalidArgumentException("Specify a valid
        directory where LESS files are located");

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
   * @param  string $asset Full path of the Less file to be served
   */
  public function serve()
  {

    $headers = apache_request_headers();

    $ifModifiedSince = isset($headers['If-Modified-Since']) ?
      $headers['If-Modified-Since'] : false;
    $lastModifiedTime = gmdate('D, d M Y H:i:s ',
      filemtime($this->lessFile->getOriginFile())) . 'GMT';

    /** Check if file is in Cache or has been modified */
    if(!$this->lessFile->validate() ||
      !($ifModifiedSince && strtotime($ifModifiedSince) ==
        filemtime($this->lessFile->getOriginFile())))
    {
      $this->lessFile->compile();
      header("Last-Modified: $lastModifiedTime");
    }
    else
    {
      /** Check if origin file didn't been modified */
      header('HTTP/1.0 304 Not Modified');
    }

    /** @see http://www.mnot.net/cache_docs/ */
    header('Content-Length: ' . filesize($this->lessFile->getCacheFile()));
    header('Content-Type: text/css');
    Header("Cache-Control: public, must-revalidate, max-age=0");
    header('Expires: -1');


    /* Output Compiled File */
    echo file_get_contents($this->lessFile->getCacheFile());

  }

}