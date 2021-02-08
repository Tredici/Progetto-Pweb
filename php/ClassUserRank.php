<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ClassUserRank
 *
 * @author marco
 */
class ClassUserRank implements JsonSerializable {
    
    /*
     * Trasforma in JSON
     */
    public function jsonSerialize() // from JsonSerializable
    {
        return [
            "Posizione"             =>  $this->rank,
            "CodiceUtente"          =>  $this->usercode,
            "NomeUtente"            =>  $this->username,
            "Vittorie"              =>  $this->victoriesCount,
            "Sconfitte"             =>  $this->defeatsCount,
            "UmiliazioniInflitte"   =>  $this->inflictedHumiliationsCount,
            "UmiliazioniSubite"     =>  $this->sufferedHumiliationsCount,
            "GoalSegnati"           =>  $this->scoredGoals,
            "GoalSubiti"            =>  $this->sufferedGoals,
            "VittorieVsSconfitte"   =>  $this->winRatio,
            "GoalFattiVsSubiti"     =>  $this->goalRatio,
            "Punteggio"             =>  $this->score
        ];
    }
    
    /*
     * int
     * Codice utente
     */
    private $usercode;
    public function getUsercode() {
        return $this->usercode;
    }
    
    /*
     * string
     * Codice utente
     */
    private $username;
    public function getUsername() {
        return $this->username;
    }
    
    /*
     * int
     * Numero delle partite vinte
     */
    private $victoriesCount;
    public function getVictories() {
        return $this->victoriesCount;
    }
    
    /*
     *  C.`Giocatore`,
        U.`NomeUtente`,
        C.`Vittorie`,
        C.`Sconfitte`,
        C.`Umiliazioni`,
        C.`GoalFatti`,
        C.`GoalSubiti`,
        C.`RapportoVittorieSconfitte`,
        C.`RapportoGoalFattiSubiti`,
        C.`Punteggio`
     */
    /*
     * int
     * Numero delle partite perse
     */
    private $defeatsCount;
    public function getDefeats() {
        return $this->defeatsCount;
    }
    
    /*
     * int
     * Numero delle umiliazioni inflitte
     */
    private $sufferedHumiliationsCount;
    public function getSufferedHumiliations() {
        return $this->sufferedHumiliationsCount;
    }
    
    /*
     * int
     * Numero delle umiliazioni subite
     */
    private $inflictedHumiliationsCount;
    public function getInflictedHumiliations() {
        return $this->inflictedHumiliationsCount;
    }
    
    /*
     * int
     * Numero dei goal fatti
     */
    private $scoredGoals;
    public function getScoredGoals() {
        return $this->scoredGoals;
    }
    
    /*
     * int
     * Numero dei goal subiti
     */
    private $sufferedGoals;
    public function getSufferedGoals() {
        return $this->sufferedGoals;
    }
    
    /*
     * double
     * Rapporto vittorie/sconfitte
     */
    private $winRatio;
    public function getWinRatio() {
        return $this->winRatio;
    }
    
    /*
     * double
     * Numero dei goal subiti
     */
    private $goalRatio;
    public function getGoalRatio() {
        return $this->goalRatio;
    }
    
    /*
     * double
     * Punteggio in classifica
     */
    private $score;
    public function getScore() {
        return $this->score;
    }
    
    /*
     * int
     * Posizione in classifica
     */
    private $rank;
    public function getRank() {
        return $this->rank;
    }
    
    public function __construct(
                $_Posizione,
                $_Giocatore,
                $_NomeUtente,
                $_Vittorie,
                $_Sconfitte,
                $_UmiliazioniInflitte,
                $_UmiliazioniSubite,
                $_GoalFatti,
                $_GoalSubiti,
                $_RapportoVittorieSconfitte,
                $_RapportoGoalFattiSubiti,
                $_Punteggio = NULL
            ) {
        $this->rank = $_Posizione;
        $this->usercode = $_Giocatore;
        $this->username = $_NomeUtente;
        $this->victoriesCount = $_Vittorie;
        $this->defeatsCount = $_Sconfitte;
        $this->inflictedHumiliationsCount = $_UmiliazioniInflitte;
        $this->sufferedHumiliationsCount = $_UmiliazioniSubite;
        $this->scoredGoals = $_GoalFatti;
        $this->sufferedGoals = $_GoalSubiti;
        $this->winRatio = $_RapportoVittorieSconfitte;
        $this->goalRatio = $_RapportoGoalFattiSubiti;
        $this->score = $_Punteggio;
    }
    
}
