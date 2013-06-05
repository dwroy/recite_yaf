<?php


class UserController extends BaseController
{
	public function showAction($uid) 
    {
        $userModel = new UserModel;
        $user = $userModel->fetch('id='.$uid);

        $this->renderJson($user);
	}
}