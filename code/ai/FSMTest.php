<?php
// vim: set expandtab cindent tabstop=4 shiftwidth=4 fdm=marker:
 
/**
 * @file     FSMTest.php
 * @version  1.0
 * @author   wade
 * @date     2015-01-24 17:44:31
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

    public function isFirstState() {
        return TRUE;
    }

    public function first() {
        echo sprintf("%d do first step!\n", $this->uid);
    }

    public function isSecondState() {
        return TRUE;
    }

    public function second() {
        echo sprintf("%d do second step!\n", $this->uid);
    }

    public function isThirdState() {
        return TRUE;
    }

    public function third() {
        echo sprintf("%d do third step!\n", $this->uid);
    }
}

class FirstState implements State {
    public function execute(Troll $troll) {
        if ($troll->isFirstState()) {
            $troll->first();
        } else {
            // todo
        }
    }
}

class SecondState implements State {
    public function execute(Troll $troll) {
        if ($troll->isSecondState()) {
            $troll->second();
        }
    }
}

class ThirdState implements State {
    public function execute(Troll $troll) {
        if ($troll->isThirdState()) {
            $troll->third();
        }
    }
}

$uid = 10001;
$troll = new Troll($uid);

$firstState = new FirstState();
$troll->changeState($firstState);
$troll->Update();

$secondState = new SecondState();
$troll->changeState($secondState);
$troll->Update();

$thirdState = new ThirdState();
$troll->changeState($thirdState);
$troll->Update();
