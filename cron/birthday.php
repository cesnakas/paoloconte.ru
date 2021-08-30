<?

if( php_sapi_name() != 'cli' ){

    exit;

}



$start_time = microtime( true );

//
// BEGIN Bitrix.
//

$_SERVER['DOCUMENT_ROOT'] = '/home/bitrix/ext_www/paoloconte.ru';

require( $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php');

CModule::IncludeModule('iblock');
CModule::IncludeModule('catalog');
CModule::IncludeModule('sale');

global $DB;

//
// END Bitrix.
//


ini_set('display_errors', 1);


$days_before = 0 * 86400;
$days_after = 10 * 86400;

$ts = time();

list( $d, $m, $y ) = explode( '.', date( 'd.m.Y' ) );

$current_ts = mktime( 0, 0, 0, $m, $d, $y );


$sql = "SELECT ID, EMAIL, PERSONAL_BIRTHDAY, LAST_NAME, NAME, SECOND_NAME FROM b_user WHERE ACTIVE = 'Y' AND PERSONAL_BIRTHDAY IS NOT NULL";


$records = $DB->Query( $sql );

$i = 0;

while( $record = $records->Fetch() ){

    list( $year, $month, $day ) = explode( '-', $record['PERSONAL_BIRTHDAY'] );

    $year = date('Y');

    $birthdate_ts = mktime( 0, 0, 0, $month, $day, $year );

    // Если день рождения в этом году уже прошёл, тогда сформировать таймштамп следующего дня рождения.
    if( $birthdate_ts < $current_ts ){

        $birthdate_ts = mktime( 0, 0, 0, $month, $day, $year + 1 );

    }

    // $birthdate_ts должен быть всегда больше $current_ts
    $diff_ts = $birthdate_ts - $current_ts;

    if( $days_before >= $diff_ts ){

        $fields = array();
        $fields['DISCOUNT_ID'] = 56;
        $fields['TYPE'] = 2; // На один заказ
        //$fields['COUPON'] = Bitrix\Sale\Internals\DiscountCouponTable::generateCoupon(true);
        $fields['COUPON'] = generate_code();
        $fields['ACTIVE'] = 'Y';
        $fields['ACTIVE_FROM'] = date( 'Y-m-d H:i:s', $birthdate_ts - $days_before );
        $fields['ACTIVE_TO'] = date( 'Y-m-d H:i:s', $birthdate_ts + $days_after );
        $fields['USER_ID'] = $record['ID'];
        $fields['DESCRIPTION'] = 'USER_ID=' . $record['ID'];

        $sql = 'SELECT * FROM b_sale_discount_coupon WHERE';
        $sql.= ' USER_ID = ' . $DB->ForSql( $record['ID'] );
        $sql.= ' AND DISCOUNT_ID = ' . $DB->ForSql( $fields['DISCOUNT_ID'] );
        $sql.= ' AND YEAR(ACTIVE_FROM) = "' . $DB->ForSql( date('Y') ) . '"';

        $coupons = $DB->Query( $sql );
        $coupon = $coupons->Fetch();

        //print_R($coupon);

        if( $coupon !== false ){

            continue;

        }


        //$coupon_id = CCatalogDiscountSave::Add( $fields );
        //$coupon_id = CCatalogDiscountCoupon::Add( $fields );


        $sql = 'INSERT INTO b_sale_discount_coupon SET';
        $sql.= ' DISCOUNT_ID = "' . $DB->ForSql( $fields['DISCOUNT_ID'] ) . '"';
        $sql.= ',ACTIVE = "' . $DB->ForSql( $fields['ACTIVE'] ) . '"';
        $sql.= ',ACTIVE_FROM = "' . $DB->ForSql( $fields['ACTIVE_FROM'] ) . '"';
        $sql.= ',ACTIVE_TO = "' . $DB->ForSql( $fields['ACTIVE_TO'] ) . '"';
        $sql.= ',COUPON = "' . $DB->ForSql( $fields['COUPON'] ) . '"';
        $sql.= ',TYPE = "' . $DB->ForSql( $fields['TYPE'] ) . '"';
        $sql.= ',USER_ID = "' . $DB->ForSql( $fields['USER_ID'] ) . '"';
        $sql.= ',DESCRIPTION = "' . $DB->ForSql( $fields['DESCRIPTION'] ) . '"';
        $sql.= ',CREATED_BY = 1';
        $sql.= ',MODIFIED_BY = 1';
        $sql.= ',TIMESTAMP_X = "' . $DB->ForSql( date('Y-m-d H:i:s') ) . '"';
        $sql.= ',DATE_CREATE = "' . $DB->ForSql( date('Y-m-d H:i:s') ) . '"';

        //	echo $sql;

        $DB->Query( $sql );

        $vars = array();

        $arr_name = array();

        if( $record['LAST_NAME'] != '' ){

            $arr_name[] = $record['LAST_NAME'];

        }

        if( $record['NAME'] != '' ){

            $arr_name[] = $record['NAME'];

        }

        if( $record['SECOND_NAME'] != '' ){

            $arr_name[] = $record['SECOND_NAME'];

        }


        if( count( $arr_name ) == 0 ){

            $arr_name[] = 'Дорогой клиент';

        }

        $vars['NAME'] = implode( ' ', $arr_name );
        $vars['COUPON'] = $fields['COUPON'];
        $vars['EMAIL'] = $record['EMAIL'];

        //CEvent::SendImmediate( 'BIRTHDATE_COUPON', 's1', $vars, 'N', 102 );
        CEvent::SendImmediate( 'BIRTHDATE_COUPON', 's1', $vars, 'N', 119 );
        $i++;

    }

}

echo 'Proccessed users: ' . $i;

?>