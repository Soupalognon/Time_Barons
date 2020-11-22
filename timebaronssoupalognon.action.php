<?php
/**
 *------
 * BGA framework: © Gregory Isabelli <gisabelli@boardgamearena.com> & Emmanuel Colin <ecolin@boardgamearena.com>
 * TimeBaronsSoupalognon implementation : © <Your name here> <Your email address here>
 *
 * This code has been produced on the BGA studio platform for use on https://boardgamearena.com.
 * See http://en.doc.boardgamearena.com/Studio for more information.
 * -----
 * 
 * timebaronssoupalognon.action.php
 *
 * TimeBaronsSoupalognon main action entry point
 *
 *
 * In this file, you are describing all the methods that can be called from your
 * user interface logic (javascript).
 *       
 * If you define a method "myAction" here, then you can call it from your javascript code with:
 * this.ajaxcall( "/timebaronssoupalognon/timebaronssoupalognon/myAction.html", ...)
 *
 */
  
  
  class action_timebaronssoupalognon extends APP_GameAction
  { 
    // Constructor: please do not modify
   	public function __default()
  	{
  	    if( self::isArg( 'notifwindow') )
  	    {
            $this->view = "common_notifwindow";
  	        $this->viewArgs['table'] = self::getArg( "table", AT_posint, true );
  	    }
  	    else
  	    {
            $this->view = "timebaronssoupalognon_timebaronssoupalognon";
            self::trace( "Complete reinitialization of board game" );
      }
  	} 
  	
    public function playCard() {
      self::setAjaxMode();
      $card_id = self::getArg("id", AT_posint, true);
      $this->game->playCard($card_id);
      self::ajaxResponse();
    }

    public function relocate() {
      self::setAjaxMode();
      $isRelocating = self::getArg("isRelocating", AT_posint, true);
      
      $relocateArray_raw = self::getArg("relocateArray", AT_numberlist, true);
      // self::dump( 'player level = ', ($relocateArray_raw));

      // Removing last ';' if exists
      if( substr( $relocateArray_raw, -1 ) == ';' )
        $relocateArray_raw = substr( $relocateArray_raw, 0, -1 );
      if( $relocateArray_raw == '' )
        $relocateArray_raw = array();
      else
        $relocateArray_raw = explode( ';', $relocateArray_raw );

      $relocateArray = array();
      foreach($relocateArray_raw as $value) {
        $relocateArray [] = explode(',', $value);
      }
      // self::dump( 'relocateArray = ', $relocateArray);

      $this->game->relocate($isRelocating, $relocateArray);
      self::ajaxResponse();
    }

    public function takeFollower() {
      self::setAjaxMode();
      $card_id = self::getArg("id", AT_posint, true);
      $this->game->takeFollower($card_id);
      self::ajaxResponse();
    }

    public function upgrade() {
      self::setAjaxMode();
      $this->game->upgrade();
      self::ajaxResponse();
    }

    public function endTurn() {
      self::setAjaxMode();
      $this->game->endTurn();
      self::ajaxResponse();
    }

    public function drawCard() {
      self::setAjaxMode();
      $this->game->drawCardStep1();
      self::ajaxResponse();
    }

    public function level1() {
      self::setAjaxMode();
      $this->game->drawCardStep2(1);
      self::ajaxResponse();
    }

    public function level2() {
      self::setAjaxMode();
      $this->game->drawCardStep2(2);
      self::ajaxResponse();
    }

    public function level3() {
      self::setAjaxMode();
      $this->game->drawCardStep2(3);
      self::ajaxResponse();
    }

    public function level4() {
      self::setAjaxMode();
      $this->game->drawCardStep2(4);
      self::ajaxResponse();
    }

    public function action1() {
      self::setAjaxMode();
      $card_id = self::getArg("id", AT_posint, true);
      $this->game->actionButton(1, $card_id);
      self::ajaxResponse();
    }

    public function action2() {
      self::setAjaxMode();
      $card_id = self::getArg("id", AT_posint, true);
      $this->game->actionButton(2, $card_id);
      self::ajaxResponse();
    }
  }
  

