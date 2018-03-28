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

/**
 * Class DeviceController
 * @package App\Controller
 */
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
            $status = $device->getStatus();
            $statusStr = '';

            switch ($status)
            {
                case Device::STATUS_TEST_NOT_TESTED:
                        $statusStr = 'Not tested';
                    break;
                case Device::STATUS_TEST_PASSED:
                        $statusStr = 'Test Passed';
                    break;
                case Device::STATUS_TEST_TEST_FAILED:
                        $statusStr = 'Test Failed';
                    break;
                case Device::STATUS_TEST_WAIT:
                        $statusStr = 'Test Wait';
                    break;
            }

            $response = [
                'id'            => $device->getId(),
                'name'          => $device->getName(),
                'mac'           => $device->getMac(),
                'is_enabled'    => $device->getIsEnable(),
                'room'          => $device->getRoom(),
                'shopperId'     => $device->getShopperId(),
                'shopperName'   => $device->getShopper() ? $device->getShopper()->getName() : '',
                'date'          => $device->getDate()->format('Y/m/d H:i'),
                'status'        => $statusStr
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
        $qb->select('d, DATE_FORMAT(d.date, \'%Y/%m/%d %H:%i\')')
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

    /**
     * @param Request $request
     * @return JsonResponse
     *
     * @Route("/device/start-test", name="jingjing_device_start_test")
     */
    public function startTest(Request $request)
    {
        /**
         * @var \App\Entity\Device $device
         * @var \Doctrine\ORM\EntityManager $em
         */
        $id = $this->getRequestParameters($request, 'id');
        $code = 200;

        $em = $this->getDoctrine()->getManager();
        if ($device = $em->getRepository(Device::class)->find($id)) {
            $device->setStatus(Device::STATUS_TEST_WAIT);

            $em->persist($device);
            $em->flush();

            $response = [
                'started test'
            ];
        } else {
            $response = [
                'error' => [
                    'code' => '1003',
                    'message' => 'Device is not defined'
                ]
            ];
            $code = 500;
        }

        return new JsonResponse($response, $code);
    }
}
