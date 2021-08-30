<?
namespace Citfact\CloudLoyalty;

use Citfact\Tools;

class Operation {

	/** @var array $parameters */
	protected $parameters;
	/** @var string $response */
	protected $response;
	/** @var array $strError */
	protected $error;
	/** @var string $operationName */
	protected $operationName;

    const URL = 'https://api.cloudloyalty.ru/';
    const API_KEY = 'feeafd0a-39fd-4db4-a30c-8b44b1b3bad9';

    const TEST_URL = 'https://api-test.cloudloyalty.ru/';
    const TEST_API_KEY = 'b61e7709-998b-4d46-8dbb-b9273f151794';

	const OPERATION_NEW_CLIENT = 'new-client';
	const OPERATION_UPDATE_CLIENT = 'update-client';
	const OPERATION_SET_ORDER = 'set-order';
	const OPERATION_GET_BALANCE = 'get-balance';
	const OPERATION_ADJUST_BALANCE = 'adjust-balance';
	const OPERATION_CALCULATE_PURCHASE = 'v2/calculate-purchase';
	const OPERATION_CONFIRM_ORDER = 'confirm-order';
	const OPERATION_CANCEL_ORDER = 'cancel-order';
    const OPERATION_APPLY_RETURN = 'apply-return';
    const OPERATION_GET_HISTORY = 'get-history';


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
	    if (Tools::isDev()) {
            return static::TEST_API_KEY;
        }
		return static::API_KEY;
	}

	public function getUrl()
	{
	    if (Tools::isDev()) {
            return static::TEST_URL;
        }
		return static::URL;
	}


	/**
	 * @return null
	 */
	public function send()
	{
		if (!is_array($this->getParameters())){
			return null;
		}
		$header[] = 'Accept: application/json';
		$header[] = 'Content-Type: application/json';
		$header[] = 'Cache-Control: no-cache';
		$header[] = 'X-Processing-Key: ' . $this->getSecretKey();

		$url = $this->getUrl() . $this->getOperationName();
		/** Отправляем курл только с прода, если нужно отправлять с теста - убрать проверка ниже */
		if(defined('ANALYTICS') && ANALYTICS == 'prod') {
            $postParameters = $this->getParameters();
            if ($this->getOperationName() == static::OPERATION_NEW_CLIENT){
                $postParameters['shop'] =  [
                    'code' => OperationManager::DEFAULT_SHOP_CODE,
                    'name' => OperationManager::DEFAULT_SHOP_NAME
                ];
            }
		    $postFields = json_encode($postParameters);

			$ch = curl_init($url);
			curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
			curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0);
			curl_setopt($ch, CURLOPT_HTTPHEADER, $header);

			$response = curl_exec($ch);

			$this->setResponse($response);
			$arMessage = [
				'request' => $postFields,
				'response' => $response
			];
            if($this->getOperationName() != self::OPERATION_GET_BALANCE) {
			    static::log($this->getOperationName() . "\n" .
                    json_encode($postParameters) . "\n" .
                    json_encode($arMessage) . "\n");
            }
		}
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
        LoyaltyLogger::log($str, 'Operation','/local/var/log/LoyaltyLogger/operation');
    }
}