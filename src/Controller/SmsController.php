<?php

namespace App\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Twilio\Rest\Client;

/**
 * Class SmsController
 * @package App\Controller
 */
class SmsController extends Controller
{

    /**
     * @Route("/sms/send", name="sms_send")
     */
    public function sendUrlAction(Request $request)
    {
        $sid = $this->getParameter('twillio_sid');
        $token = $this->getParameter('twillio_auth_token');

//        print($sid);
//        print($token);

//        $h = fopen('uploads/twillio.txt', 'a+');
//        fwrite($h, $sid . "\n");
//        fwrite($h, $token . "\n");
//        fwrite($h, print_r($request->request->all(), true));
//        fclose($h);

//        return new Response();
        $client = new Client($sid, $token);
        $client->messages->create(
            $request->request->get('From'),
            [
                'from' => $request->request->get('To'),
                'body' => 'https://xin.jjpanda.com/consumer/buy-time-slots/' . $request->request->get('Body')
            ]
        );

        return new Response();
    }

    /**
     * @Route("/sms/fallback", name="sms_fallback")
     *
     * @param Request $request
     */
    public function fallBackAction(Request $request)
    {
        $h = fopen('uploads/twillio_fall.txt', 'a+');
        fwrite($h, print_r($request->request->all(), true));
        fclose($h);
    }
}
