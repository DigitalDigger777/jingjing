<?php

namespace App\Controller;

use App\Entity\Device;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class InstallDeviceController
 *
 * @package App\Controller
 */
class InstallDeviceController extends Controller
{
    /**
     * Install device.
     *
     * @param Request $request
     * @return Response
     *
     * @Route("/install/device", name="jingjing_install_device")
     */
    public function install(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $mac = $request->query->get('mac');

        $device = $em->getRepository(Device::class)->findOneBy([
            'mac' => $mac
        ]);

        $code = 200;

        if (!$device) {
            try {
                $device = new Device();
                $device->setShopperId(null);
                $device->setIsEnable(true);
                $device->setMac($mac);
                $device->setName('');
                $device->setRoom('111');
                $device->setStatus(Device::STATUS_TEST_NOT_TESTED);
                $device->setDate(new \DateTime());

                $em->persist($device);
                $em->flush();
                $code = 201;
                $message = 'Save Successful';
            } catch (\Exception $e) {
                $code = 500;
                $message = $e->getMessage();
            }
        } else {
            $message = 'DB have device';
        }

        return new Response($message, $code);
    }
}
