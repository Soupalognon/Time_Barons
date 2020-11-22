<?php
/**
 *------
 * BGA framework: © Gregory Isabelli <gisabelli@boardgamearena.com> & Emmanuel Colin <ecolin@boardgamearena.com>
 * heartsTutoSoupa implementation : © <Your name here> <Your email address here>
 * 
 * This code has been produced on the BGA studio platform for use on http://boardgamearena.com.
 * See http://en.boardgamearena.com/#!doc/Studio for more information.
 * -----
 *
 * material.inc.php
 *
 * heartsTutoSoupa game material description
 *
 * Here, you can describe the material of your game with PHP variables.
 *   
 * This file is loaded in your game logic class constructor, ie these variables
 * are available everywhere in your game logic code.
 *
 */

$this->allCards = array();

$this->allCards[]  = array('name' => 'Homeland', 'card_style' => 'site', 'level' => '0', 'level_arg' => '1', 'action_cost' => '0', 'possible_actions' => '0', 'number_of_cards' => '2');
$this->allCards[]  = array('name' => 'Capital', 'card_style' => 'site', 'level' => '0', 'level_arg' => '2', 'action_cost' => '2', 'possible_actions' => '0', 'number_of_cards' => '4');

$this->allCards[]  = array('name' => 'Catapult', 'card_style' => 'site', 'level' => '1', 'level_arg' => '1', 'action_cost' => '2', 'possible_actions' => '2', 'number_of_cards' => '2');
$this->allCards[]  = array('name' => 'Temple', 'card_style' => 'site', 'level' => '1', 'level_arg' => '2', 'action_cost' => '1', 'possible_actions' => '1', 'number_of_cards' => '2');
$this->allCards[]  = array('name' => 'Mission', 'card_style' => 'site', 'level' => '1', 'level_arg' => '3', 'action_cost' => '1', 'possible_actions' => '2', 'number_of_cards' => '2');
$this->allCards[]  = array('name' => 'Fort', 'card_style' => 'site', 'level' => '1', 'level_arg' => '4', 'action_cost' => '2', 'possible_actions' => '0', 'number_of_cards' => '2');
$this->allCards[]  = array('name' => 'Church', 'card_style' => 'site', 'level' => '1', 'level_arg' => '5', 'action_cost' => '2', 'possible_actions' => '1', 'number_of_cards' => '2');
$this->allCards[]  = array('name' => 'Plague', 'card_style' => 'Attachment', 'level' => '1', 'level_arg' => '6', 'action_cost' => '1', 'possible_actions' => '0', 'number_of_cards' => '2');
$this->allCards[]  = array('name' => 'Siege', 'card_style' => 'event', 'level' => '1', 'level_arg' => '7', 'action_cost' => '1', 'possible_actions' => '0', 'number_of_cards' => '2');
$this->allCards[]  = array('name' => 'Fanaticism', 'card_style' => 'event', 'level' => '1', 'level_arg' => '8', 'action_cost' => '2', 'possible_actions' => '0', 'number_of_cards' => '2');
$this->allCards[]  = array('name' => 'Divine Inspiration', 'card_style' => 'event', 'level' => '1', 'level_arg' => '9', 'action_cost' => '1', 'possible_actions' => '0', 'number_of_cards' => '1');
$this->allCards[]  = array('name' => 'Display of Power', 'card_style' => 'event', 'level' => '1', 'level_arg' => '10', 'action_cost' => '1', 'possible_actions' => '0', 'number_of_cards' => '1');
$this->allCards[]  = array('name' => 'Escape Route', 'card_style' => 'reaction', 'level' => '1', 'level_arg' => '11', 'action_cost' => '0', 'possible_actions' => '0', 'number_of_cards' => '1');
$this->allCards[]  = array('name' => 'Misinformation', 'card_style' => 'reaction', 'level' => '1', 'level_arg' => '12', 'action_cost' => '0', 'possible_actions' => '0', 'number_of_cards' => '1');

$this->allCards[]  = array('name' => 'Military Base', 'card_style' => 'site', 'level' => '2', 'level_arg' => '1', 'action_cost' => '2', 'possible_actions' => '1', 'number_of_cards' => '2');
$this->allCards[]  = array('name' => 'Library', 'card_style' => 'site', 'level' => '2', 'level_arg' => '2', 'action_cost' => '2', 'possible_actions' => '2', 'number_of_cards' => '2');
$this->allCards[]  = array('name' => 'Hospital', 'card_style' => 'site', 'level' => '2', 'level_arg' => '3', 'action_cost' => '2', 'possible_actions' => '2', 'number_of_cards' => '1');
$this->allCards[]  = array('name' => 'Sniper Nest', 'card_style' => 'site', 'level' => '2', 'level_arg' => '4', 'action_cost' => '1', 'possible_actions' => '2', 'number_of_cards' => '1');
$this->allCards[]  = array('name' => 'Railroad', 'card_style' => 'site', 'level' => '2', 'level_arg' => '5', 'action_cost' => '1', 'possible_actions' => '1', 'number_of_cards' => '1');
$this->allCards[]  = array('name' => 'Fortify', 'card_style' => 'attachment', 'level' => '2', 'level_arg' => '6', 'action_cost' => '1', 'possible_actions' => '0', 'number_of_cards' => '1');
$this->allCards[]  = array('name' => 'Sabotage', 'card_style' => 'attachment', 'level' => '2', 'level_arg' => '7', 'action_cost' => '2', 'possible_actions' => '0', 'number_of_cards' => '1');
$this->allCards[]  = array('name' => 'Conversion', 'card_style' => 'event', 'level' => '2', 'level_arg' => '8', 'action_cost' => '1', 'possible_actions' => '0', 'number_of_cards' => '2');
$this->allCards[]  = array('name' => 'Shelling', 'card_style' => 'event', 'level' => '2', 'level_arg' => '9', 'action_cost' => '3', 'possible_actions' => '0', 'number_of_cards' => '1');
$this->allCards[]  = array('name' => 'Blockade', 'card_style' => 'event', 'level' => '2', 'level_arg' => '10', 'action_cost' => '2', 'possible_actions' => '0', 'number_of_cards' => '1');
$this->allCards[]  = array('name' => 'Loss of Faith', 'card_style' => 'event', 'level' => '2', 'level_arg' => '11', 'action_cost' => '1', 'possible_actions' => '0', 'number_of_cards' => '1');
$this->allCards[]  = array('name' => 'Martyr', 'card_style' => 'reaction', 'level' => '2', 'level_arg' => '12', 'action_cost' => '0', 'possible_actions' => '0', 'number_of_cards' => '1');

$this->allCards[]  = array('name' => 'Missile Silo', 'card_style' => 'site', 'level' => '3', 'level_arg' => '1', 'action_cost' => '1', 'possible_actions' => '2', 'number_of_cards' => '2');
$this->allCards[]  = array('name' => 'Robotics Lab', 'card_style' => 'site', 'level' => '3', 'level_arg' => '2', 'action_cost' => '2', 'possible_actions' => '1', 'number_of_cards' => '2');
$this->allCards[]  = array('name' => 'Radio Station', 'card_style' => 'site', 'level' => '3', 'level_arg' => '3', 'action_cost' => '1', 'possible_actions' => '2', 'number_of_cards' => '1');
$this->allCards[]  = array('name' => 'Computer Network', 'card_style' => 'attachment', 'level' => '3', 'level_arg' => '4', 'action_cost' => '1', 'possible_actions' => '0', 'number_of_cards' => '1');
$this->allCards[]  = array('name' => 'Air Strike', 'card_style' => 'event', 'level' => '3', 'level_arg' => '5', 'action_cost' => '2', 'possible_actions' => '0', 'number_of_cards' => '1');
$this->allCards[]  = array('name' => 'Bioweapon', 'card_style' => 'event', 'level' => '3', 'level_arg' => '6', 'action_cost' => '2', 'possible_actions' => '0', 'number_of_cards' => '1');
$this->allCards[]  = array('name' => 'Archaeology', 'card_style' => 'event', 'level' => '3', 'level_arg' => '7', 'action_cost' => '1', 'possible_actions' => '0', 'number_of_cards' => '1');
$this->allCards[]  = array('name' => 'Ruthless Efficiency', 'card_style' => 'event', 'level' => '3', 'level_arg' => '8', 'action_cost' => '1', 'possible_actions' => '0', 'number_of_cards' => '1');

$this->allCards[]  = array('name' => 'Doomsday Laser', 'card_style' => 'site', 'level' => '4', 'level_arg' => '1', 'action_cost' => '1', 'possible_actions' => '1', 'number_of_cards' => '1');
$this->allCards[]  = array('name' => 'Ark', 'card_style' => 'site', 'level' => '4', 'level_arg' => '2', 'action_cost' => '3', 'possible_actions' => '0', 'number_of_cards' => '1');
$this->allCards[]  = array('name' => 'Force Field', 'card_style' => 'site', 'level' => '4', 'level_arg' => '3', 'action_cost' => '1', 'possible_actions' => '1', 'number_of_cards' => '1');
$this->allCards[]  = array('name' => 'Sentient IA', 'card_style' => 'attachment', 'level' => '4', 'level_arg' => '4', 'action_cost' => '1', 'possible_actions' => '0', 'number_of_cards' => '1');
$this->allCards[]  = array('name' => 'Mind Control', 'card_style' => 'event', 'level' => '4', 'level_arg' => '5', 'action_cost' => '2', 'possible_actions' => '0', 'number_of_cards' => '1');