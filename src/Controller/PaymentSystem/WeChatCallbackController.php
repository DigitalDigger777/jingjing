<?php

namespace App\Controller\PaymentSystem;

use App\Controller\AbstractController;
use App\Entity\Device;
use App\Entity\Schedule;
use App\Entity\Statement;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

/**
 * Class WeChatCallbackController
 * @package App\Controller\PaymentSystem
 */
class WeChatCallbackController extends AbstractController
{

    /**
     * @param Request $request
     * @return Response
     *
     * @Route("/payment/wechat/success", name="jingjing_payment_wechat_success")
     */
    public function success(Request $request)
    {
        $rate = 3; // one hour amount in ￥
        $id = $request->get('id');
        $amount = $request->get('amount');

        if ($id && $amount) {
            //calculate interval in milliseconds
            $interval = ($amount / $rate) * 3600000;

            return $this->addSchedule($id, $interval);
        } else {
            return new JsonResponse([
                'error' => [
                    'code' => '4001',
                    'message' => 'id or amount is not defined'
                ]
            ]);
        }
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @Route("/payment/wechat/error", name="jingjing_payment_wechat_error")
     */
    public function error(Request $request)
    {
        $post = $request->request->all();
        $get = $request->query->all();

        return new JsonResponse([
            'post' => $post,
            'get' => $get
        ]);
    }

    /**
     * @param $id
     * @param $interval
     * @return JsonResponse|Response
     */
    private function addSchedule($id, $interval)
    {

        $em = $this->getDoctrine()->getManager();

        $device = $em->getRepository(Device::class)->findOneBy([
            'id' => $id
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
            $room = $device->getRoom();
            $shopper = $device->getShopper();

            //amount for 1 hour
            $amount = 3;
            $hours = ($interval/60)/60;

            $statement = new Statement();
            $statement->setRoom($room);
            $statement->setShopper($shopper);
            $statement->setAmount($amount * $hours);
            //$statement->setConsumer()
            $statement->setDate(new \DateTime());
            $statement->setHours(1/(60/($interval/60)));
            $statement->setRate('3');

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
}
