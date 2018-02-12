<?php
/**
 * Created by PhpStorm.
 * User: korman
 * Date: 09.02.18
 * Time: 16:14
 */

namespace App\Controller;

use App\Entity\AdminUser;
use App\Entity\ConsumerUser;
use App\Entity\ShopperUser;
use App\Entity\User;
use Doctrine\ORM\Query;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class UserController
 *
 * @package App\Controller
 */
class UserController extends AbstractController
{

    /**
     * @param Request $request
     * @return JsonResponse
     *
     * @Route("/user/login", name="jingjing_user_login")
     */
    public function login(Request $request)
    {
        $email      = $this->getRequestParameters($request, 'email');
        $password   = $this->getRequestParameters($request, 'password');

        $method = $request->getMethod();
        $response = null;

        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository(User::class)->findOneBy([
            'email'     => $email,
            'password'  => md5($password)
        ]);

        if ($user) {
            $data = [
                'id'   => $user->getId(),
                'role' => $user->getRole(),
                'token' => $user->getToken()
            ];
            $code = 200;
        } else {
            $data = [
                'error' => [
                    'code'      => '1000',
                    'message'   => 'User not found'
                ]
            ];
            $code = 500;
        }

        if ($method == 'OPTIONS') {

            $response = new Response();

        } else {

            $response = new JsonResponse($data, $code);

        }

        return $response;
    }

    /**
     * @param Request $request
     * @return JsonResponse
     *
     * @Route("/user/save", name="jingjing_user_save")
     */
    public function save(Request $request)
    {
        $role = $this->getRequestParameters($request, 'role');

        switch ($role)
        {
            case 'ROLE_ADMIN':
                    $response = $this->saveAdmin($request);
                break;
            case 'ROLE_CONSUMER':
                    $response = $this->saveConsumer($request);
                break;
            case 'ROLE_SHOPPER':
                    $response = $this->saveShopper($request);
                break;
            default:
                    $response = new JsonResponse([
                        'error' => [
                            'code' => '1001',
                            'message' => 'Role is not defined'
                        ]
                    ], 500);
                break;
        }

        if ($request->getMethod() == 'OPTIONS') {
            $response = new Response();
        }

        return $response;
    }

    /**
     * @param Request $request
     * @return JsonResponse
     *
     * @Route("/user/load", name="jingjing_user_load")
     */
    public function load(Request $request)
    {
        /**
         * @var \Doctrine\ORM\EntityManager $em
         */
        $token = $this->getRequestParameters($request, 'token');

        $em = $this->getDoctrine()->getManager();
        $qb = $em->createQueryBuilder();

        $user = $qb->select('u')
                    ->from(User::class, 'u')
                        ->where($qb->expr()->eq('u.token', ':token'))
                        ->setParameter(':token', $token)
                        ->getQuery()
                        ->getSingleResult(Query::HYDRATE_ARRAY);

        return new JsonResponse($user);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     *
     * @Route("/user/items", name="jingjing_user_items")
     */
    public function items(Request $request)
    {
        /**
         * @var \Doctrine\ORM\EntityManager $em
         */
        $role = $this->getRequestParameters($request, 'role');
        $code = 200;

        switch ($role)
        {
            case 'ROLE_ADMIN':
                    $class = AdminUser::class;
                break;
            case 'ROLE_CONSUMER':
                    $class = ConsumerUser::class;
                break;
            case 'ROLE_SHOPPER':
                    $class = ShopperUser::class;
                break;
        }

        if (isset($class)) {
            $em = $this->getDoctrine()->getManager();
            $qb = $em->createQueryBuilder();
            $items = $qb->select('u')
                        ->from($class, 'u')
                        ->where(
                            $qb->expr()->orX(
                                $qb->expr()->isNull('u.isDeleted'),
                                $qb->expr()->eq('u.isDeleted', ':is_deleted')
                            )
                        )
                        ->setParameter(':is_deleted', false)
                        ->getQuery()
                        ->getResult(Query::HYDRATE_ARRAY);

            $response = $items;
        } else {
            $response = [
                'error' => [
                    'code' => '1001',
                    'message' => 'Role is not defined'
                ]
            ];
            $code = 500;
        }


        return new JsonResponse($response, $code);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    private function saveAdmin(Request $request)
    {
        $id       = $this->getRequestParameters($request, 'id');
        $email    = $this->getRequestParameters($request, 'email');
        $password = $this->getRequestParameters($request, 'password');

        $em = $this->getDoctrine()->getManager();
        $data = [];
        $code = 200;

        if ($id) {
            $user = $em->getRepository(AdminUser::class)->find($id);

            if (!$user) {
                $data = [
                    'error' => [
                        'code' => '1000',
                        'message' => 'user with #' . $id . ' not found'
                    ]
                ];
                $code = 500;
            }
        } else {
            $user = new AdminUser();
            $user->setEmail($email);
            $user->setRole(null);
        }

        if ($password) {
            $user->setPassword(md5($password));
            $user->setToken(hash('sha256', $password));
        }

        if (!isset($data['error'])) {
            $em->persist($user);
            $em->flush();

            $data = [
                'message' => 'Admin save successful'
            ];
        }

        return new JsonResponse($data, $code);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    private function saveConsumer(Request $request)
    {
        $id       = $this->getRequestParameters($request, 'id');
        $email    = $this->getRequestParameters($request, 'email');
        $password = $this->getRequestParameters($request, 'password');

        $em = $this->getDoctrine()->getManager();
        $data = [];
        $code = 200;

        if ($id) {
            $user = $em->getRepository(ConsumerUser::class)->find($id);

            if (!$user) {
                $data = [
                    'error' => [
                        'code' => '1000',
                        'message' => 'user with #' . $id . ' not found'
                    ]
                ];
                $code = 500;
            }
        } else {
            $user = new ConsumerUser();
            $user->setEmail($email);
            $user->setRole(null);
        }

        if ($password) {
            $user->setPassword(md5($password));
            $user->setToken(hash('sha256', $password));
        }

        if (!isset($data['error'])) {
            $em->persist($user);
            $em->flush();

            $data = [
                'message' => 'Consumer save successful'
            ];
        }

        return new JsonResponse($data, $code);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    private function saveShopper(Request $request)
    {
        $id       = $this->getRequestParameters($request, 'id');
        $email    = $this->getRequestParameters($request, 'email');
        $password = $this->getRequestParameters($request, 'password');

        $name       = $this->getRequestParameters($request, 'name');
        $address    = $this->getRequestParameters($request, 'address');
        $contact    = $this->getRequestParameters($request, 'contact');
        $cell       = $this->getRequestParameters($request, 'cell');


        $em = $this->getDoctrine()->getManager();
        $data = [];
        $code = 200;

        if ($id) {
            $user = $em->getRepository(ShopperUser::class)->find($id);

            if (!$user) {
                $data = [
                    'error' => [
                        'code' => '1000',
                        'message' => 'user with #' . $id . ' not found'
                    ]
                ];
                $code = 500;
            }
        } else {
            $user = new ShopperUser();
            $user->setEmail($email);
            $user->setRole(null);
        }

        if ($password && !empty($password)) {
            $user->setPassword(md5($password));
            $user->setToken(hash('sha256', $password));
        }

        if ($name) {
            $user->setName($name);
        }

        if ($address) {
            $user->setAddress($address);
        }

        if ($contact) {
            $user->setContact($contact);
        }

        if ($cell) {
            $user->setCell($cell);
        }

        if (!isset($data['error'])) {
            $em->persist($user);
            $em->flush();

            $data = [
                'message' => 'Shopper save successful'
            ];
        }

        return new JsonResponse($data, $code);
    }
}
