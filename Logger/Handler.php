<?php

namespace GiftGroup\GeoPage\Logger;

use Monolog\Logger;
use Magento\Framework\Logger\Handler\Base;

class Handler extends Base
{
   /**
    * Logging level
    * @var int
    */
   protected $loggerType = Logger::ERROR;

   /**
    * File name
    * @var string
    */
   protected $fileName = '/var/log/geopage_error.log';
}