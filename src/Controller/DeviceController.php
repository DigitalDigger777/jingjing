<?php

namespace App\Controller;

use App\Entity\Device;
use App\Entity\ShopperUser;
use Doctrine\ORM\Query;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DeviceController extends AbstractController
{
    /**
     * @param Request $request
     * @return JsonResponse
     *
     * @Route("/device/load/{id}", name="jingjing_device_load")
     */
    public function load(Request $request)
    {
        /**
         * @var \App\Entity\Device $device
         * @var \Doctrine\ORM\EntityManager $em
         */
        $id = $this->getRequestParameters($request, 'id');

        $em = $this->getDoctrine()->getManager();
        $device = $em->getRepository(Device::class)->find($id);

        $code = 200;

        if (!$device) {
            $code = 500;

            $response = [
                'error' => [
                    'code' => '1003',
                    'message' => 'Device is not defined'
                ]
            ];
        } else {
            $response = [
                'id'            => $device->getId(),
                'name'          => $device->getName(),
                'mac'           => $device->getMac(),
                'is_enabled'    => $device->getIsEnable(),
                'room'          => $device->getRoom(),
                'shopperId'     => $device->getShopperId(),
                'shopperName'   => $device->getShopper() ? $device->getShopper()->getName() : ''
            ];
        }

        return new JsonResponse($response, $code);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     *
     * @Route("/device/items", name="jingjing_device_items")
     */
    public function items(Request $request)
    {
        /**
         * @var \Doctrine\ORM\EntityManager $em
         */
        $shopperId = $this->getRequestParameters($request, 'shopperId');
        $code = 200;

        $em = $this->getDoctrine()->getManager();
        $qb = $em->createQueryBuilder();
        $qb->select('d as device, DATE_FORMAT(d.dateAdd, \'%Y/%m/%d %H:%i\') as date')
                ->from(Device::class, 'd');

        if ($shopperId) {
            $qb->where($qb->expr()->eq('d.shopperId', ':shopperId'))
                    ->setParameter(':shopperId', $shopperId);
        }

        $response = $qb->getQuery()
                        ->getResult(Query::HYDRATE_ARRAY);

        return new JsonResponse($response, $code);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     *
     * @Route("/device/save", name="jingjing_device_save")
     */
    public function save(Request $request)
    {
        /**
         * @var \Doctrine\ORM\EntityManager $em
         */
        $id         = $this->getRequestParameters($request, 'id');
        $name       = $this->getRequestParameters($request, 'name');
        $mac        = $this->getRequestParameters($request, 'mac');
        $name       = $this->getRequestParameters($request, 'name');
        $shopperId  = $this->getRequestParameters($request, 'shopperId');
        $isEnabled  = $this->getRequestParameters($request, 'is_enabled');

        if ($request->getMethod() == 'OPTIONS') {
            return new Response('');
        }

        $code = 200;

        $em = $this->getDoctrine()->getManager();
        $response = [];

        if ($id) {
            $device = $em->getRepository(Device::class)->find($id);
        } else {
            $device = new Device();
        }

        if ($name) {
            $device->setName($name);
        }

        if ($mac) {
            $device->setMac($mac);
        }

        if ($shopperId) {
            $shopper = $em->getRepository(ShopperUser::class)->find($shopperId);
            $device->setShopperId($shopperId);
            $device->setShopper($shopper);
        }

        if ($isEnabled || $isEnabled === false || $isEnabled === 0) {
            $device->setIsEnable($isEnabled);
        }

        $em->persist($device);
        $em->flush();

        $response = [
            'message' => 'Save Device Successful'
        ];

        return new JsonResponse($response, $code);
    }
}
