<?php
// vim: set expandtab cindent tabstop=4 shiftwidth=4 fdm=marker:
 
/**
 * @file     RunnerTest.php
 * @version  1.0
 * @author   wade
 * @date     2015-01-24 19:57:53
 */

/**
 * 有限状态机实现demo
 */

interface State {
    public function execute(Troll $troll);
}

class Troll {
    private $uid;
    private $_curState = NULL;

    public function __construct($uid) {
        $this->uid = $uid;
    }

    public function Update() {
        if ($this->_curState instanceof State) {
            $this->_curState->execute($this);
        }
    }

    public function changeState(State $state) {
        $this->_curState = $state;
    }

    public function isPatrolState() {
        return TRUE;
    }

    public function patrol() {
        echo sprintf("%d is patroling!\n", $this->uid);
    }

    public function isAttackState() {
        return TRUE;
    }

    public function attack() {
        echo sprintf("%d is attacking!\n", $this->uid);
    }

    public function isRunawayState() {
        return TRUE;
    }

    public function runaway() {
        echo sprintf("%d is running away!\n", $this->uid);
    }
}

class PatrolState implements State {
    public function execute(Troll $troll) {
        if ($troll->isPatrolState()) {
            $troll->patrol();
        } else {
            // todo
        }
    }
}

class AttackState implements State {
    public function execute(Troll $troll) {
        if ($troll->isAttackState()) {
            $troll->attack();
        } else {
            // todo
        }
    }
}

class RunawayState implements State {
    public function execute(Troll $troll) {
        if ($troll->isRunawayState()) {
            $troll->runaway();
        } else {
            // todo
        }
    }
}

$uid = 10001;
$troll = new Troll($uid);

$PatrolState = new PatrolState();
$troll->changeState($PatrolState);
$troll->Update();

$AttackState = new AttackState();
$troll->changeState($AttackState);
$troll->Update();

$RunawayState = new RunawayState();
$troll->changeState($RunawayState);
$troll->Update();
