<?php

namespace App\Controller;

use App\Entity\ShopperUser;
use App\Entity\Statement;
use Doctrine\ORM\Query;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class PayPalDirectPaymentController extends AbstractController
{
    /**
     * @param Request $request
     * @return JsonResponse
     *
     * @Route("/pay-pal/direct-payment/pay", name="jingjing_paypal_direct_payment")
     */
    public function pay(Request $request)
    {
        /**
         * @var \App\Entity\ShopperUser $shopper
         */

        $apiSignature       = '';
        $user               = '';
        $password           = '';

        $amt = 3.99;
        $creditCardType     = $this->getRequestParameters($request, 'creditCardType');
        $acct               = $this->getRequestParameters($request, 'acct');
        $expDate            = $this->getRequestParameters($request, 'expDate');
        $cvv2               = $this->getRequestParameters($request, 'cvv2');
        $firstName          = $this->getRequestParameters($request, 'firstName');
        $lastName           = $this->getRequestParameters($request, 'lastName');
        $street             = $this->getRequestParameters($request, 'street');
        $city               = $this->getRequestParameters($request, 'city');
        $state              = $this->getRequestParameters($request, 'state');
        $zip                = $this->getRequestParameters($request, 'zip');
        $countryCode        = 'US';

        $em = $this->getDoctrine()->getManager();
        $shopper = $em->getRepository(ShopperUser::class)->find($id);
        $code = 200;

        if (!$shopper) {
            $code = 500;

            $response = [
                'error' => [
                    'code' => '1003',
                    'message' => 'Shopper is not defined'
                ]
            ];
        } else {
            $response = [
                'id'        => $shopper->getId(),
                'name'      => $shopper->getName(),
                'address'   => $shopper->getAddress(),
                'contact'   => $shopper->getContact(),
                'cell'      => $shopper->getCell()
            ];
        }

        return new JsonResponse($response, $code);
    }

}
