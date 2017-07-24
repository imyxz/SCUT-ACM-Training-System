<?php
include_once("enum.php");
class acStatus extends enum
{
    public $FAILED=0;
    public $OK=1;
    public $PARTIAL=2;
    public $COMPILATION_ERROR=3;
    public $RUNTIME_ERROR=4;
    public $WRONG_ANSWER=5;
    public $PRESENTATION_ERROR=6;
    public $TIME_LIMIT_EXCEEDED=7;
    public $MEMORY_LIMIT_EXCEEDED=8;
    public $IDLENESS_LIMIT_EXCEEDED=9;
    public $SECURITY_VIOLATED=10;
    public $CRASHED=11;
    public $INPUT_PREPARATION_CRASHED=12;
    public $CHALLENGED=13;
    public $SKIPPED=14;
    public $TESTING=15;
    public $REJECTED=16;
}