<?php
class indexs extends SlimvcController
{
    function IndexAction()
    {
        $this->active=1;
        $this->title='首页';
        $this->isLogin=$this->helper("user_helper")->isLogin();
        $this->view("index");

    }
}