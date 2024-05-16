<?php
  
namespace App\Enums;
 
enum TicketStatus :int {

    use EnumTrait;
    
    case Running  = 1;
    case Answered = 2;
    case Replied  = 3;
    case CLOSED   = 4;

}