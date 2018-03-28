<?php

namespace App\Controller\PaymentSystem;

use App\Controller\AbstractController;
use App\Entity\Device;
use App\Entity\Schedule;
use App\Entity\Statement;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

/**
 * Class PaymentController
 * @package App\Controller\PaymentSystem
 */
class PaymentController extends AbstractController
{

    /**
     * @param Request $request
     * @return array|JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse
     *
     * @Route("/payment/start/{deviceId}", name="jingjing_payment_start")
     */
    public function start(Request $request)
    {
        $deviceId = $request->get('deviceId');
        $userAgent = $_SERVER['HTTP_USER_AGENT'];
        $paymentSystem = null;

        if (preg_match('/MicroMessenger/', $userAgent)) {
            $paymentSystem = 'WeChat';
            $response = $this->redirect('http://jingjing.fenglinfl.com/consumer/buy-time-slots/wechat/' . $deviceId);
        }

        if (preg_match('/AlipayClient/', $userAgent)) {
            $paymentSystem = 'Alipay';
            $response = $this->redirect('http://jingjing.fenglinfl.com/consumer/buy-time-slots/alipay/' . $deviceId);
        }

        if (!$paymentSystem) {
            $response = [
                'error' => [
                    'code' => 4002,
                    'message' => 'undefined payment system'
                ]
            ];

            $response = new JsonResponse($response);
        }

        return $response;
    }

}
