<?php


class BaseController extends Yaf_Controller_Abstract 
{
    public function get($name)
    {
        return BaseModel::getInstance($name);
    }

    public function renderJson($data = null, $message = 'success', $code = 0)
    {
        $json = json_encode(
                ['message' => $message, 'code' => $code, 'data' => $data], 
                JSON_UNESCAPED_UNICODE);

        $response = $this->getResponse();
        header('Content-Type: application/json');
        $response->setBody($json);
    }
}
