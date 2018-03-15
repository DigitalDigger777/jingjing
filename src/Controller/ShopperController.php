<?php

namespace App\Controller;

use App\Entity\Device;
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
                'cell'      => $shopper->getCell(),
                'email'     => $shopper->getEmail(),
                'rate'      => $shopper->getRate(),
                'devices'   => $shopper->getDevices()
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

    /**
     * @param Request $request
     * @return JsonResponse|Response
     *
     * @Route("/shopper/device/delete/{id}", name="jingjing_shopper_device_delete")
     */
    public function deleteDevice(Request $request)
    {
        /**
         * @var \App\Entity\Device $device
         * @var \App\Entity\ShopperUser $shopper
         */
        $id = $this->getRequestParameters($request, 'id');

        $em = $this->getDoctrine()->getManager();
        $device = $em->getRepository(Device::class)->find($id);
        $code = 200;

        if (!$device) {
            $code = 500;

            $response = [
                'error' => [
                    'code' => '3003',
                    'message' => 'device is not defined'
                ]
            ];
        } else {
            $response = [
                'message' => 'Device delete successful'
            ];

            $shopperId = $device->getShopperId();
            $shopper = $em->getRepository(ShopperUser::class)->find($shopperId);
            $devices = $shopper->getDevices();
            $devices->removeElement($device);

            $shopper->setDevices($devices);

            $device->setShopperId(null);


            $em->persist($device);
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
