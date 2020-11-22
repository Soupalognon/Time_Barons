<?php
 /**
  *------
  * BGA framework: © Gregory Isabelli <gisabelli@boardgamearena.com> & Emmanuel Colin <ecolin@boardgamearena.com>
  * TimeBaronsSoupalognon implementation : © <Your name here> <Your email address here>
  * 
  * This code has been produced on the BGA studio platform for use on http://boardgamearena.com.
  * See http://en.boardgamearena.com/#!doc/Studio for more information.
  * -----
  * 
  * timebaronssoupalognon.game.php
  *
  * This is the main file for your game logic.
  *
  * In this PHP file, you are going to defines the rules of the game.
  *
  */


require_once( APP_GAMEMODULE_PATH.'module/table/table.game.php' );


class TimeBaronsSoupalognon extends Table
{
	function __construct( )
	{
        // Your global variables labels:
        //  Here, you can assign labels to global variables you are using for this game.
        //  You can use any number of global variables with IDs between 10 and 99.
        //  If your game has options (variants), you also have to associate here a label to
        //  the corresponding ID in gameoptions.inc.php.
        // Note: afterwards, you can get/set the global variables with getGameStateValue/setGameStateInitialValue/setGameStateValue
        parent::__construct();
        
        self::initGameStateLabels( array( 
            //    "my_first_global_variable" => 10,
            //    "my_second_global_variable" => 11,
            //      ...
            //    "my_first_game_variant" => 100,
            //    "my_second_game_variant" => 101,
            //      ...
        ) );    
        
        $this->cards = self::getNew( "module.common.deck" );
        $this->cards->init( "card" );    
	}
	
    protected function getGameName( )
    {
		// Used for translations and stuff. Please do not modify.
        return "timebaronssoupalognon";
    }	

    /*
        setupNewGame:
        
        This method is called only once, when a new game is launched.
        In this method, you must setup the game according to the game rules, so that
        the game is ready to be played.
    */
    protected function setupNewGame( $players, $options = array() )
    {    
        // Set the colors of the players with HTML color code
        // The default below is red/green/blue/orange/brown
        // The number of colors defined here must correspond to the maximum number of players allowed for the gams
        $gameinfos = self::getGameinfos();
        $default_colors = $gameinfos['player_colors'];
 
        // Create players
        // Note: if you added some extra field on "player" table in the database (dbmodel.sql), you can initialize it there.
        $sql = "INSERT INTO player (player_id, player_color, player_canal, player_name, player_avatar) VALUES ";
        $values = array();
        foreach( $players as $player_id => $player )
        {
            $color = array_shift( $default_colors );
            $values[] = "('".$player_id."','$color','".$player['player_canal']."','".addslashes( $player['player_name'] )."','".addslashes( $player['player_avatar'] )."')";
        }
        $sql .= implode( $values, ',' );
        self::DbQuery( $sql );
        self::reattributeColorsBasedOnPreferences( $players, $gameinfos['player_colors'] );
        self::reloadPlayersBasicInfos();
        
        // Create cards
        $cards = array ();
        foreach($this->allCards as $card) {
            $cards [] = array ('type' => $card['level'],'type_arg' => $card['level_arg'],'nbr' => $card['nbr'] );
        }
        $this->cards->createCards( $cards, 'deck' );

        //Update cards with other informations
        foreach($this->allCards as $card) {
            $sql = "UPDATE card SET  ";
            $values = array();
            $values[] = "action_point = '".$card['action_point']."', card_style = '".$card['card_style']."' WHERE (card_type = '".$card['level']."' AND card_type_arg = '".$card['level_arg']."')";
            $sql .= implode( $values, ',' );
            self::DbQuery( $sql );
        }
        
        // Shuffle deck
        $this->cards->shuffle('deck');
        $players = self::loadPlayersBasicInfos();
        foreach ( $players as $player_id => $player ) 
        {
            // $cards = $this->cards->pickCards(5, 'deck', $player_id);
            $cardsToPick = array();
            $cardsToPick = self::pickCardsLevel(1, 5);
            foreach($cardsToPick as $card) {
                $this->cards->moveCard( (int)(array_keys($card)[0]), 'hand', $player_id );
                // self::dump( 'card = ', $card);
            }
        
            //Put starting sites
            $startingSite = array();
            $startingSite = $this->cards->getCardsOfTypeInLocation( 0, 1, 'deck');
            $startingSite_id = (int)(array_keys($startingSite)[0]);
            $this->cards->moveCard( $startingSite_id, 'cardsontable', $player_id );
            
            //Put 10 followers on starting site
            self::DbQuery("UPDATE card SET followers = '10' WHERE card_id = $startingSite_id");

            // self::DbQuery("UPDATE player SET player_level = '4'");

            // $cardsToPick = self::pickCardsLevel(2, 10);
            // foreach($cardsToPick as $card) {
            //     $this->cards->moveCard( (int)(array_keys($card)[0]), 'cardsontable', $player_id );
            // }
        }

        // Init global values with their initial values
        //self::setGameStateInitialValue( 'my_first_global_variable', 0 );
        
        // Init game statistics
        // (note: statistics used in this file must be defined in your stats.inc.php file)
        //self::initStat( 'table', 'table_teststat1', 0 );    // Init a table statistics
        //self::initStat( 'player', 'player_teststat1', 0 );  // Init a player statistics (for all players)

        // TODO: setup the initial game situation here
       

        // Activate first player (which is in general a good idea :) )
        $this->activeNextPlayer();

        /************ End of the game initialization *****/
    }

    /*
        getAllDatas: 
        
        Gather all informations about current game situation (visible by the current player).
        
        The method is called each time the game interface is displayed to a player, ie:
        _ when the game starts
        _ when a player refreshes the game page (F5)
    */
    protected function getAllDatas()
    {
        $result = array();
    
        $current_player_id = self::getCurrentPlayerId();    // !! We must only return informations visible by this player !!
    
        // Get information about players
        // Note: you can retrieve some extra field you added for "player" table in "dbmodel.sql" if you need it.
        $sql = "SELECT player_id id, player_score score FROM player ";
        $result['players'] = self::getCollectionFromDb( $sql );
  
        // Cards in player hand
        $result['hand'] = $this->cards->getCardsInLocation( 'hand', $current_player_id );
    
        // Cards played on the table
        // $result['cardsontable'] = $this->cards->getCardsInLocation( 'cardsontable' );
        $sql = "SELECT card_id id, card_type type, card_type_arg type_arg, card_location_arg location_arg, life_point life_point, followers followers  FROM card WHERE (card_location = 'cardsontable')";
        $result['cardsontable'] = self::getCollectionFromDb( $sql );

        return $result;
    }

    /*
        getGameProgression:
        
        Compute and return the current game progression.
        The number returned must be an integer beween 0 (=the game just started) and
        100 (= the game is finished or almost finished).
    
        This method is called each time we are in a game state with the "updateGameProgression" property set to true 
        (see states.inc.php)
    */
    function getGameProgression()
    {
        // TODO: compute and return the game progression

        return 0;
    }


//////////////////////////////////////////////////////////////////////////////
//////////// Utility functions
////////////    

    /*
        pickCardsLevel: 
        
        Allow to pick any number of card in a specific deck level
        
        Params: 
        - level : level of the card you want to pick
        - nbCards : number of cards you want to pick

        Return:
        - Error : 
            -Deck level is empty : Return an empty array
            -Deck has not enough cards : Return all cards it can return
        - Success : an array with all cards. 
                    array($nbCards) { 
                        [positionId] => array(1) {
                            [cardId] => string(2) "cardRandomValueDuringShuffle"
                        }
                    }
    */
    function pickCardsLevel($level, $nbCards)
    {
        $i = 0;
        $pickCard = array();
        $cardsInDeck = array();
        
        $cardsInDeck[] = $this->cards->getCardsOfTypeInLocation($level, null, 'deck');

        if(count($cardsInDeck) == 0)    //If this deck level is empty
            return $pickCard;

        foreach($cardsInDeck[0] as $valuess)
        {
            if($i++ < $nbCards) { //take all cards I need at first
                $pickCard [] = array($valuess["id"] => $valuess["location_arg"]);
            }
            else {  //Then compare next cards with already taken cards to see if there are bigger ones
                // if($i == $nbCards)
                //     self::dump( 'pickcard at first = ', $pickCard);
                    
                $valueToHandle = array($valuess["id"] => $valuess["location_arg"]); //Take the next value to sort
                // self::dump( 'valueToHandle = ', $valueToHandle);

                foreach($pickCard as $position => &$cardInfo) {    //We take the address of cardInfo to be able to replace it.
                    if((int)(array_values($valueToHandle)[0]) > (int)(array_values($cardInfo)[0])) {   //If the next value to sort is bigger than the current value inside pickCard
                        // self::dump( 'Position = ', array($position => $valueToHandle));
                        // array_replace($pickCard, array($position => $valueToHandle)); //Remplace la case du tableau par la valeur aléatoire actuelle

                        $tmp = $cardInfo;  //Save the previous value to be able to compare it after
                        $cardInfo = $valueToHandle; //Replace the value inside pickCard
                        $valueToHandle = $tmp;  //Put the erased value inside valueToHandle to compare to next values inside pickCard
                    }
                }
                // self::dump( 'New Pickcard = ', $pickCard);
            }
        }
        
        // self::dump( 'pickcard at the end = ', $pickCard);

        return $pickCard;
    }

//////////////////////////////////////////////////////////////////////////////
//////////// Player actions
//////////// 

    /*
        Each time a player is doing some game action, one of the methods below is called.
        (note: each method below must match an input method in timebaronssoupalognon.action.php)
    */

    function playCard($card_id) {
        self::checkAction("playCard");
        $player_id = self::getActivePlayerId();

        $sql = "SELECT card_id id, card_type type, card_type_arg type_arg, card_location_arg location_arg, life_point life_point, followers followers, card_style card_style  FROM card WHERE (card_id = '".$card_id."')";
        $currentCard = self::getCollectionFromDb( $sql );
        $currentCard = $currentCard[$card_id];
        // self::dump( 'type of card = ', $currentCard);
        // self::dump( 'type of card = ', $currentCard[$card_id]["card_style"]);

        // $sql = "SELECT card_style card_style FROM card WHERE (card_type = '".$currentCard['type']."' AND card_type_arg = '".$currentCard['type_arg']."')";
        // $cardStyle = self::getCollectionFromDb( $sql );
        // self::dump( 'type of card = ', $cardStyle);
        // self::dump( 'type of card = ', array_keys($cardStyle)[0]);

        if($currentCard['card_style'] == 'site')
        {
            $this->cards->moveCard($card_id, 'cardsontable', $player_id);
        }
        else
        {
            $this->cards->moveCard($card_id, 'discardpile', $player_id);
        }

        // And notify
        self::notifyAllPlayers(
            'playCard', 
            clienttranslate('${player_name} plays a ${card_style} ${card_displayed}'), 
            array (
                'i18n' => array ( 'card_displayed' ),
                'player_id' => $player_id,
                'level' => $currentCard ['type'],
                'value' => $currentCard ['type_arg'],
                'card_id' => $card_id,
                'player_name' => self::getActivePlayerName(),
                'card_displayed' => '',
                // 'card_displayed' => $this->card_label[$currentCard['type']][$currentCard['type_arg']],
                'life_point' => $currentCard ['life_point'],
                'followers' => $currentCard ['followers'],
                'card_style' => $currentCard ['card_style']
            )
        );
    }

    function drawCardStep1() {
        $player_id = self::getActivePlayerId();
        
        $sql = "SELECT player_level player_level FROM player WHERE player_id = $player_id";
        $player_level = self::getCollectionFromDb( $sql );

        self::notifyPlayer(
            $player_id,
            'drawCardStep1', 
            clienttranslate('${player_name} wants to draw a card'), 
            array (
                'player_name' => self::getActivePlayerName(),
                'player_level' => (int)(array_keys($player_level)[0])
            )
        );
        // }
    }

    function drawCardStep2($level) {
        $player_id = self::getActivePlayerId();

        $cardsToPick = self::pickCardsLevel($level, 1);

        foreach($cardsToPick as $card) {
            // self::dump( 'player level = ', ($card));

            $card_id = (int)(array_keys($card)[0]);
            $sql = "SELECT card_id id, card_type type, card_type_arg type_arg, card_location_arg location_arg FROM card WHERE (card_id = '".$card_id."')";
            $currentCard = self::getCollectionFromDb( $sql );
            $currentCard = $currentCard[$card_id];

            $this->cards->moveCard( $card_id, 'hand', $player_id );

            self::notifyPlayer(
                $player_id,
                'drawCardStep2', 
                clienttranslate(''), 
                array (
                    'player_id' => $player_id,
                    'level' => $currentCard ['type'],
                    'value' => $currentCard ['type_arg'],
                    'card_id' => $card_id,
                    'player_name' => self::getActivePlayerName()
                )
            );

            self::notifyAllPlayers(
                'drawCardInfo', 
                clienttranslate('${player_name} picked level ${level} card'), 
                array (
                    'level' => $currentCard ['type'],
                    'player_name' => self::getActivePlayerName()
                )
            );
        }
    }

    function upgrade() {
        $player_id = self::getActivePlayerId();

        $sql = "SELECT player_level player_level FROM player WHERE player_id = $player_id";
        $player_level = self::getCollectionFromDb( $sql );
        $player_level = (int)(array_keys($player_level)[0]);

        if($player_level < 4) {
            self::DbQuery("UPDATE player SET player_level = player_level + 1 WHERE player_id = $player_id");

            // And notify
            self::notifyAllPlayers(
                'upgrade', 
                clienttranslate('${player_name} updrages to level ${player_level}'), 
                array (
                    'player_id' => $player_id,
                    'player_name' => self::getActivePlayerName(),
                    'player_level' => $player_level+1
                )
            );
        }
        else {
            self::notifyPlayer(
                $player_id,
                'upgrade', 
                clienttranslate('you cannot upgrade more'), 
                array (
                )
            );
        }
    }

    function takeFollower($card_id) {
        $player_id = self::getActivePlayerId();

        self::DbQuery("UPDATE card SET followers = (followers + 1) WHERE card_id = $card_id");

        // And notify
        self::notifyAllPlayers(
            'takeFollower', 
            clienttranslate('${player_name} add a follower to ${card_displayed}'), 
            array (
                'i18n' => array ( 'card_displayed' ),
                'player_id' => $player_id,
                'card_id' => $card_id,
                'player_name' => self::getActivePlayerName(),
                'card_displayed' => '',
                // 'card_displayed' => $this->card_label[$currentCard['type']][$currentCard['type_arg']],
            )
        );
    }

    /*
    relocate($isRelocating,$relocateArray)
    Params: 
        $isRelocating : to be able to know from which step we are.
        $relocateArray : array of all modifications during relocation.
                How to use it : 
                    array(number of modifications) {
                        array(2) {
                            [0] ==> card_id
                            [1] ==> number of moved follower, if it is positive it means you add followers and if it is negative you remove followers
                        }
                    }
    */
    function relocate($isRelocating,$relocateArray) {
        if($isRelocating) {
            $player_id = self::getActivePlayerId();

            // self::dump( 'player level = ', ($relocateArray));

            self::notifyPlayer(
                $player_id,
                'beginRelocate', 
                clienttranslate('You begin relocate'), 
                array (
                    'player_id' => $player_id                
                )
            );
        }
        else {
            $player_id = self::getActivePlayerId();

            //Get all cards current followers value
            $sql = "SELECT card_id id, followers followers, card_location_arg player_id FROM card";
            $cards = self::getCollectionFromDb( $sql );
            $followerCounter = 0;
            $isCheating = false;
            
            foreach($relocateArray as $value) {
                $followerCounter += $value[1];  //Verify if addition of all moved followers are equal to 0 (no follower addtion / hack)
                
                // self::dump( 'follower = ', $cards[$value[0]]['followers']);
                if( ((int)$cards[$value[0]]['followers'] + $value[1]) < 0 ) { //Verify if the difference of initial card follower and new value > 0 (hack again)
                    $isCheating = true;
                }
                else if((int)$cards[$value[0]]['player_id'] != $player_id) {
                    $isCheating = true;
                }
            }

            if($followerCounter != 0) {
                $isCheating = true;
            }

            if($isCheating) {
                $finalMsg = "cheated on relocate";
                self::debug( 'ERROR HACKING on number of followers');
            }
            else {
                $finalMsg = "relocated";

                //Everything is ok, update database
                foreach($relocateArray as $value) {
                    $sql = "UPDATE card SET ";
                    $sql .= "followers = (followers + '".$value[1]."')";
                    $sql .= "WHERE (card_id = '".$value[0]."')";
                    // self::dump( 'sql value = ', $sql);
                    self::DbQuery( $sql );
                }
            }

            self::notifyAllPlayers(
                'endRelocate', 
                clienttranslate('${player_name} ${finalMsg}'), 
                array (
                    'finalMsg' => $finalMsg,
                    'player_name' => self::getActivePlayerName(),
                    'player_id' => $player_id,
                    'isCheating' => $isCheating,
                    'relocateArray' => $relocateArray
                )
            );
        }

        $this->gamestate->nextState('relocate');
    }

    function endTurn() {
        // Next player
        $this->gamestate->nextState('endTurn');
    }

    
//////////////////////////////////////////////////////////////////////////////
//////////// Game state arguments
////////////

    /*
        Here, you can create methods defined as "game state arguments" (see "args" property in states.inc.php).
        These methods function is to return some additional information that is specific to the current
        game state.
    */

    /*
    
    Example for game state "MyGameState":
    
    function argMyGameState()
    {
        // Get some values from the current game situation in database...
    
        // return values:
        return array(
            'variable1' => $value1,
            'variable2' => $value2,
            ...
        );
    }    
    */

//////////////////////////////////////////////////////////////////////////////
//////////// Game state actions
////////////

    /*
        Here, you can create methods defined as "game state actions" (see "action" property in states.inc.php).
        The action method of state X is called everytime the current game state is set to X.
    */
    
    function stNextPlayer() {
        $player_id = self::activeNextPlayer();
        self::giveExtraTime($player_id);
        $this->gamestate->nextState('nextPlayer');
    }

//////////////////////////////////////////////////////////////////////////////
//////////// Zombie
////////////

    /*
        zombieTurn:
        
        This method is called each time it is the turn of a player who has quit the game (= "zombie" player).
        You can do whatever you want in order to make sure the turn of this player ends appropriately
        (ex: pass).
        
        Important: your zombie code will be called when the player leaves the game. This action is triggered
        from the main site and propagated to the gameserver from a server, not from a browser.
        As a consequence, there is no current player associated to this action. In your zombieTurn function,
        you must _never_ use getCurrentPlayerId() or getCurrentPlayerName(), otherwise it will fail with a "Not logged" error message. 
    */

    function zombieTurn( $state, $active_player )
    {
    	$statename = $state['name'];
    	
        if ($state['type'] === "activeplayer") {
            switch ($statename) {
                default:
                    $this->gamestate->nextState( "zombiePass" );
                	break;
            }

            return;
        }

        if ($state['type'] === "multipleactiveplayer") {
            // Make sure player is in a non blocking status for role turn
            $this->gamestate->setPlayerNonMultiactive( $active_player, '' );
            
            return;
        }

        throw new feException( "Zombie mode not supported at this game state: ".$statename );
    }
    
///////////////////////////////////////////////////////////////////////////////////:
////////// DB upgrade
//////////

    /*
        upgradeTableDb:
        
        You don't have to care about this until your game has been published on BGA.
        Once your game is on BGA, this method is called everytime the system detects a game running with your old
        Database scheme.
        In this case, if you change your Database scheme, you just have to apply the needed changes in order to
        update the game database and allow the game to continue to run with your new version.
    
    */
    
    function upgradeTableDb( $from_version )
    {
        // $from_version is the current version of this game database, in numerical form.
        // For example, if the game was running with a release of your game named "140430-1345",
        // $from_version is equal to 1404301345
        
        // Example:
//        if( $from_version <= 1404301345 )
//        {
//            // ! important ! Use DBPREFIX_<table_name> for all tables
//
//            $sql = "ALTER TABLE DBPREFIX_xxxxxxx ....";
//            self::applyDbUpgradeToAllDB( $sql );
//        }
//        if( $from_version <= 1405061421 )
//        {
//            // ! important ! Use DBPREFIX_<table_name> for all tables
//
//            $sql = "CREATE TABLE DBPREFIX_xxxxxxx ....";
//            self::applyDbUpgradeToAllDB( $sql );
//        }
//        // Please add your future database scheme changes here
//
//


    }    
}
