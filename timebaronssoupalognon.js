/**
 *------
 * BGA framework: © Gregory Isabelli <gisabelli@boardgamearena.com> & Emmanuel Colin <ecolin@boardgamearena.com>
 * TimeBaronsSoupalognon implementation : © <Your name here> <Your email address here>
 *
 * This code has been produced on the BGA studio platform for use on http://boardgamearena.com.
 * See http://en.boardgamearena.com/#!doc/Studio for more information.
 * -----
 *
 * timebaronssoupalognon.js
 *
 * TimeBaronsSoupalognon user interface script
 * 
 * In this file, you are describing the logic of your user interface, in Javascript language.
 *
 */
define([
    "dojo","dojo/_base/declare",
    "ebg/core/gamegui",
    "ebg/counter",
    "ebg/stock"
],
function (dojo, declare) {
    return declare("bgagame.timebaronssoupalognon", ebg.core.gamegui, {
        constructor: function(){
            console.log('timebaronssoupalognon constructor');
              
            // Here, you can init the global variables of your user interface
            this.cardwidth = 220;
            this.cardheight = 300;
            this.cardsOnTableWidth = dojo.position("game_play_area").w - 100;   //This function is not perfect, it does not get the exact Stock width... Find a way to get it!!
            // this.cardsOnTableHeight = dojo.position("game_play_area").h;
            this.image_items_per_row = 13;

            this.isRelocating = false;
            this.relocateArray = [];
            this.availableFollower = 0;
        },
        
        /*
            setup:
            
            This method must set up the game user interface according to current game situation specified
            in parameters.
            
            The method is called each time the game interface is displayed to a player, ie:
            _ when the game starts
            _ when a player refreshes the game page (F5)
            
            "gamedatas" argument contains all datas retrieved by your "getAllDatas" PHP method.
        */
        
        setup: function( gamedatas )
        {
            console.log( "Starting game setup" );
            
            // Setting up player boards
            for( var player_id in gamedatas.players ){
                var player = gamedatas.players[player_id];
            }
            
            this.initPlayerHand();
            this.initPlayerBoards(gamedatas.players);

            // var callback = dojo.hitch( this, 'onRelocateButton', 10 );
            // dojo.query('#relocateButton').connect('onclick', this, callback);
            // dojo.query('#drawACardButton').connect('onclick', this, 'onDrawACardButton');
            // dojo.query('#takeAFollowerButton').connect('onclick', this, 'onTakeAFollowerButton');
            // dojo.query('#upgradeButton').connect('onclick', this, 'onUpgradeButton');
            // dojo.query('#endYourTurnButton').connect('onclick', this, 'onEndYourTurnButton');

            dojo.query('#relocateButton').connect('onclick', this, dojo.hitch( this, 'onRelocateButton', 'relocate' ));
            dojo.query('#takeAFollowerButton').connect('onclick', this, dojo.hitch( this, 'onTakeFollowerButton', 'takeFollower' ));
            dojo.query('#drawACardButton').connect('onclick', this, dojo.hitch( this, 'onActionButton', 'drawCard' ));
            dojo.query('#upgradeButton').connect('onclick', this, dojo.hitch( this, 'onActionButton', 'upgrade' ));
            dojo.query('#endYourTurnButton').connect('onclick', this, dojo.hitch( this, 'onActionButton', 'endTurn' ));
            
            dojo.query('#level1Button').connect('onclick', this, dojo.hitch( this, 'onActionButton', 'level1' ));
            dojo.query('#level2Button').connect('onclick', this, dojo.hitch( this, 'onActionButton', 'level2' ));
            dojo.query('#level3Button').connect('onclick', this, dojo.hitch( this, 'onActionButton', 'level3' ));
            dojo.query('#level4Button').connect('onclick', this, dojo.hitch( this, 'onActionButton', 'level4' ));

            document.getElementById("DrawCardLevelButtons").style.display = 'none';
            document.getElementById("availableFollower").style.visibility = 'hidden';

            // console.log( 'playerID: '+  this.playerBoards);
            // console.log( 'playerID: '+  JSON.stringify(gamedatas.players));

            // Cards played on table
            // for (i in this.gamedatas.cardsontable) {
            //     var card = this.gamedatas.cardsontable[i];
            //     var level = card.type;
            //     var value = card.type_arg;
            //     var player_id = card.location_arg;
            //     this.playCardOnTable(player_id, level, value, card.id);
            // }

            // Setup game notifications to handle (see "setupNotifications" method below)
            this.setupNotifications();

            console.log( "Ending game setup" );
        },

        initPlayerHand : function()
        {
            // Player hand
           this.playerHand = new ebg.stock(); // new stock object for hand
           this.playerHand.create( this, $('myhand'), this.cardwidth, this.cardheight );
           dojo.connect( this.playerHand, 'onChangeSelection', this, 'onPlayerHandSelectionChanged' );

           this.playerHand.image_items_per_row = this.image_items_per_row;
            
            // Create cards types:
            for (value = 1; value <= 2; value++) {
                var tmp = this.getCardPositionOnJPG_Stock(0, value);
                this.playerHand.addItemType(tmp, tmp, g_gamethemeurl + 'img/cards.jpg', tmp);
            }
            for (value = 1; value <= 12; value++) {
                var tmp = this.getCardPositionOnJPG_Stock(1, value);
                this.playerHand.addItemType(tmp, tmp, g_gamethemeurl + 'img/cards.jpg', tmp);
            }
            for (value = 1; value <= 12; value++) {
                var tmp = this.getCardPositionOnJPG_Stock(2, value);
                this.playerHand.addItemType(tmp, tmp, g_gamethemeurl + 'img/cards.jpg', tmp);
            }
            for (value = 1; value <= 8; value++) {
                var tmp = this.getCardPositionOnJPG_Stock(3, value);
                this.playerHand.addItemType(tmp, tmp, g_gamethemeurl + 'img/cards.jpg', tmp);
            }
            for (value = 1; value <= 5; value++) {
                var tmp = this.getCardPositionOnJPG_Stock(4, value);
                this.playerHand.addItemType(tmp, tmp, g_gamethemeurl + 'img/cards.jpg', tmp);
            }
 
            // Cards in player's hand
            for ( var i in this.gamedatas.hand) {
                var card = this.gamedatas.hand[i];
                var level = card.type;
                var value = card.type_arg;
                this.playerHand.addToStockWithId(this.getCardPositionOnJPG_Stock(level, value), card.id);
            }
        },

        initPlayerBoards : function(players)
        {
            this.playerBoards = [];

            for( var player in players )
            {
                this.playerBoards.push({'id' : players[player].id, 'cards' : new ebg.stock()});
                var index = this.getPlayerBoardIndex(players[player].id);   //find the position on this array where the playerBoards is.
                // console.log('index = ' + JSON.stringify(this.playerBoards[index].cards));

                this.playerBoards[index].cards.create( this, $('playertablecard_' + players[player].id), this.cardwidth, this.cardheight );
                this.playerBoards[index].cards.setSelectionMode(1);  //Configure player board to be able to select only one card at a time

                this.playerBoards[index].cards.image_items_per_row = this.image_items_per_row;
            
                // Create cards types:
                for (value = 1; value <= 2; value++) {
                    var tmp = this.getCardPositionOnJPG_Stock(0, value);
                    this.playerBoards[index].cards.addItemType(tmp, tmp, g_gamethemeurl + 'img/cards.jpg', tmp);
                }
                for (value = 1; value <= 12; value++) {
                    var tmp = this.getCardPositionOnJPG_Stock(1, value);
                    this.playerBoards[index].cards.addItemType(tmp, tmp, g_gamethemeurl + 'img/cards.jpg', tmp);
                }
                for (value = 1; value <= 12; value++) {
                    var tmp = this.getCardPositionOnJPG_Stock(2, value);
                    this.playerBoards[index].cards.addItemType(tmp, tmp, g_gamethemeurl + 'img/cards.jpg', tmp);
                }
                for (value = 1; value <= 8; value++) {
                    var tmp = this.getCardPositionOnJPG_Stock(3, value);
                    this.playerBoards[index].cards.addItemType(tmp, tmp, g_gamethemeurl + 'img/cards.jpg', tmp);
                }
                for (value = 1; value <= 5; value++) {
                    var tmp = this.getCardPositionOnJPG_Stock(4, value);
                    this.playerBoards[index].cards.addItemType(tmp, tmp, g_gamethemeurl + 'img/cards.jpg', tmp);
                }
    
                // console.log('Item:' + JSON.stringify(this.gamedatas.cardsontable));
                // Cards on player's board
                for ( var i in this.gamedatas.cardsontable) {
                    var card = this.gamedatas.cardsontable[i];
                    var level = card.type;
                    var value = card.type_arg;
                    var player_id = card.location_arg;
                    if(players[player].id == player_id)
                    {
                        this.playerBoards[index].cards.addToStockWithId(this.getCardPositionOnJPG_Stock(level, value), card.id);
                        this.UpdateCardInfo(players[player].id);
                    }
                }                
            }

            // console.log( 'playerboard keys: '+  JSON.stringify(this.playerBoards));
        },


        ///////////////////////////////////////////////////
        //// Game & client states
        
        // onEnteringState: this method is called each time we are entering into a new game state.
        //                  You can use this method to perform some user interface changes at this moment.
        //
        onEnteringState: function( stateName, args )
        {
            console.log( 'Entering state: '+stateName );
            
            switch( stateName )
            {
            
            /* Example:
            
            case 'myGameState':
            
                // Show some HTML block at this game state
                dojo.style( 'my_html_block_id', 'display', 'block' );
                
                break;
           */
           
           
            case 'dummmy':
                break;
            }
        },

        // onLeavingState: this method is called each time we are leaving a game state.
        //                 You can use this method to perform some user interface changes at this moment.
        //
        onLeavingState: function( stateName )
        {
            console.log( 'Leaving state: '+stateName );
            
            switch( stateName )
            {
            
            /* Example:
            
            case 'myGameState':
            
                // Hide the HTML block we are displaying only during this game state
                dojo.style( 'my_html_block_id', 'display', 'none' );
                
                break;
           */
           
           
            case 'dummmy':
                break;
            }               
        }, 

        // onUpdateActionButtons: in this method you can manage "action buttons" that are displayed in the
        //                        action status bar (ie: the HTML links in the status bar).
        //        
        onUpdateActionButtons: function( stateName, args )
        {
            console.log( 'onUpdateActionButtons: '+stateName );
                      
            if( this.isCurrentPlayerActive() )
            {            
                switch( stateName )
                {
/*               
                 Example:
 
                 case 'myGameState':
                    
                    // Add 3 action buttons in the action status bar:
                    
                    this.addActionButton( 'button_1_id', _('Button 1 label'), 'onMyMethodToCall1' ); 
                    this.addActionButton( 'button_2_id', _('Button 2 label'), 'onMyMethodToCall2' ); 
                    this.addActionButton( 'button_3_id', _('Button 3 label'), 'onMyMethodToCall3' ); 
                    break;
*/
                }
            }
        },        

        ///////////////////////////////////////////////////
        //// Utility methods
        
        /*
        
            Here, you can defines some utility methods that you can use everywhere in your javascript
            script.
        
        */

        UpdateCardInfo: function(player_id)
        {
            var index = this.getPlayerBoardIndex(player_id);   //find the position on this array where the playerBoards is.
            cards = this.playerBoards[index].cards.getAllItems();
            // console.log('cards: ' + JSON.stringify(cards));
            // console.log('count cards: ' + JSON.stringify(cards.length));

            tmp = 0;
            cards.forEach(card =>   //BE CAREFUL, the 'card' used here contain only information inside a Stock, so only database ID (card.id) and JPG ID(card.type) obtained with function getCardPositionOnJPG_Stock()
            {
                textPosition = this.getCardTextPosition(tmp);
                life_point = this.gamedatas.cardsontable[card.id].life_point;
                followers = this.gamedatas.cardsontable[card.id].followers;

                position = this.relocateArray.findIndex(p => p.id == card.id);
                if(this.isRelocating && (position != -1)) { //If we already moved player from/to this card and we are currently relocating
                    // console.log("already moved");
                    followers = (Number(followers) + this.relocateArray[position].movedFollower).toString();
                }

                // console.log('position: ' + JSON.stringify(textPosition));

                if($('cardtext_' + player_id + '_' + card.id) == null)  //If card information display is not already created
                {
                    // console.log('count existe pas');
                    dojo.place(
                        this.format_block('jstpl_cardtext', 
                        {
                            player_id : player_id,
                            card_id : card.id,
                            top : textPosition.pos_top,
                            left : textPosition.pos_left,
                            text : 'followers:' + followers + '  /  HP:' + life_point
                            // text : 'level:' + this.gamedatas.cardsontable[card.id].type
                        }), 
                        'playertablecard_' + player_id,
                    );
                    // dojo.addClass( 'cardtext_' + player_id + '_' + card.id, 'cardtext' );
                }
                else    //If it is already created, just move its position in front of its card
                {
                    $('cardtext_' + player_id + '_' + card.id).innerHTML = 'followers:' + followers + '  /  HP:' + life_point;
                    dojo.style($('cardtext_' + player_id + '_' + card.id), {left : (textPosition.pos_left.toString() + "px"), top : textPosition.pos_top.toString() + "px"});
                }

                tmp += 1;
            });
        },


        // Get card unique identifier based on its level and value
        getCardPositionOnJPG_Stock : function(level, value) {
            var ret = value - 1;
            if(level > 0)
                ret += 2;  //Number of card level 0 (starting sites)
            if(level > 1)
                ret += 12;  //Number of card level 1
            if(level > 2)
                ret += 12;  //Number of card level 2
            if(level > 3)
                ret += 8;  //Number of card level 3

            return ret;
        },

        //Fonction a optimiser
        getCardPositionOnJPG_DojoPlace : function(level, value) {       
            pos_x = 0, pos_y = 0;
            id = this.getCardPositionOnJPG_Stock(level, value);

            while(id >= this.image_items_per_row) {
                if(id >= this.image_items_per_row) {
                    id -= this.image_items_per_row;
                    pos_y += 1;
                }
            }
            pos_x = id;

            // console.log('pos_x = ' + pos_x + '    pos_y = ' + pos_y);
            return {
                pos_x, 
                pos_y
            };
        },

        //Return board id of a player, to be able to select the playBoard
        getPlayerBoardIndex: function(player_id) {
            return this.playerBoards.findIndex(p => p.id == player_id);
        },

        getCardTextPosition: function(cardPositionIndex) {
            pos_left = 0, pos_top = 0;

            maximumNumberOfCardsOnARow = Math.floor(this.cardsOnTableWidth / this.cardwidth);
            lineNumber = Math.floor(cardPositionIndex / maximumNumberOfCardsOnARow);
            rowNumber = cardPositionIndex % maximumNumberOfCardsOnARow;

            pos_left = (this.cardwidth + 5)*rowNumber + 12;   //5px pour la marge entre les cartes, 12px pour la bordure de la carte
            pos_top = (this.cardheight + 5)*lineNumber + 12;    //5px pour la marge entre les cartes, 10px pour la bordure de la carte

            // console.log('id:' + cardPositionIndex + ' / top:' + pos_top + ' / left:' + pos_left);

            return {
                pos_left, 
                pos_top
            };
        },

        getCardRelocatePlusButtonPosition: function(cardPositionIndex) {
            pos_left = 0, pos_top = 0;

            maximumNumberOfCardsOnARow = Math.floor(this.cardsOnTableWidth / this.cardwidth);
            lineNumber = Math.floor(cardPositionIndex / maximumNumberOfCardsOnARow);
            rowNumber = cardPositionIndex % maximumNumberOfCardsOnARow;

            pos_left = (this.cardwidth)*rowNumber + 75;   //5px pour la marge entre les cartes, 58px pour être au milieu
            pos_top = (this.cardheight)*lineNumber + 260;    //5px pour la marge entre les cartes, -12 pour être en bas de la carte

            // console.log('id:' + cardPositionIndex + ' / top:' + pos_top + ' / left:' + pos_left);

            return {
                pos_left, 
                pos_top
            };
        },

        playCardOnTable : function(player_id, level, value, card_id, life_point, followers, card_style) 
        {
            //Add the card played into gamedatas informations 
            this.gamedatas.cardsontable[card_id] = {'id' : card_id, 'type' : level, 'type_arg' : value, 'location_arg' : player_id, 'life_point' : life_point, 'followers' : followers};

            dojoPosition = this.getCardPositionOnJPG_DojoPlace(level, value);
            
            if(card_style == 'site')
            {
                dojo.place(
                    this.format_block('jstpl_cardontable', 
                    {
                        x : this.cardwidth * dojoPosition.pos_x,
                        y : this.cardheight * dojoPosition.pos_y,
                        player_id : player_id
                    }), 
                    'playertablecard_' + player_id
                );

                if (player_id != this.player_id) {
                    // Some opponent played a card
                    // Move card from player panel
                    this.placeOnObject('cardontable_' + player_id, 'overall_player_board_' + player_id);
                    var index = this.getPlayerBoardIndex(player_id);   //find the position on this array where the playerBoards is.
                    this.playerBoards[index].cards.addToStockWithId(this.getCardPositionOnJPG_Stock(level, value), card_id);
                } else {
                    // You played a card. If it exists in your hand, move card from there and remove
                    // corresponding item

                    if ($('myhand_item_' + card_id)) {
                        this.placeOnObject('cardontable_' + player_id, 'myhand_item_' + card_id);
                        var index = this.getPlayerBoardIndex(player_id);   //find the position on this array where the playerBoards is.
                        this.playerBoards[index].cards.addToStockWithId(this.getCardPositionOnJPG_Stock(level, value), card_id);

                        this.playerHand.removeFromStockById(card_id);
                    }
                }

                this.UpdateCardInfo(player_id);

                // In any case: move it to its final destination
                // this.slideToObject('cardontable_' + player_id, 'playertablecard_' + player_id,).play();
                this.slideToObjectAndDestroy('cardontable_' + player_id, 'playertablecard_' + player_id,);
            }
            else
            {
                dojo.place(
                    this.format_block('jstpl_cardontable', 
                    {
                        x : this.cardwidth * dojoPosition.pos_x,
                        y : this.cardheight * dojoPosition.pos_y,
                        player_id : player_id
                    }), 
                    'playertables'
                );

                if (player_id != this.player_id) {
                    // Some opponent played a card
                    // Move card from player panel
                    this.placeOnObject('cardontable_' + player_id, 'overall_player_board_' + player_id);

                    //Put into discard pile
                } else {
                    // You played a card. If it exists in your hand, move card from there and remove
                    // corresponding item

                    if ($('myhand_item_' + card_id)) {
                        this.placeOnObject('cardontable_' + player_id, 'myhand_item_' + card_id);

                        this.playerHand.removeFromStockById(card_id);
                    }
                }

                // In any case: move it to its final destination
                // this.slideToObject('cardontable_' + player_id, 'playertablecard_' + player_id,).play();
                this.slideToObjectAndDestroy('cardontable_' + player_id, 'playertables', 2000);
            }
        },

        ///////////////////////////////////////////////////
        //// Player's action
        
        /*
        
            Here, you are defining methods to handle player's action (ex: results of mouse click on 
            game objects).
            
            Most of the time, these methods:
            _ check the action is possible at this game state.
            _ make a call to the game server
        
        */
        onActionButton: function(arg) {
            if(this.player_id == this.getActivePlayerId()) {
                if(arg == 'drawCard') {
                    console.log( "drawCard" );
                }
                else if(arg == 'upgrade') {
                    console.log( "upgrade" );
                }
                else if(arg == 'endTurn') {
                    console.log( "endTurn" );
                }
                else if(arg == 'level1') {
                    console.log( "level 1" );
                }
                else if(arg == 'level2') {
                    console.log( "level 2" );
                }
                else if(arg == 'level3') {
                    console.log( "level 3" );
                }
                else if(arg == 'level4') {
                    console.log( "level 4" );
                }
            
                var action = arg;
                if (this.checkAction(action, true)) {
                    // Can play a card                    
                    this.ajaxcall("/" + this.game_name + "/" + this.game_name + "/" + action + ".html", {
                        lock : true
                    }, this, function(result) {
                    }, function(is_error) {
                    });
                }
            }
            else {
                this.showMessage( "This is not you turn", "error" );
            }
        },

        onRelocateButton : function(arg) {
            if(this.player_id == this.getActivePlayerId()) {
                this.isRelocating = !this.isRelocating;

                if(this.isRelocating) {
                    console.log( "begin relocate");
                }
                else  {
                    console.log( "end relocate");
                    
                    if(this.availableFollower > 0) {
                        this.showMessage( "You still have followers to put on cards", "info" );
                        return;
                    }
                }

                relocateArrayAsString = "";
                this.relocateArray.forEach(element => {
                    relocateArrayAsString += element.id.toString();
                    relocateArrayAsString += ",";
                    relocateArrayAsString += element.movedFollower.toString();
                    relocateArrayAsString += ";";
                });
                // console.log(relocateArrayAsString);

                var action = arg;
                if (this.checkAction(action, true)) {
                    this.ajaxcall("/" + this.game_name + "/" + this.game_name + "/" + action + ".html", {
                        relocateArray : relocateArrayAsString,
                        isRelocating : this.isRelocating ? 1 : 0,   //Must be an integer
                        lock : true
                    }, this, function(result) {
                    }, function(is_error) {
                    });
                }
            }
            else {
                this.showMessage( "This is not you turn", "error" );
            }
        },

        onTakeFollowerButton : function(arg) {
            var index = this.getPlayerBoardIndex(this.getActivePlayerId());   //find the position on this array where the playerBoards is.
            var items = this.playerBoards[index].cards.getSelectedItems();
            // console.log("item = " + JSON.stringify(items));

            if(this.player_id == this.getActivePlayerId()) {
                if (items.length > 0) {
                    var action = arg;
                    if (this.checkAction(action, true)) {
                        console.log( "takeFollower" );

                        var card_id = items[0].id;
                        this.ajaxcall("/" + this.game_name + "/" + this.game_name + "/" + action + ".html", {
                            id : card_id,
                            lock : true
                        }, this, function(result) {
                        }, function(is_error) {
                        });

                        this.playerBoards[index].cards.unselectAll();
                    }
                }
                else {
                    this.showMessage( "You must choose a site where follower will go", "info" );
                }
            }
            else {
                this.showMessage( "This is not you turn", "error" );
            }
        },

        onPlayerHandSelectionChanged : function() {
            var items = this.playerHand.getSelectedItems();

            if (items.length > 0) {
                var action = 'playCard';
                if (this.checkAction(action, true)) {
                    // Can play a card
                    var card_id = items[0].id;                    
                    this.ajaxcall("/" + this.game_name + "/" + this.game_name + "/" + action + ".html", {
                        id : card_id,
                        lock : true
                    }, this, function(result) {
                    }, function(is_error) {
                    });

                    this.playerHand.unselectAll();
                } else {
                    this.playerHand.unselectAll();
                }
            }
        },

        /*
            onRelocatePlusMinus:
            This function is called when one of all relocate button is used.


            Params :    player_id : Don't know what to eplain more
                        type : There are two types of button, "Plus" or "Minus"
                        card_id : This is the database ID
        */
       onRelocatePlusMinus : function(player_id, type, card_id, stock_id) {
            // console.log("Click on button " + type + " for the stock_id: " + stock_id);
            tmp = {'id' : card_id, 'movedFollower' : 0};
            position = this.relocateArray.findIndex(p => p.id == card_id);

            //Verify conditions and handle value to put in relocate array
            if(type == "Plus") {
                tmp.movedFollower += 1;

                // if(this.availableFollower <= 0) {
                //     this.showMessage( "You do not have any follower available to move", "info" );
                //     return;
                // }
                this.availableFollower -= 1;
            }
            else if(type == "Minus") {
                tmp.movedFollower -= 1;

                //If we goes to a number of follower bellow 0
                alreadyMovedFollower = (position == -1) ? 0 : this.relocateArray[position].movedFollower;
                if((Number(this.gamedatas.cardsontable[card_id]['followers']) + alreadyMovedFollower + tmp.movedFollower) < 0) {
                    // this.showMessage( "You cannot ", "info" );
                    return;
                }
                this.availableFollower += 1;
            }
            else {
                this.showMessage( "onRelocatePlusMinus function, argument is not recognized, go to JS file", "error" );
            }

            //Modify the relocate array
            if(position == -1) {   //If we never add or remove follower on this card
                this.relocateArray.push(tmp);
            }
            else {
                this.relocateArray[position].movedFollower += tmp.movedFollower;
            }

            // console.table(this.relocateArray);
            // console.log('available follower : ' + this.availableFollower);
            document.getElementById("availableFollowerNumber").innerHTML = this.availableFollower;

            this.UpdateCardInfo(player_id);
        },
        
        ///////////////////////////////////////////////////
        //// Reaction to cometD notifications

        /*
            setupNotifications:
            
            In this method, you associate each of your game notifications with your local method to handle it.
            
            Note: game notification names correspond to "notifyAllPlayers" and "notifyPlayer" calls in
                  your timebaronssoupalognon.game.php file.
        
        */
        setupNotifications: function()
        {
            console.log( 'notifications subscriptions setup' );
            
            // dojo.subscribe('newHand', this, "notif_newHand");
            dojo.subscribe('playCard', this, "notif_playCard");
            dojo.subscribe('drawCardStep1', this, "notif_drawCardStep1");
            dojo.subscribe('drawCardStep2', this, "notif_drawCardStep2");
            dojo.subscribe('takeFollower', this, "notif_takeFollower");
            dojo.subscribe('beginRelocate', this, "notif_beginRelocate");
            dojo.subscribe('endRelocate', this, "notif_endRelocate");

            // dojo.subscribe( 'newScores', this, "notif_newScores" );
        },  
        
        notif_playCard : function(notif) {
            // Play a card on the table
            this.playCardOnTable(notif.args.player_id, notif.args.level, notif.args.value, notif.args.card_id, notif.args.life_point, notif.args.followers, notif.args.card_style);
        },

        notif_drawCardStep1 : function(notif) {
            // Draw a card on the hand
            playerLevel = notif.args.player_level;
            // console.log('Player level = ' + JSON.stringify(playerLevel));

            document.getElementById("level2Button").style.visibility = "hidden";
            document.getElementById("level3Button").style.visibility = "hidden";
            document.getElementById("level4Button").style.visibility = "hidden";

            document.getElementById("ActionButtons").style.display = 'none';
            document.getElementById("DrawCardLevelButtons").style.display = 'block';

            if(playerLevel > 1) {
                document.getElementById("level2Button").style.visibility = "visible";
            }
            if(playerLevel > 2) {
                document.getElementById("level3Button").style.visibility = "visible";
            }
            if(playerLevel > 3) {
                document.getElementById("level4Button").style.visibility = "visible";
            }
        },

        notif_drawCardStep2 : function(notif) {
            // Draw a card on the hand
            player_id = notif.args.player_id;

            card_id = notif.args.card_id;
            level = notif.args.level;
            value = notif.args.value;

            // this.placeOnObject('cardontable_' + player_id, 'overall_player_board_' + player_id);
            this.playerHand.addToStockWithId(this.getCardPositionOnJPG_Stock(level, value), card_id);

            // In any case: move it to its final destination
            // this.slideToObject('cardontable_' + player_id, 'myhand',).play();

            document.getElementById("ActionButtons").style.display = 'block';
            document.getElementById("DrawCardLevelButtons").style.display = 'none';
        },

        notif_takeFollower : function(notif) {
            // Draw a card on the hand
            player_id = notif.args.player_id;
            card_id = notif.args.card_id;

            // console.log("take Follower on card id : " + card_id);

            //Add the follower onto gamedatas information
            this.gamedatas.cardsontable[card_id]['followers'] = (Number(this.gamedatas.cardsontable[card_id]['followers']) + 1).toString();
            // console.table(this.gamedatas.cardsontable[card_id]);

            this.UpdateCardInfo(player_id);
        },

        notif_beginRelocate : function(notif) {
            $("relocateButton").innerHTML = 'End relocate';

            document.getElementById("drawACardButton").style.visibility = 'hidden';
            document.getElementById("takeAFollowerButton").style.visibility = 'hidden';
            document.getElementById("upgradeButton").style.visibility = 'hidden';
            document.getElementById("endYourTurnButton").style.visibility = 'hidden';

            document.getElementById("availableFollower").style.visibility = 'visible';

            player_id = notif.args.player_id;

            var index = this.getPlayerBoardIndex(player_id);   //find the position on this array where the playerBoards is.
            cards = this.playerBoards[index].cards.getAllItems();
            // console.log('cards: ' + JSON.stringify(cards));
            // console.log('count cards: ' + JSON.stringify(cards.length));

            tmp = 0;
            cards.forEach(card =>   //BE CAREFUL, the 'card' used here contain only information inside a Stock, so only database ID (card.id) and JPG ID(card.type) obtained with function getCardPositionOnJPG_Stock()
            {
                plusButtonPosition = this.getCardRelocatePlusButtonPosition(tmp);
                type = "";

                for(i=0; i<2; i++) {    //Used for the plus and minus button
                    if(i == 0) {
                        type = 'Plus';
                    }
                    else {
                        type = 'Minus';
                        plusButtonPosition.pos_left += 45;
                    }
                    buttonName = 'relocateButtons_' + type + '_' + player_id + '_' + card.id;

                    //Create or display every Plus Minus buttons
                    if($(buttonName) == null)  //If card information display is not already created
                    {
                        dojo.place(
                            this.format_block('jstpl_plusMinusButtons', 
                            {
                                type : type,
                                player_id : player_id,
                                card_id : card.id,
                                top : plusButtonPosition.pos_top,
                                left : plusButtonPosition.pos_left,
                            }), 
                            'playertablecard_' + player_id,
                        );

                        if(i == 0)
                            dojo.addClass( buttonName, 'addFollower' );
                        else
                            dojo.addClass( buttonName, 'removeFollower' );

                        dojo.query('#' + buttonName).connect('onclick', this, dojo.hitch( this, 'onRelocatePlusMinus', //Parameters here
                                        player_id,
                                        type,
                                        card.id,
                                        card.type,
                                ));
                    }
                    else {    //If it is already created, just move its position in front of its card
                        document.getElementById('relocateButtons_' + type + '_' + player_id + '_' + card.id).style.visibility = 'visible';
                        dojo.style($(buttonName), 
                                    { left : (plusButtonPosition.pos_left.toString() + "px"), top : plusButtonPosition.pos_top.toString() + "px" }
                                );
                    }
                }

                tmp += 1;
            });

        },

        notif_endRelocate : function(notif) {
            if(notif.args.player_id == this.player_id) {
                $("relocateButton").innerHTML = 'Relocate';

                document.getElementById("drawACardButton").style.visibility = 'visible';
                document.getElementById("takeAFollowerButton").style.visibility = 'visible';
                document.getElementById("upgradeButton").style.visibility = 'visible';
                document.getElementById("endYourTurnButton").style.visibility = 'visible';

                document.getElementById("availableFollower").style.visibility = 'hidden';

                //Hide every Plus Minus buttons
                cards.forEach(card =>   //BE CAREFUL, the 'card' used here contain only information inside a Stock, so only database ID (card.id) and JPG ID(card.type) obtained with function getCardPositionOnJPG_Stock()
                {
                    type = "";

                    for(i=0; i<2; i++) {    //Used for the plus and minus button
                        if(i == 0) {
                            type = 'Plus';
                        }
                        else {
                            type = 'Minus';
                        }
                        
                        document.getElementById('relocateButtons_' + type + '_' + player_id + '_' + card.id).style.visibility = 'hidden';
                    }
                });

                if(notif.args.isCheating) {
                    this.showMessage( "YOU ARE A CHEATER!", "error" );
                    this.UpdateCardInfo(notif.args.player_id);
                }
                else {
                    //Update gamedatas
                    this.relocateArray.forEach(element => {
                        card_id = element.id;
                        movedFollower = element.movedFollower;
                        this.gamedatas.cardsontable[card_id].followers = (Number(this.gamedatas.cardsontable[card_id].followers) + movedFollower).toString();
                    });
                }
            }
            else {

                if(notif.args.isCheating) {
                    this.showMessage( notif.args.player_name + " IS A CHEATER!", "info" );
                }
                else {
                    notif.args.relocateArray.forEach(element => {
                        card_id = element[0];
                        movedFollower = Number(element[1]);
                        this.gamedatas.cardsontable[card_id].followers = (Number(this.gamedatas.cardsontable[card_id].followers) + movedFollower).toString();
                    });

                    this.UpdateCardInfo(notif.args.player_id);
                }
            }
        },
   });

});
