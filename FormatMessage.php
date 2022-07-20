<?php

namespace FileWork;

use Exception;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Redirect;

class FormatMessage
{
    /**
     * Function to get the error in the correct format
     * 
     * @param array $message
     * @param int @arg
     * 
     * @return string format JSON. Example: [
     *  "success" => "false",
     *  "error" => [
     *      "message" => "test",
     *      "code" => "500" 
     *  ]
     * ] 
     */

    public function getErrorMessage($message = [], $code, $arg)
    {
        switch ($arg) {
            case 384:
                $language = 'ru';
                $format = 'ajax';
                break;
            case 128:
                $language = 'en';
                $format = 'ajax';
                break;
            case 264:
                $language = 'ru';
                $format = 'api';
                break;
            case 8:
                $language = 'en';
                $format = 'api';
                break;
            case 500:
                return Redirect::to('/' . $code)->send();
            default:
                throw new Exception('Incorrect incoming data:' . $arg);
                break;
        }

        $language == 'en' ? $message = $message[0] : $message = $message[1];
        if ($format == 'api') {
            return json_encode([
                "success" => false,
                "error" => [
                    "code" => $code,
                    "message" => $message,
                ]
            ], JSON_UNESCAPED_UNICODE);
        } else return json_encode([
            "success" => false,
            "code" => $code,
            "message" => $message,
        ], JSON_UNESCAPED_UNICODE);

        return false;
    }

    /**
     * Returns an error message in response 
     *
     * @param array $message
     * @param string $code
     * @param int $format
     * @return \Illuminate\Http\Response 
     */

    public function getResponse($message, $code, $format, $headers = [])
    {
        $message = $this->getErrorMessage($message, $code, $format);
        (new Response($message, $code, $headers))->send();
        exit;
    }

    /**
     * A function that checks the input variable and if it is false returns: An internal server error
     * 
     * @param bool $checkVariable
     * @param int $format
     * @return \Illuminate\Http\Response 
     */

    public function getInternalError($checkVariable, $format, $rollback = false)
    {
        if (!$checkVariable) $this->getResponse([
            'Internal server error',
            'Внутренняя ошибка сервера'
        ], 500, $format, $rollback);
    }
}
