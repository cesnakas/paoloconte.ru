<?php

namespace helpers;

/**
 * Class ftp
 * Author Nikolay Volkov
 * @package helpers
 */
class ftp {

    var $server,
        $port,
        $login,
        $password,
        $timeout = 30,
        $ftp,
        $upload_dir = 'product_images';

    /**
     * @param $config array
     */
    function __construct($config){
        if(!empty($config['SERVER'])) $this->server = $config['SERVER'];
        if(!empty($config['PORT'])) $this->port = $config['PORT'];
        if(!empty($config['LOGIN'])) $this->login = $config['LOGIN'];
        if(!empty($config['PASSWORD'])) $this->password = $config['PASSWORD'];
        if(!empty($config['TIMEOUT'])) $this->timeout = $config['TIMEOUT'];
    }

    function __destruct() {
        $this->disconnect();
    }

    /**
     * @return bool
     */
    function connect(){
        if(
            !empty($this->server) &&
            !empty($this->port) &&
            !empty($this->timeout) &&
            !empty($this->login) &&
            !empty($this->password)
        ){
            $this->ftp = ftp_connect($this->server, $this->port, $this->timeout) or die("Не удалось установить соединение с $this->server \n");
            return ftp_login($this->ftp, $this->login, $this->password);
        }
        else
            return false;
    }

    function disconnect(){
        ftp_close($this->ftp);
    }

    /**
     * @param string $path
     * @return array
     */
    function list_files($path='.'){
        return ftp_nlist($this->ftp, $path);
    }

    /**
     * @param string $path
     * @return array
     */
    function list_files_detail($path='.'){
        return ftp_rawlist($this->ftp, $path);
    }

    /**
     * @param string $path
     * @return array
     */
    function get_file_structure($path='.'){
        $detail_list = $this->list_files_detail($path);

        $structure = array();

        foreach($detail_list as $key => $item){
            $item_name = substr($item,49);
            $item_path = $path.'/'.$item_name;
            $item_type = substr($item,0,1);
            if($item_type == '-') $item_type = 'FILE';
            elseif($item_type == 'd') $item_type = 'DIR';
            else $item_type = 'UNDEFINED';

            $structure[] = array(
                'NAME' => $item_name,
                'TYPE' => $item_type,
                'SIZE' => trim(substr($item,20,15)),
                'DATE' => trim(substr($item,35,13)),
                'PATH' => $item_path,
                //'STRING' => $item,
                'HASH' => md5($item.$path),
            );
        }
        return $structure;
    }

    function test(){
        $root_list = $this->get_file_structure();

        $i=0;
        foreach ($root_list as $item) {
            if($i>10) continue;

            //создаем корневые директории
            if($item['TYPE'] == 'DIR'){
                if(!file_exists($this->upload_dir.'/'.$item['NAME'])) {
                    mkdir($this->upload_dir.'/'.$item['NAME'], 0777);
                }

                //создаем директории второго уровня
                $root_list2 = $this->get_file_structure($item['PATH']);
                foreach ($root_list2 as $item2) {
                    if($item['TYPE'] == 'DIR') {
                        if (!file_exists($this->upload_dir . '/' . $item2['NAME'])) {
                            mkdir($this->upload_dir.'/'.$item['NAME'].'/'.$item2['NAME'], 0777);
                        }
                        print $this->upload_dir.'/'.$item['NAME'].'/'.$item2['NAME'].'<br>';
                    }
                }
            }
            $i++;
        }
    }

    function download($fileFrom, $fileTo){
//		echo "From-To: ".$fileFrom.' '.$fileTo."\n".'<br />';
        // *** Set the transfer mode
        $asciiArray = array('txt', 'csv');
		$exp = explode('.', $fileFrom);
        $extension = end($exp);
        if (in_array($extension, $asciiArray)) {
            $mode = FTP_ASCII;
        } else {
            $mode = FTP_BINARY;
        }
		ftp_pasv($this->ftp, true);
        if (ftp_get($this->ftp, $fileTo, $fileFrom, $mode, 0)) {
            return true;
        } else {
            return false;
        }
    }

    ///////////////////////////////////////////////////
    function get_tree_structure($path='.'){
        $root_list = $this->get_file_structure($path='.');

        $i=0;
        foreach ($root_list as $key => $list_item) {
            if($i>100) continue;

            if($list_item['TYPE'] == 'DIR'){
                $root_list[$key]['ITEMS'] = $this->get_file_structure($list_item['PATH']);
            }

            $i++;
        }
        return $root_list;
    }

}