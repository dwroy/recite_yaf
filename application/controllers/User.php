<?php


class UserController extends BaseController
{
	public function showAction($uid) 
    {
        $userModel = BaseModel::getInstance('User');
        $user = $userModel->fetch('id='.$uid);

        $this->renderJson($user);
	}
}