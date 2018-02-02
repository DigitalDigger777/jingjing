<?php

namespace App\Controller;

use App\Entity\Device;
use App\Entity\Schedule;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ListenerController extends Controller
{

    /**
     * @param Request $request
     * @return Response
     *
     * @Route("/add-schedule", name="jingjing_add_schedule")
     */
    public function addSchedule(Request $request)
    {
        $mac = $request->query->get('mac');
        $interval = $request->query->get('interval');

        $em = $this->getDoctrine()->getManager();

        $device = $em->getRepository(Device::class)->findOneBy([
            'mac' => $mac
        ]);

        if ($device) {
            $timeStart = new \DateTime();
            $timeEnd = clone $timeStart;
            $timeEnd->add(new \DateInterval('PT' . $interval . 'S'));
            $deviceId = $device->getId();

            $schedule = new Schedule();
            $schedule->setDeviceId($deviceId);
            $schedule->setTimeStart($timeStart);
            $schedule->setTimeEnd($timeEnd);

            $em->persist($schedule);
            $em->flush();

            return new Response('add schedule and enable device', 200);
        } else {
            return new Response('device not found', 500);
        }
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @Route("/check-interval", name="jingjing_check_interval")
     */
    public function checkInterval(Request $request)
    {
        /**
         * @var \App\Entity\Schedule $schedule
         */
        $em = $this->getDoctrine()->getManager();
        $mac = $request->query->get('mac');

        $device = $em->getRepository(Device::class)->findOneBy([
            'mac' => $mac
        ]);

        if ($device) {
            $deviceId = $device->getId();

            $schedule = $em->getRepository(Schedule::class)->findOneBy([
                'deviceId' => $deviceId
            ]);

            if ($schedule) {
                $now = new \DateTime();
                $timeEnd = $schedule->getTimeEnd();

                if ($now > $timeEnd) {
                    return new Response('disable device', 204);
                } else {
                    return new Response('continue work ' . $now->format('Y/m/d H:i:s') . ' ' . $timeEnd->format('Y/m/d H:i:s'), 200);
                }
            } else {
                return new Response('Schedule not found', 500);
            }
        } else {
            return new Response('Device not found', 500);
        }

        return new Response('Error', 500);
    }
}
