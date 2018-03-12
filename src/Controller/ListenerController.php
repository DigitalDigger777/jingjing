<?php

namespace App\Controller;

use App\Entity\Device;
use App\Entity\Schedule;
use App\Entity\Statement;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
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
        /**
         * @var Device $device
         */
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

            $schedule = $em->getRepository(Schedule::class)->findOneBy([
                'deviceId' => $deviceId
            ]);

            if (!$schedule) {
                $schedule = new Schedule();
                $schedule->setDeviceId($deviceId);
            }

            $schedule->setTimeStart($timeStart);
            $schedule->setTimeEnd($timeEnd);

            $em->persist($schedule);
            $em->flush();

            //statement
            $room = $device->getRoom();
            $shopper = $device->getShopper();

            $statement = new Statement();
            $statement->setRoom($room);
            $statement->setShopper($shopper);
            $statement->setAmount('3.99');
            //$statement->setConsumer()
            $statement->setDate(new \DateTime());
            $statement->setHours(1/(60/($interval/60)));
            $statement->setRate('3.99');

            $em->persist($statement);
            $em->flush();

            return new JsonResponse([
                'message'   => 'add schedule and enable device',
                'timeStart' => $timeStart->format('Y/m/d  H:i'),
                'timeEnd'   => $timeEnd->format('Y/m/d  H:i')
            ]);
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
                    $interval = $now->diff($timeEnd);

                    return new JsonResponse([
                        'interval' => $interval->format('%s')
                    ]);
                    //return new Response('continue work ' . $now->format('Y/m/d H:i:s') . ' ' . $timeEnd->format('Y/m/d H:i:s'), 200);
                }
            } else {
                return new Response('Schedule not found', 500);
            }
        } else {
            return new Response('Device not found', 500);
        }

    }
}
