<?php


class UserController extends BaseController
{
	public function showAction($uid = 0)
    {
        if(!$uid) $uid = Yaf_Session::getInstance()->get('uid');
        $user = BaseModel::getInstance('User')->find($uid);

        if(!$user->id)
            throw new BaseException(BaseException::USER_NOT_FOUND);

        $this->renderJson([
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email
        ]);
	}

	public function signinAction()
    {
        $request = $this->getRequest();
        $email = $request->getPost('email');
        $passwd = $request->getPost('passwd');

        if(strlen($passwd) < 6) 
            throw new BaseException(BaseException::PASSWORD_TOO_SHORT);

        if(!$email || !filter_var($email, FILTER_VALIDATE_EMAIL))
            throw new BaseException(BaseException::EMAIL_FORMAT_ERROR);

        $user = BaseModel::getInstance('User')->findOneBy(['email' => $email]);

        if(!$user->id) 
            throw new BaseException(BaseException::USER_NOT_FOUND);

        if(!$user->checkPasswd($passwd))
            throw new BaseException(BaseException::PASSWORD_ERROR);

        $this->saveSession($user);
        $this->renderJson();
	}

    public function signupAction()
    {
        $request = $this->getRequest();
        $email = $request->getPost('email');
        $passwd = $request->getPost('passwd');

        if(strlen($passwd) < 6) 
            throw new BaseException(BaseException::PASSWORD_TOO_SHORT);

        if(!$email || !filter_var($email, FILTER_VALIDATE_EMAIL))
            throw new BaseException(BaseException::EMAIL_FORMAT_ERROR);

        $userModel = BaseModel::getInstance('User');
        $user = $userModel->findOneBy(['email' => $email]);

        if($user)
            throw new BaseException(BaseException::EMAIL_IS_USED);

        $user = BaseModel::getInstance('User')->newEntity();
        $user->email = $email;
        $user->name = $email;
        $user->passwd = $passwd;
        $userModel->save($user);

        if(!$user->id)
            throw new BaseException(BaseException::SERVER_ERROR);

        $this->saveSession($user);
        $this->renderJson([
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email
        ]);
    }

    public function signoutAction()
    {
        Yaf_Session::getInstance()->del('uid');
        $remember = Yaf_Registry::get('config')->get('security.remember_me');
        setcookie($remember->key, '', 0, '/');
        $this->renderJson();
    }

    private function saveSession($user, $rememberMe = true)
    {
        Yaf_Session::getInstance()->set('uid', $user->id);

        if($rememberMe)
        {
            $remember = Yaf_Registry::get('config')->get('security.remember_me');
            $time = time();
            $expire = $time + $remember->duration * 24 * 3600;
            setcookie($remember->key, $user->getAuthorizedKey($time), 
                    $expire, '/');
        }
    }
}