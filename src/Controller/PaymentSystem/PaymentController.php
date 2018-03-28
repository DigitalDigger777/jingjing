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
 * Class WeChatCallbackController
 * @package App\Controller\PaymentSystem
 */
class PaymentController extends AbstractController
{

    /**
     * @param Request $request
     * @return Response
     *
     * @Route("/payment/start", name="jingjing_payment_start")
     */
    public function success(Request $request)
    {
        return new JsonResponse([
            'server' => $_SERVER
        ]);
    }

}
