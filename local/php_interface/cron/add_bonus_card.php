<?
$_SERVER['DOCUMENT_ROOT'] = str_replace('/local/php_interface/cron', '', __DIR__);
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");


function kama_parse_csv_file( $file_path, $file_encodings = ['cp1251','UTF-8'], $col_delimiter = '', $row_delimiter = "" ){

    if( ! file_exists($file_path) )
        return false;

    $cont = trim( file_get_contents( $file_path ) );

    $encoded_cont = mb_convert_encoding( $cont, 'UTF-8', mb_detect_encoding($cont, $file_encodings) );

    unset( $cont );

    // определим разделитель
    if( ! $row_delimiter ){
        $row_delimiter = "\r\n";
        if( false === strpos($encoded_cont, "\r\n") )
            $row_delimiter = "\n";
    }

    $lines = explode( $row_delimiter, trim($encoded_cont) );
    $lines = array_filter( $lines );
    $lines = array_map( 'trim', $lines );

    // авто-определим разделитель из двух возможных: ';' или ','.
    if( ! $col_delimiter ){
        $lines10 = array_slice( $lines, 0, 97981 );

        // если в строке нет одного из разделителей, то значит другой точно он...
        foreach( $lines10 as $line ){
            if( ! strpos( $line, ',') ) $col_delimiter = ';';
            if( ! strpos( $line, ';') ) $col_delimiter = ',';

            if( $col_delimiter ) break;
        }

        // если первый способ не дал результатов, то погружаемся в задачу и считаем кол разделителей в каждой строке.
        // где больше одинаковых количеств найденного разделителя, тот и разделитель...
        if( ! $col_delimiter ){
            $delim_counts = array( ';'=>array(), ','=>array() );
            foreach( $lines10 as $line ){
                $delim_counts[','][] = substr_count( $line, ',' );
                $delim_counts[';'][] = substr_count( $line, ';' );
            }

            $delim_counts = array_map( 'array_filter', $delim_counts ); // уберем нули

            // кол-во одинаковых значений массива - это потенциальный разделитель
            $delim_counts = array_map( 'array_count_values', $delim_counts );

            $delim_counts = array_map( 'max', $delim_counts ); // берем только макс. значения вхождений

            if( $delim_counts[';'] === $delim_counts[','] )
                return array('Не удалось определить разделитель колонок.');

            $col_delimiter = array_search( max($delim_counts), $delim_counts );
        }

    }

    $data = [];
    foreach( $lines as $key => $line ){
        $data[] = str_getcsv( $line, $col_delimiter );
        unset( $lines[$key] );
    }

    return $data;
}

$data = kama_parse_csv_file( 'local/php_interface/cron/111.csv' );
//echo '<pre>';
//print_r( $data[1] );
//echo '</pre>';
$filename = $_SERVER["DOCUMENT_ROOT"] . "/local/var/logs/cron/add_bonus_card.log";
/*$user = new CUser;

$data[] = [
    '47400',
    '',
    '6666000044287'
];*/
global $DB;
$i = 0;
file_put_contents($filename, print_r(date('Y.m.d H:i:s'). " Обработано: " . $i . "\n", true), FILE_APPEND);
foreach ($data as $value)
{
    $i++;
    /*$fields = Array(
        "UF_LOYALTY_CARD" => $value[2],
    );*/
    //$user->Update($value[0], $fields);
    //print_r($value[0] . "\n");
    //print_r($value[2] . "\n");
    //file_put_contents($filename, print_r("UPDATE b_uts_user SET UF_LOYALTY_CARD = '" . $value[2] . "' WHERE VALUE_ID = " . $value[0] . ";" . "\n", true), FILE_APPEND);
    //$record = $DB->Query("UPDATE b_uts_user SET UF_LOYALTY_CARD = '6666000044284' WHERE VALUE_ID = 47397;")->Fetch();
    $count = $DB->Query("SELECT COUNT(*) AS CNT FROM b_uts_user WHERE VALUE_ID = " . $value[0] . ";")->Fetch();
    if ($count['CNT'] == 0) {
        $record = $DB->Query("INSERT INTO b_uts_user (VALUE_ID, UF_LOYALTY_CARD) VALUES (" . $value[0] . ", '" . $value[2] . "');")->Fetch();
    } else {
        $record = $DB->Query("UPDATE b_uts_user SET UF_LOYALTY_CARD = '" . $value[2] . "' WHERE VALUE_ID = " . $value[0] . ";")->Fetch();
    }

    if ($i % 1000 == 0) {
        file_put_contents($filename, print_r(date('Y.m.d H:i:s'). " Обработано: " . $i . "\n", true), FILE_APPEND);
        //print_r('Обработано: ' . $i . "\n");
        //break;
    }
}
