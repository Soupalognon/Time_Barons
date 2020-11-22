{OVERALL_GAME_HEADER}

<!-- 
--------
-- BGA framework: © Gregory Isabelli <gisabelli@boardgamearena.com> & Emmanuel Colin <ecolin@boardgamearena.com>
-- TimeBaronsSoupalognon implementation : © <Your name here> <Your email address here>
-- 
-- This code has been produced on the BGA studio platform for use on http://boardgamearena.com.
-- See http://en.boardgamearena.com/#!doc/Studio for more information.
-------

    timebaronssoupalognon_timebaronssoupalognon.tpl
    
    This is the HTML template of your game.
    
    Everything you are writing in this file will be displayed in the HTML page of your game user interface,
    in the "main game zone" of the screen.
    
    You can use in this template:
    _ variables, with the format {MY_VARIABLE_ELEMENT}.
    _ HTML block, with the BEGIN/END format
    
    See your "view" PHP file to check how to set variables and control blocks
    
    Please REMOVE this comment before publishing your game on BGA
-->


<div id="playertables">
    <div id="ActionButtons">
        <a href="#" id="relocateButton" class="bgabutton bgabutton_blue"><span>Relocate</span></a>
        <a href="#" id="drawACardButton" class="bgabutton bgabutton_blue"><span>Draw a card</span></a>
        <a href="#" id="takeAFollowerButton" class="bgabutton bgabutton_blue"><span>Take a follower</span></a>
        <a href="#" id="upgradeButton" class="bgabutton bgabutton_blue"><span>Upgrade</span></a>
        <a href="#" id="endYourTurnButton" class="bgabutton bgabutton_blue"><span>End Turn</span></a>
        <ul id="availableFollower" class="">
            <label>Available follower to move : </label>
            <span id="availableFollowerNumber">0</span>
        </ul>
    </div>

    <div id="DrawCardLevelButtons" class="styleNextStepButton">
        <a href="#" id="level1Button" class="bgabutton bgabutton_blue"><span>Level 1</span></a>
        <a href="#" id="level2Button" class="bgabutton bgabutton_blue"><span>Level 2</span></a>
        <a href="#" id="level3Button" class="bgabutton bgabutton_blue"><span>Level 3</span></a>
        <a href="#" id="level4Button" class="bgabutton bgabutton_blue"><span>Level 4</span></a>
    </div>
    
    <!-- BEGIN player -->
    <div class="playertable whiteblock playertable_{DIR}">
        <div class="playertablename" style="color:#{PLAYER_COLOR}">
            {PLAYER_NAME}
        </div>
        
        <div class="playertablecard" id="playertablecard_{PLAYER_ID}">    
            <!-- <div class="cardtext">This is the card information</div> -->
            <!-- <a href="#" id="temp1" class="bgabutton relocateButtons addFollower"><span></span></a>
            <a href="#" id="temp2" class="bgabutton relocateButtons removeFollower"><span></span></a> -->
            <!-- <button class="tempPlus"></button> -->
        </div>
    </div>
    <!-- END player -->

</div>

<div id="myhand_wrap" class="whiteblock">
    <h3>{MY_HAND}</h3>
    <div class="playertablecard" id="myhand">
    </div>
</div>

<script type="text/javascript">

    var jstpl_cardontable = '<div class="cardontable" id="cardontable_${player_id}" style="background-position:-${x}px -${y}px">\
                            </div>';

    var jstpl_cardtext = '<div class="cardtext" id="cardtext_${player_id}_${card_id}" style="left: ${left}px; top: ${top}px;" >\
                        ${text}\
                        </div>';

    var jstpl_plusMinusButtons = '<a href="#" class="plusMinusButtons" id="relocateButtons_${type}_${player_id}_${card_id}" style="left: ${left}px; top: ${top}px;" >\
                            </a>';
                         
</script>

{OVERALL_GAME_FOOTER}
