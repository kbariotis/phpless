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
    $cacheExpiration = 259200; /* Three days */

  /**
   * @param array $options
   */
  public function __construct($options)
  {
    session_cache_limiter('public');
    session_cache_expire(30);

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
  public function serve($asset)
  {

    $this->lessFile = new Less($asset, $this->cacheDirectory);

    $if_modified_since = isset($_SERVER['HTTP_IF_MODIFIED_SINCE']) ?
      $_SERVER['HTTP_IF_MODIFIED_SINCE'] : false;
    $if_none_match = isset($_SERVER['HTTP_IF_NONE_MATCH']) ?
      $_SERVER['HTTP_IF_NONE_MATCH'] : false;

    /** Check if file is in Cache */
    if(!$this->lessFile->validate())
    {
      $this->lessFile->compile();
      $tsstring = gmdate('D, d M Y H:i:s ', filemtime($this->lessFile->getCacheFile())) . 'GMT';
      header("Last-Modified: $tsstring");
    }
    else
    {
      $tsstring = gmdate('D, d M Y H:i:s ', filemtime($this->lessFile->getCacheFile())) . 'GMT';
      if ($if_modified_since && date($if_modified_since) <= date($tsstring))
      {
        header('HTTP/1.1 304 Not Modified');
      }
      else
      {
        header("Last-Modified: $tsstring");
      }
    }

    /** @see http://www.mnot.net/cache_docs/ */
    Header("Cache-Control: must-revalidate");
    header('Content-Length: ' . filesize($this->lessFile->getCacheFile()));
    header('Content-Type: text/css');
    header('Expires: ' . gmdate('D, d M Y H:i:s',
        filemtime($this->lessFile->getCacheFile()) + $this->cacheExpiration));

    /* Output Compiled File */
    echo file_get_contents($this->lessFile->getCacheFile());

  }

}