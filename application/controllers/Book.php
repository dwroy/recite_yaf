<?php

class BookController extends BaseController
{

    public function init()
    {
//        if(!Yaf_Session::getInstance()->get('uid'))
//            throw new BaseException(BaseException::USER_NOT_SIGNIN);
    }

    public function listAction()
    {
        $request = $this->getRequest();
        $page = (int)$request->getQuery('page', 1);
        $pageSize = (int)$request->getQuery('page_size', 20);

        $books = $this->get('Book')->fetchAll(
                null, ['level' => 'asc'], $pageSize, ($page - 1) * $pageSize);

        $this->renderJson($books);
    }
}