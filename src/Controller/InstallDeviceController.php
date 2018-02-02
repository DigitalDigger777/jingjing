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

        if (!$device) {
            $device = new Device();
            $device->setShopperId(null);
            $device->setIsEnable(true);
            $device->setMac($mac);
            $device->setName('');

            $em->persist($device);
            $em->flush();
        }

        return new Response('Save Successful', 200);
    }
}
