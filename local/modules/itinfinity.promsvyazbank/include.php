<?
global $MESS;
$strPath2Lang = str_replace("\\", "/", __FILE__);
$strPath2Lang = substr($strPath2Lang, 0, strlen($strPath2Lang)-strlen("/include.php"));
include(GetLangFileName($strPath2Lang . "/lang/", "/include.php"));

class CITPromsvyazBank{
	private static $paySystemParams;
	private static $arOrder;

	public static function GetPaymentParams( $ID ){
		$arAllowPaySystemFile = array(
			'/bitrix/php_interface/include/sale_payment/itinfinity_payment_promsvyazbank',
			'/local/php_interface/include/sale_payment/itinfinity_payment_promsvyazbank'
		);
		self::$arOrder = CSaleOrder::GetByID( $ID );
		//$arPaySystem = CSalePaySystem::GetByID( self::$arOrder['PAY_SYSTEM_ID'], self::$arOrder['PERSON_TYPE_ID'] ); // не то вохзвращает
        $arPaySystem = CSalePaySystem::GetList(Array("SORT"=>"ASC", "PSA_NAME"=>"ASC"), Array("ID"=>self::$arOrder['PAY_SYSTEM_ID'], "PERSON_TYPE_ID"=>self::$arOrder['PERSON_TYPE_ID']))->Fetch();
		if( in_array($arPaySystem['PSA_ACTION_FILE'], $arAllowPaySystemFile) ){
			$paySystemParams = unserialize( $arPaySystem['PSA_PARAMS'] );
			self::$paySystemParams = $paySystemParams;
			return $paySystemParams;
		}else{
			return false;
		};
	}

	public static function AddLog($string, $array = false ){
		$logDir = $_SERVER['DOCUMENT_ROOT'] . '/local/var/logs/itinfinity.promsvyazbank/';
		$logFile = $logDir .date('d-m-Y') . ".log";
		CheckDirPath( $logDir );
		if( self::$paySystemParams['PAY_DEBUG']['VALUE'] == 'Y' ){
			file_put_contents( $logFile, '[' . date('d-m-Y G:i:s') . '] ' . $string . "\r\n", FILE_APPEND);
			if( $array ){
				file_put_contents( $logFile, "[ARRAY]\r\n" . var_export($array, true) . "\r\n\r\n", FILE_APPEND);
			};
		};
	}

	public static function PSFormatOrderID( $id ){
		if(strlen($id) < 6){
			$n = 6-strlen($id);
			for($i = 0; $i < $n; $i++)
				$id = "0".$id;
		};

		return $id;
	}

	public static function PSOrderDescription(){
		return str_replace("#ID#", self::$arOrder["ID"], trim( self::$paySystemParams["ORDER_DESC"]["VALUE"] ) ) ;
	}

	private static function GetPropertiesGroup(){
		self::AddLog( GetMessage('INFD_LOG_CHKPROPGROUP') . self::$arOrder['PERSON_TYPE_ID']);
		$groupName = GetMessage('INFD_ORDERPROP_GROUPNAME');

		$cdbPropsGroup = CSaleOrderPropsGroup::GetList( array(), array( 'NAME' => $groupName ,'PERSON_TYPE_ID' => self::$arOrder['PERSON_TYPE_ID'] ) );
		if( $cdbPropsGroup->SelectedRowsCount() ){
			$arPropsGroup = $cdbPropsGroup->GetNext();
			self::AddLog( GetMessage('INFD_LOG_CHKPROPGROUPNOTFOUND') . $arPropsGroup['ID'] );
		}else{
			$arGroupFields = array(
				'PERSON_TYPE_ID' => self::$arOrder['PERSON_TYPE_ID'],
				'NAME' => $groupName
			);
			self::AddLog( GetMessage('INFD_LOG_CHKPROPGROUPNOTFOUND_CREATE') . '"' . $groupName . '"');
			$id = CSaleOrderPropsGroup::Add( $arGroupFields );
			$arPropsGroup['ID'] = $id;
			if( $id ){
				self::AddLog( GetMessage('INFD_LOG_CHKPROPGROUP_CREATED') . $id );
			};
		};

		return $arPropsGroup;
	}

	public static function SetProperty( $code, $value ){
		self::AddLog( GetMessage('INFD_LOG_SAVEPROPERTYCODE') . $value );
		$arAvalibleProperties = array(
			'ITIPSB_TIMESTAMP' => array(
				'NAME' => GetMessage('INFD_PROP_ITIPSB_TIMESTAMP_NAME'),
				'DESCRIPTION' => GetMessage('INFD_PROP_ITIPSB_TIMESTAMP_DESCRIPTION')
			),
			'ITIPSB_NONCE' => array(
				'NAME' => GetMessage('INFD_PROP_ITIPSB_NONCE_NAME'),
				'DESCRIPTION' => GetMessage('INFD_PROP_ITIPSB_NONCE_DESCRIPTION')
			),
			'ITIPSB_RRN' => array(
				'NAME' => GetMessage('INFD_PROP_ITIPSB_RRN_NAME'),
				'DESCRIPTION' => GetMessage('INFD_PROP_ITIPSB_RRN_DESCRIPTION')
			),
			'ITIPSB_INTREF' => array(
				'NAME' => GetMessage('INFD_PROP_ITIPSB_INTREF_NAME'),
				'DESCRIPTION' => GetMessage('INFD_PROP_ITIPSB_INTREF_DESCRIPTION')
			),
			'ITIPSB_ORG_AMOUNT' => array(
				'NAME' => GetMessage('INFD_PROP_ITIPSB_ORG_AMOUNT_NAME'),
				'DESCRIPTION' => GetMessage('INFD_PROP_ITIPSB_ORG_AMOUNT_DESCRIPTION')
			),
		);

		if( @is_array( $arAvalibleProperties[$code] ) ){
			$arPropsGroup = self::GetPropertiesGroup();
			$arFilter = array(
				'PROPS_GROUP_ID' => $arPropsGroup['ID'],
				'CODE' => $code
			);
			$cdbPropsList = CSaleOrderProps::GetList( array(), $arFilter );

			if( $cdbPropsList->SelectedRowsCount() ){
				$arPropFields = $cdbPropsList->GetNext();
				$propID = $arPropFields['ID'];
				self::AddLog( GetMessage('INFD_LOG_CHKPROPFOUND') );
			}else{
				self::AddLog( GetMessage('INFD_LOG_CHKPROPNOTFOUND') );
				$arPropFields = array(
					'PERSON_TYPE_ID' => self::$arOrder['PERSON_TYPE_ID'],
					'NAME' => $arAvalibleProperties[$code]['NAME'],
					'TYPE' => 'TEXT',
					'REQUIED' => 'N',
					'DEFAULT_VALUE' => '',
					'USER_PROPS' => 'N',
					'IS_LOCATION' => 'N',
					'PROPS_GROUP_ID' => $arPropsGroup['ID'],
					'DESCRIPTION' => $arAvalibleProperties[$code]['DESCRIPTION'],
					'IS_EMAIL' => 'N',
					'IS_PROFILE_NAME' => 'N',
					'IS_PAYER' => 'N',
					'IS_LOCATION4TAX' => 'N',
					'CODE' => $code,
					'IS_FILTERED' => 'N',
					'IS_ZIP' => 'N',
					'UTIL' => 'Y'
				);

				$propID = CSaleOrderProps::Add( $arPropFields );
				if( $propID ){
					self::AddLog( GetMessage('INFD_LOG_CHKPROPNOTFOUND_CREATED') );
				};
			};



			$arFields = array(
				'ORDER_ID' => self::$arOrder['ID'],
				'ORDER_PROPS_ID' => $propID,
				'NAME' => $arPropFields['NAME'],
				'VALUE' => $value,
				'CODE' => $code
			);

			$cdbPropsValues = CSaleOrderPropsValue::GetList( array(), array( 'ORDER_ID'=>self::$arOrder['ID'], 'CODE' => $code) );

			if( $cdbPropsValues->SelectedRowsCount() ){
				$arPropsValues = $cdbPropsValues->GetNext();
				CSaleOrderPropsValue::Update( $arPropsValues['ID'], $arFields );
				$orderID = self::$arOrder['ID'];
				self::AddLog( GetMessage('INFD_LOG_ORDER_PROP_REWRITE') );
			}else{
				CSaleOrderPropsValue::Add( $arFields );
				self::AddLog( GetMessage('INFD_LOG_ORDER_PROP_WRITE') );
			};

		}else{
			self::AddLog( GetMessage('INFD_LOG_ORDER_PROP_NOTAVAILABLE') );
		};
	}

	public static function GetOrderPropertyValue( $code ){
		$arFilter = array(
			"ORDER_ID" => self::$arOrder['ID'],
			"CODE" => $code
		);

		$cdbPropsValues = CSaleOrderPropsValue::GetList(array(), $arFilter);
		if( $cdbPropsValues->SelectedRowsCount() ){
			$arPropsValues = $cdbPropsValues->GetNext();
			return $arPropsValues['VALUE'];
		}else{
			return false;
		};
	}

	private static function SendRequest( $arFields ){
		self::AddLog( GetMessage('INFD_LOG_SEND_BANKDATA'), $arFields );
		if( strlen( self::$paySystemParams["IS_TEST"]['VALUE'] ) > 0 )
			$server_url = "https://test.3ds.payment.ru/cgi-bin/cgi_link";
		else
			$server_url = "https://3ds.payment.ru/cgi-bin/cgi_link";

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $server_url);
		curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query( $arFields ) );
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_SSLVERSION, 6);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
			'User-Agent: IT-Infinity/PSB Payment module',
			'Accept: */*',
			'Content-Type: application/x-www-form-urlencoded'
		));
		$response = curl_exec($ch);
		curl_close($ch);

		//self::AddLog("����� ������� �����:", $response);
	}

	public static function PayHandler( $ID, $key ){
		if( self::GetPaymentParams($ID) && $key == 'N' ){
			//������ ������
			self::AddLog( GetMessage('INFD_LOG_ORDER_PAYMENTSTATUSCHANGE') . $key );

			$nonce = md5( time() );
			$rrn = self::GetOrderPropertyValue('ITIPSB_RRN');
			$int_ref = self::GetOrderPropertyValue('ITIPSB_INTREF');
			$mac = self::$paySystemParams['MAC']['VALUE'];
			$key = pack( "H*", $mac );
			$arFields = array(
				"ORDER" => self::PSFormatOrderID( self::$arOrder['ID'] ),
				"AMOUNT" => self::$arOrder['PRICE'],
				"CURRENCY" => self::$arOrder['CURRENCY'],
				"ORG_AMOUNT" => self::$arOrder['PRICE'],
				"RRN" => $rrn,
				"INT_REF" => self::GetOrderPropertyValue('ITIPSB_INTREF'),
				"TRTYPE" => "22",
				"TERMINAL" => self::$paySystemParams['TERMINAL']['VALUE'],
				"BACKREF" => self::$paySystemParams['SHOP_RESULT']['VALUE'],
				"EMAIL" => self::$paySystemParams['EMAIL']['VALUE'],
				"TIMESTAMP" => gmdate("YmdHis", time()),
				"NONCE" => $nonce,
			);



			self::AddLog( GetMessage('INFD_LOG_ORDER_PAYMENTSTATUSCHANGE_KEYGEN') . $key );

			self::AddLog( GetMessage('INFD_LOG_ORDER_PAYMENTSTATUSCHANGE_ITIPSB_RNN') . $rrn );
			self::AddLog( GetMessage('INFD_LOG_ORDER_PAYMENTSTATUSCHANGE_ITIPSB_INTREF') . $int_ref );
			self::AddLog( GetMessage('INFD_LOG_ORDER_PAYMENTSTATUSCHANGE_ITIPSB_MAC') . $mac );

			$dataString =	(strlen($arFields['ORDER']) > 0 ? strlen($arFields['ORDER']).$arFields['ORDER'] : "-").
							(strlen($arFields['AMOUNT']) > 0 ? strlen($arFields['AMOUNT']).$arFields['AMOUNT'] : "-").
							(strlen($arFields['CURRENCY']) > 0 ? strlen($arFields['CURRENCY']).$arFields['CURRENCY'] : "-").
							(strlen($arFields['ORG_AMOUNT']) > 0 ? strlen($arFields['ORG_AMOUNT']).$arFields['ORG_AMOUNT'] : "-").
							(strlen($arFields['RRN']) > 0 ? strlen($arFields['RRN']).$arFields['RRN'] : "-").
							(strlen($arFields['INT_REF']) > 0 ? strlen($arFields['INT_REF']).$arFields['INT_REF'] : "-").
							(strlen($arFields['TRTYPE']) > 0 ? strlen($arFields['TRTYPE']).$arFields['TRTYPE'] : "-").
							(strlen($arFields['TERMINAL']) > 0 ? strlen($arFields['TERMINAL']).$arFields['TERMINAL'] : "-").
							(strlen($arFields['BACKREF']) > 0 ? strlen($arFields['BACKREF']).$arFields['BACKREF'] : "-").
							(strlen($arFields['EMAIL']) > 0 ? strlen($arFields['EMAIL']).$arFields['EMAIL'] : "-").
							(strlen($arFields['TIMESTAMP']) > 0 ? strlen($arFields['TIMESTAMP']).$arFields['TIMESTAMP'] : "-").
							(strlen($arFields['NONCE']) > 0 ? strlen($arFields['NONCE']).$arFields['NONCE'] : "-");

			$sign = hash_hmac("sha1", $dataString, $key);
			$arFields['P_SIGN'] = $sign;
			self::AddLog( GetMessage('INFD_LOG_ORDER_PAYMENTSTATUSCHANGE_DATASTRING') . $dataString );

			self::SendRequest( $arFields );
		};
	}

	public static function DeliveryChangeHandler( $ID, $deliverykey ){
		if( self::GetPaymentParams($ID) && $deliverykey == 'Y'){

			//������������� ������

			$nonce = md5( time() );
			$rrn = self::GetOrderPropertyValue('ITIPSB_RRN');
			$int_ref = self::GetOrderPropertyValue('ITIPSB_INTREF');
			$mac = self::$paySystemParams['MAC']['VALUE'];
			$key = pack( "H*", $mac );
			$arFields = array(
				"ORDER" => self::PSFormatOrderID( self::$arOrder['ID'] ),
				"AMOUNT" => self::$arOrder['PRICE'],
				"CURRENCY" => self::$arOrder['CURRENCY'],
				"ORG_AMOUNT" => self::$arOrder['PRICE'],
				"RRN" => $rrn,
				"INT_REF" => $int_ref,
				"TRTYPE" => "21",
				"TERMINAL" => self::$paySystemParams['TERMINAL']['VALUE'],
				"BACKREF" => self::$paySystemParams['SHOP_RESULT']['VALUE'],
				"EMAIL" => self::$paySystemParams['EMAIL']['VALUE'],
				"TIMESTAMP" => gmdate("YmdHis", time()),
				"NONCE" => $nonce,
			);
			self::AddLog( GetMessage('INFD_LOG_ORDER_DELIVERYSTATUSCHANGE_DELIVERYKEY') . $deliverykey );



			self::AddLog( GetMessage('INFD_LOG_ORDER_DELIVERYSTATUSCHANGE_KEYGEN') . $key );

			self::AddLog( GetMessage('INFD_LOG_ORDER_DELIVERYSTATUSCHANGE_ITIPSB_RNN') . $rrn );
			self::AddLog( GetMessage('INFD_LOG_ORDER_DELIVERYSTATUSCHANGE_ITIPSB_INTREF') . $int_ref );
			self::AddLog( GetMessage('INFD_LOG_ORDER_DELIVERYSTATUSCHANGE_ITIPSB_MAC') . $mac );

			$dataString =	(strlen($arFields['ORDER']) > 0 ? strlen($arFields['ORDER']).$arFields['ORDER'] : "-").
							(strlen($arFields['AMOUNT']) > 0 ? strlen($arFields['AMOUNT']).$arFields['AMOUNT'] : "-").
							(strlen($arFields['CURRENCY']) > 0 ? strlen($arFields['CURRENCY']).$arFields['CURRENCY'] : "-").
							(strlen($arFields['ORG_AMOUNT']) > 0 ? strlen($arFields['ORG_AMOUNT']).$arFields['ORG_AMOUNT'] : "-").
							(strlen($arFields['RRN']) > 0 ? strlen($arFields['RRN']).$arFields['RRN'] : "-").
							(strlen($arFields['INT_REF']) > 0 ? strlen($arFields['INT_REF']).$arFields['INT_REF'] : "-").
							(strlen($arFields['TRTYPE']) > 0 ? strlen($arFields['TRTYPE']).$arFields['TRTYPE'] : "-").
							(strlen($arFields['TERMINAL']) > 0 ? strlen($arFields['TERMINAL']).$arFields['TERMINAL'] : "-").
							(strlen($arFields['BACKREF']) > 0 ? strlen($arFields['BACKREF']).$arFields['BACKREF'] : "-").
							(strlen($arFields['EMAIL']) > 0 ? strlen($arFields['EMAIL']).$arFields['EMAIL'] : "-").
							(strlen($arFields['TIMESTAMP']) > 0 ? strlen($arFields['TIMESTAMP']).$arFields['TIMESTAMP'] : "-").
							(strlen($arFields['NONCE']) > 0 ? strlen($arFields['NONCE']).$arFields['NONCE'] : "-");

			$sign = hash_hmac("sha1", $dataString, $key);
			$arFields['P_SIGN'] = $sign;
			self::AddLog( GetMessage('INFD_LOG_ORDER_DELIVERYSTATUSCHANGE_DATASTRING') . $dataString );

			self::SendRequest( $arFields );
		};
	}

	public static function PaymentAuth( $arPost ){
		if( self::GetPaymentParams((int)$arPost['ORDER']) ){
			$arFields = $arPost;
			$mac = self::$paySystemParams['MAC']['VALUE'];
            self::AddLog( 'MAC: ' . $mac );
			$key = pack( "H*", $mac );
			$dataString =	(strlen($arFields['AMOUNT']) > 0 ? strlen($arFields['AMOUNT']).$arFields['AMOUNT'] : "-").
							(strlen($arFields['CURRENCY']) > 0 ? strlen($arFields['CURRENCY']).$arFields['CURRENCY'] : "-").
							(strlen($arFields['ORDER']) > 0 ? strlen($arFields['ORDER']).$arFields['ORDER'] : "-").
							(strlen($arFields['MERCH_NAME']) > 0 ? strlen($arFields['MERCH_NAME']).$arFields['MERCH_NAME'] : "-").
							(strlen($arFields['MERCHANT']) > 0 ? strlen($arFields['MERCHANT']).$arFields['MERCHANT'] : "-").
							(strlen($arFields['TERMINAL']) > 0 ? strlen($arFields['TERMINAL']).$arFields['TERMINAL'] : "-").
							(strlen($arFields['EMAIL']) > 0 ? strlen($arFields['EMAIL']).$arFields['EMAIL'] : "-").
							(strlen($arFields['TRTYPE']) > 0 ? strlen($arFields['TRTYPE']).$arFields['TRTYPE'] : "-").
							(strlen($arFields['TIMESTAMP']) > 0 ? strlen($arFields['TIMESTAMP']).$arFields['TIMESTAMP'] : "-").
							(strlen($arFields['NONCE']) > 0 ? strlen($arFields['NONCE']).$arFields['NONCE'] : "-").
							(strlen($arFields['BACKREF']) > 0 ? strlen($arFields['BACKREF']).$arFields['BACKREF'] : "-").
							(strlen($arFields['RESULT']) > 0 ? strlen($arFields['RESULT']).$arFields['RESULT'] : "-").
							(strlen($arFields['RC']) > 0 ? strlen($arFields['RC']).$arFields['RC'] : "-").
							(strlen($arFields['RCTEXT']) > 0 ? strlen($arFields['RCTEXT']).$arFields['RCTEXT'] : "-").
							(strlen($arFields['AUTHCODE']) > 0 ? strlen($arFields['AUTHCODE']).$arFields['AUTHCODE'] : "-").
							(strlen($arFields['RRN']) > 0 ? strlen($arFields['RRN']).$arFields['RRN'] : "-").
							(strlen($arFields['INT_REF']) > 0 ? strlen($arFields['INT_REF']).$arFields['INT_REF'] : "-");
			$sign = hash_hmac("sha1", $dataString, $key);
            self::AddLog( GetMessage('STRING_FIELDS') . $dataString );
			self::AddLog( GetMessage('INFD_LOG_BANK_CHECKKEY') . $arFields['P_SIGN'] );
			self::AddLog( GetMessage('INFD_LOG_BANK_NEEDCHECKKEY') . $sign );
			return ( strtoupper($arFields['P_SIGN']) == strtoupper($sign) );
		};
	}

}
?>