<?php

namespace App\Controller;

use App\Entity\ShopperUser;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ShopperController extends AbstractController
{
    /**
     * @param Request $request
     * @return JsonResponse
     *
     * @Route("/shopper/load/{id}", name="jingjing_shopper_load")
     */
    public function load(Request $request)
    {
        /**
         * @var \App\Entity\ShopperUser $shopper
         */
        $id = $this->getRequestParameters($request, 'id');

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

    /**
     * @param Request $request
     * @return JsonResponse|Response
     *
     * @Route("/shopper/delete/{id}", name="jingjing_shopper_delete")
     */
    public function delete(Request $request)
    {
        /**
         * @var \App\Entity\ShopperUser $shopper
         */
        $id = $this->getRequestParameters($request, 'id');

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
                'message' => 'Shopper delete successful'
            ];

            $shopper->setIsDeleted(true);

            $em->persist($shopper);
            $em->flush();
        }

        if ($request->getMethod() == 'OPTIONS') {
            return new Response();
        } else {
            return new JsonResponse($response, $code);
        }
    }
}
