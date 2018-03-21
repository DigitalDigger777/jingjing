<?php

namespace App\Controller\PaymentSystem;

use App\Controller\AbstractController;
use App\Entity\Device;
use App\Entity\ShopperUser;
use Doctrine\ORM\Query;
use GuzzleHttp\Client;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Yansongda\Pay\Gateways\Alipay;
use Yansongda\Pay\Gateways\Wechat\Support;
use Yansongda\Pay\Log;
use Yansongda\Pay\Pay;
use Yansongda\Supports\Collection;
use Yansongda\Supports\Str;

class WeChatController extends AbstractController
{
    protected $config = [
        'appid'           => 'wx0c25d154feebb196', // APP APPID
        'app_id'          => 'wx0c25d154feebb196', // 公众号 APPID
        'miniapp_id'      => 'wx0c25d154feebb196', // 小程序 APPID
        'mch_id'          => '1225312702',
        'key'             => 'FEAA9837085EC53680F04AE0808A212B',
        'sandbox_signkey' => '7304453353771B330AAD23BB6A667687',
        'notify_url'    => 'http://yanda.net.cn/notify.php',
        'cert_client' => './cert/apiclient_cert.pem', // optional，退款等情况时用到
        'cert_key' => './cert/apiclient_key.pem',// optional，退款等情况时用到
//        'log' => [ // optional
//            'file' => './logs/wechat.log',
//            'level' => 'debug'
//        ],
        'mode' => 'dev', // optional, dev/hk;当为 `hk` 时，为香港 gateway。
    ];

    /**
     * @param Request $request
     * @return JsonResponse
     *
     * @Route("/payment/wechat/send", name="jingjing_payment_wechat_send")
     */
    public function index(Request $request)
    {
        $order = [
            'out_trade_no'  => time(),
            'total_fee'     => '1', // **单位：分**
            'body'          => 'test body - 测试',
            'openid'        => 'onkVf1FjWS5SBIixxxxxxx',
        ];

//        $this->config['key'] = $this->config['sandbox_signkey'];
        //print_r($this->config);

        $this->preOrder();
        //$pay = Pay::wechat($this->config)->mp($order);

        // $pay->appId
        // $pay->timeStamp
        // $pay->nonceStr
        // $pay->package
        // $pay->signType
    }

    /**
     * @param Request $request
     * @return JsonResponse
     *
     * @Route("/payment/wechat/notify", name="jingjing_payment_wechat_notify")
     */
    public function notify(Request $request)
    {
        $pay = Pay::wechat($this->config);

        try{
            $data = $pay->verify(); // 是的，验签就这么简单！

            Log::debug('Wechat notify', $data->all());
        } catch (\Exception $e) {
            // $e->getMessage();
        }

        return $pay->success()->send();// laravel 框架中请直接 `return $pay->success()`
    }

    /**
     * @param Request $request
     * @return JsonResponse
     *
     * @Route("/payment/wechat/getsignkey", name="jingjing_payment_wechat_getsignkey")
     */
    public function getsignkey(Request $request)
    {
        /**
         * @var Collection $resp
         */
        //generate signature
        $data = [
            'mch_id'    => $this->config['mch_id'],
            'nonce_str' => Str::random()
        ];

        $signature = $this->getSignature($data);

//        $signature = Support::generateSign($data, $this->config['key']);
//
        $data['sign'] = $signature;

//        echo '<pre>';
        echo $this->getSandboxSignKey($data);
        exit;
//        $resp = Support::requestApi(
//            'sandboxnew/pay/getsignkey',
//            $data,
//            $this->config['key'],
//            $this->config['cert_client'],
//            $this->config['cert_key']
//        );
//        $signkey = $resp->get('sandbox_signkey');
//        //echo '<pre>';
//        //print_r($data);
//        //print_r($resp);
//        $signature = strtoupper($signkey);
////        print_r(Support::fromXml($resp));
//        echo $signature;
//        exit;
    }



    /**
     * Create signature
     *
     * @param $data
     * @return string
     */
    private function getSignature($data)
    {
        ksort($data);
        return strtoupper(md5(http_build_query($data)));
    }

    private function toXml($data): string
    {
        $xml = '<xml>';
        foreach ($data as $key => $val) {
            $xml .= is_numeric($val) ? '<'.$key.'>'.$val.'</'.$key.'>' :
                '<'.$key.'><![CDATA['.$val.']]></'.$key.'>';
        }
        $xml .= '</xml>';

        return $xml;
    }

    public function fromXml($xml): array
    {
        libxml_disable_entity_loader(true);

        return json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA), JSON_UNESCAPED_UNICODE), true);
    }

    private function getSandboxSignKey($config)
    {
        $client = new Client();

        $response = $client->request('POST', 'https://api.mch.weixin.qq.com/sandboxnew/pay/getsignkey', [
            'headers' => [
                'Content-Type' => 'text/xml; charset=UTF8'
            ],
            'body' => $this->toXml([
                'mch_id'    => $config['mch_id'],
                'nonce_str' => $config['nonce_str'],
                'sign'      => $config['sign']
            ])
        ]);

        //print_r($config);

        //echo $response->getStatusCode();
        if ($response->getStatusCode() == 200) {
            return strtoupper($this->fromXml($response->getBody()->getContents())['sandbox_signkey']);
        } else {
            throw new \Exception('Status code: ' . $response->getStatusCode());
        }

    }

    private function preOrder()
    {
        $payload = [
            'appid'             => 'wx11912106637f6d34',
            'mch_id'            => '1499026472',
            'nonce_str'         => 'XvOVjONj1ml0ODjn',
            'notify_url'        => 'http://yanda.net.cn/notify.php',
            'trade_type'        => 'JSAPI',
            'spbill_create_ip'  => $_SERVER['REMOTE_ADDR'],
            'out_trade_no'      => time(),
            'total_fee'         => '1',
            'body'              => 'test body - 测试',
            'openid'            => 'onkVf1FjWS5SBIixxxxxxx'
        ];

        $payload['sign'] = $this->getSignature($payload);

        $client = new Client();

        $response = $client->request('POST', 'https://api.mch.weixin.qq.com/pay/unifiedorder', [
            'headers' => [
                'Content-Type' => 'text/xml; charset=UTF8'
            ],
            'body' => $this->toXml($payload)
        ]);

        //Log::debug('Pre Order:', [$endpoint, $payload]);
        if ($response->getStatusCode() == 200) {
            $res = $this->fromXml($response->getBody()->getContents());
            print_r($res);
            exit;
            //return strtoupper($this->fromXml($response->getBody()->getContents())['sandbox_signkey']);
        } else {
            throw new \Exception('Status code: ' . $response->getStatusCode());
        }
        echo '<pre>';
        echo $endpoint;
        print_r($payload); exit;
        return Support::requestApi($endpoint, $payload, $this->config->get('key'));
    }
}