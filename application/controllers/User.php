<?php


class UserController extends BaseController
{
	public function showAction($uid = 0)
    {
        if(!$uid) $uid = Yaf_Session::getInstance()->get('uid');
        $user = $this->get('User')->findOneBy(['id' => $uid]);

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

        $user = $this->get('User')->findOneBy(['email' => $email]);

        if(!$user->id) 
            throw new BaseException(BaseException::USER_NOT_FOUND);

        if(!$user->checkPasswd($passwd))
            throw new BaseException(BaseException::PASSWORD_ERROR);

        Yaf_Session::getInstance()->set('uid', $user->id);
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

        $user = $this->get('User')->findOneBy(['email' => $email]);

        if($user->id)
            throw new BaseException(BaseException::EMAIL_IS_USED);

        $user->email = $email;
        $user->name = $email;
        $user->passwd = $passwd;
        $user->save();

        if($user->id)
        {
            Yaf_Session::getInstance()->set('uid', $user->id);

            $this->renderJson([
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email
            ]);
        }
        else
        {
            throw new BaseException(BaseException::SERVER_ERROR);
        }
    }
}