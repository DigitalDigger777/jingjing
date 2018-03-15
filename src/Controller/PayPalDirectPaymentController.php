<?php

namespace App\Controller;

use App\Entity\ShopperUser;
use App\Entity\Statement;
use Doctrine\ORM\Query;
use GuzzleHttp\Client;
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

        $apiSignature       = 'Ajiy6YmBz00sV0oT2S-obuaQ3kehAqwqfy0RgqbW4oTwqj8RSe6bweuA';
        $user               = 'business_api1.xin.jjpanda.com';
        $password           = 'Q2MLF4CYLPBYLRBA';


        $amt = 3.99;
        $creditCardType     = $this->getRequestParameters($request, 'creditCardType');
        $acct               = $this->getRequestParameters($request, 'acct');
        $expDate            = $this->getRequestParameters($request, 'expDate');
        $cvv2               = $this->getRequestParameters($request, 'cvv2');
        $firstName          = $this->getRequestParameters($request, 'firstName');
        $lastName           = $this->getRequestParameters($request, 'lastName');
//        $street             = $this->getRequestParameters($request, 'street');
//        $city               = $this->getRequestParameters($request, 'city');
//        $state              = $this->getRequestParameters($request, 'state');
//        $zip                = $this->getRequestParameters($request, 'zip');
//        $countryCode        = 'US';

        //$em = $this->getDoctrine()->getManager();
        //$shopper = $em->getRepository(ShopperUser::class)->find($id);
        $client = new Client([
            'base_uri' => 'https://api-3t.sandbox.paypal.com/'
        ]);

        $response = $client->request('POST', 'nvp', [
            'form_params' => [
                'VERSION'        => '56.0',
                'SIGNATURE'      => $apiSignature,
                'USER'           => $user,
                'PWD'            => $password,
                'METHOD'         => 'DoDirectPayment',
                'PAYMENTACTION'  => 'Sale',
                'IPADDRESS'      => $_SERVER['REMOTE_ADDR'],
                'AMT'            => $amt,
                'CREDITCARDTYPE' => $creditCardType,
                'ACCT'           => $acct,
                'EXPDATE'        => $expDate,
                'CVV2'           => $cvv2,
                'FIRSTNAME'      => $firstName,
                'LASTNAME'       => $lastName,
//                'STREET'         => $street,
//                'CITY'           => $city,
//                'STATE'          => $state,
//                'ZIP'            => $zip,
//                'COUNTRYCODE'    => $countryCode
            ]
        ]);

        $code = $response->getStatusCode();

        if ($code != 200) {

            $response = [
                'error' => [
                    'code' => '2001',
                    'message' => 'Payment Error'
                ]
            ];

        } else {
            $response = [];
        }

        return new JsonResponse($response, $code);
    }

}
