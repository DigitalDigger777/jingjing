<?php

namespace App\Controller;

use App\Entity\ShopperUser;
use App\Entity\Statement;
use Doctrine\ORM\Query;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class StatementController extends AbstractController
{
    /**
     * @param Request $request
     * @return JsonResponse
     *
     * @Route("/statement/load/{id}", name="jingjing_statement_load")
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
     * @return JsonResponse
     *
     * @Route("/statement/items", name="jingjing_statement_items")
     */
    public function items(Request $request)
    {
        /**
         * @var \Doctrine\ORM\EntityManager $em
         */
        $shopperId = $this->getRequestParameters($request, 'shopperId');
        $consumerId = $this->getRequestParameters($request, 'consumerId');
        $code = 200;

        $em = $this->getDoctrine()->getManager();
        $qb = $em->createQueryBuilder();
        $qb->select('s, sh.name, DATE_FORMAT(s.date, \'%Y/%m/%d %H:%i\') as date')
            ->from(Statement::class, 's')
            ->join('s.shopper', 'sh')
            ->leftJoin('s.consumer', 'c');

        if ($shopperId) {
            $qb->where($qb->expr()->eq('sh.id', ':shopperId'))
                ->setParameter(':shopperId', $shopperId);
        }

        if ($consumerId) {
            $qb->where($qb->expr()->eq('c.id', ':consumerId'))
                ->setParameter(':consumerId', $consumerId);
        }
        
        $qb->orderBy('c.id', 'DESC');
        $response = $qb->getQuery()
            ->getResult(Query::HYDRATE_ARRAY);

        return new JsonResponse($response, $code);
    }

    /**
     * @param Request $request
     * @return JsonResponse|Response
     *
     * @Route("/statement/delete/{id}", name="jingjing_statement_delete")
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
                    'code' => '1004',
                    'message' => 'Statement is not defined'
                ]
            ];
        } else {
            $response = [
                'message' => 'Statement delete successful'
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
