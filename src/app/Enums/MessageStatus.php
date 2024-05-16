<?php
  
namespace App\Enums;
 
enum MessageStatus :int {

    use EnumTrait;
    
    case Pending    = 1;
    case Schedule   = 2;
    case Fail       = 3;
    case Delivered  = 4;
    case Processing = 5;

}