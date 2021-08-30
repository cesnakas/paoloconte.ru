<?

namespace Citfact\InfoBip;

class Proceed
{

    /** @var array $parameters */
    protected $parameters;
    /** @var string $response */
    protected $response;
    /** @var array $strError */
    protected $error;
    /** @var string $operationName */
    protected $operationName;

    const URL = 'https://e68r2.api.infobip.com/sms/';
    const API_KEY = '3623f87a527dd6f5513d3efad2dd8585-12f43084-cbf8-49d2-9e9f-7544951c5ff4';

    const OPERATION_POST_SEND_SMS = '2/text/advanced';
    const OPERATION_GET_SMS_STATUS = '1/logs?';
    const OPERATION_NOTIFY = 'https://paoloconte.ru/local/modules/citfact.tools/lib/infobip/notify.php';   //Адрес скрипта автоматического уведомления (infobip не отчитывается в настоящее время)


    /**
     * @return string
     */
    public function getOperationName()
    {
        return $this->operationName;
    }

    /**
     * @param mixed $operationName
     * @return Operation
     */
    public function setOperationName($operationName)
    {
        $this->operationName = $operationName;
        return $this;
    }

    /**
     * $this constructor.
     * @param array $parameters
     * @param string $operationName
     */
    public function __construct($parameters, $operationName)
    {
        $this->setParameters($parameters);
        $this->setOperationName($operationName);
    }

    /**
     * @return string
     */
    public function getSecretKey()
    {
        return static::API_KEY;
    }


    /**
     * @return null
     */
    public function send()
    {
        if (!is_array($this->getParameters())) {
            return null;
        }

        $header[] = 'Authorization: App ' . $this->getSecretKey();
        if ($this->getOperationName() == Proceed::OPERATION_POST_SEND_SMS) {
            $header[] = 'Content-Type: application/json';
        }
        $header[] = 'Accept: application/json';

        $url = static::URL . $this->getOperationName();

        $postParameters = $this->getParameters();
        $sendType = 'POST';
        if ($this->getOperationName() == Proceed::OPERATION_GET_SMS_STATUS) {
            $sendType = 'GET';
            $strParameters = implode('&', $postParameters);
            $url .= $strParameters;
        }
        $postFields = json_encode($postParameters);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_ENCODING, "");
        curl_setopt($ch, CURLOPT_MAXREDIRS, 10);
        curl_setopt($ch, CURLOPT_TIMEOUT, 0);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $sendType);
        if ($sendType == 'POST') {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
        }
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);

        $response = curl_exec($ch);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        
        //Debug code
//        $response = '{"results":[{"messageId":"smsPaolo-Order-112537-Buyer-20996-test","to":"79033607789","from":"PaoloConte","text":"TEST Заказ N112537 принят и готовится к отправке. Наш телефон +78003337077","sentAt":"2020-06-08T08:31:17.370+0000","doneAt":"2020-06-08T08:31:17.611+0000","smsCount":2,"mccMnc":"25099","price":{"pricePerMessage":1.683333,"currency":"RUB"},"status":{"groupId":3,"groupName":"DELIVERED","id":5,"name":"DELIVERED_TO_HANDSET","description":"Message delivered to handset"},"error":{"groupId":0,"groupName":"OK","id":0,"name":"NO_ERROR","description":"No Error","permanent":false}},{"messageId":"smsPaolo-Order-112537-Buyer-20996","to":"79033607789","from":"PaoloConte","text":"Заказ N112537 принят и готовится к отправке. Наш телефон +78003337077","sentAt":"2020-06-08T08:06:11.630+0000","doneAt":"2020-06-08T08:06:11.854+0000","smsCount":1,"mccMnc":"25099","price":{"pricePerMessage":1.683333,"currency":"RUB"},"status":{"groupId":3,"groupName":"DELIVERED","id":5,"name":"DELIVERED_TO_HANDSET","description":"Message delivered to handset"},"error":{"groupId":0,"groupName":"OK","id":0,"name":"NO_ERROR","description":"No Error","permanent":false}}]}';
//        $response = '{"messages":[{"to":"+71212131313","status":{"groupId":1,"groupName":"PENDING","id":26,"name":"PENDING_ACCEPTED","description":"Message sent to next instance"},"messageId":"smsPaolo-Order-112551-Buyer-310866"}]}';
//        $httpcode = 200;

        $this->setResponse($response);

        $arMessage = array(
            'operation name:' => $this->getOperationName(),
            'response' => $response,
            'http_code' => $httpcode,
        );

        static::log($this->getOperationName() . "\n" . json_encode($arMessage) . "\n");
        return true;
    }


    /**
     * @return array
     */
    public function getParameters()
    {
        return $this->parameters;
    }

    /**
     * @param $parameters
     * @return $this
     */
    public function setParameters($parameters)
    {
        $this->parameters = $parameters;
        return $this;
    }

    /**
     * @return string
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * @param string $response
     * @return $this
     */
    public function setResponse($response)
    {
        $this->response = $response;
        return $this;
    }


    /**
     * @param $str
     */
    public static function log($str)
    {
        file_put_contents($_SERVER['DOCUMENT_ROOT'] . '/local/var/logs/infobip.log', $str . "\n", FILE_APPEND | LOCK_EX);
    }
}