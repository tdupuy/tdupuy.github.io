<?php

require_once('match.class.php');
require_once('team.class.php');

class Tournament
{
    private $nbplayers;
    private $nbterrains;

    private $teams;
    private $substitutes;

    // Constructor
    public function __construct($nbplayers,$nbterrains){
        $this->nbplayers = $nbplayers;
        $this->nbterrains = $nbterrains;
    }

    public function set_teams($teams){
        $this->teams = $teams;
    }

    public function get_teams(){
        return $this->teams;
    }

    public function set_substitutes($substitutes){
        $this->substitutes = $substitutes;
    }

    public function get_substitutes(){
        return $this->substitutes;
    }

    /**
     * Créer un array pour le nombre de joueur indiqué
     * 
     * @param mixed $nbplayers
     * 
     * @return Array pour le nombre de joueur indiqué
     */
    private function generate_players_arr($nbplayers){
        for($i = 1; $i <= $nbplayers; $i++){
            $players[$i] = $i;
        }
        return $players;
    }


    /**
     * Créer les matches pour le nombre de joueur indiqué
     * 
     * @param mixed $nbplayers
     * 
     * @return Array les équipes (nombre de pair unique pour le nombre de joueur indiqué)
     */
    public function generate_teams($nbplayers){
        $team = new Team();
        $players = $this->generate_players_arr($nbplayers);
        $teams = $team->generate_teams($players);
        return $teams;
    }

    public function generate_turn($index_turn, $old_tournament = false, $teams = false){
        if($old_tournament){
            // On retire un car on ajouté les substitutes dans le tableau
            $this->nbterrains = sizeof($old_tournament) - 1;
            $this->nbplayers = ($this->nbterrains * 4) + sizeof($old_tournament['substitutes']);
        }
        if($teams && !empty($teams) && sizeof($teams) > 1){
            $this->teams = $teams;
        }else if(empty($this->teams) && $teams === false){
            $this->teams = $this->generate_teams($this->nbplayers);
        }else{
            $tournament['end'] = true;
            return $tournament;
        }
        $matches = $this->get_matches($this->nbterrains,$this->teams,$this->substitutes);
        foreach($matches as $match){
            $teams_key = array_search($match[0],$this->teams);
            unset($this->teams[$teams_key]);
            $teams_key = array_search($match[1],$this->teams);
            unset($this->teams[$teams_key]);
        }
        $this->substitutes = Match::get_substitutes_players($matches,$this->generate_players_arr($this->nbplayers));
        $tournament['tour_'.$index_turn] = $matches;
        $tournament['tour_'.$index_turn]['substitutes'] = $this->substitutes;
        $tournament['tour_'.$index_turn]['teams'] = $this->teams;
        return $tournament;
    }

    /**
     * Créer les matches
     * 
     * @param mixed $nbterrains
     * @param mixed $teams
     * 
     * @return Array les matchs
     */
    public function get_matches($nbterrains,$teams,$substitutes){
        $match = new Match();
        $matches = $match->get_matches($nbterrains,$teams,$substitutes);
        return $matches;
    }
}
