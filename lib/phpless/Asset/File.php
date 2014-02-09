<?php

namespace phpless\Asset;

abstract class File
{
  // Force Extending class to define this method
  abstract protected function getFileName();
}