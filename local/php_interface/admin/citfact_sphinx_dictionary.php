<?php

use Citfact\Tools;

require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_before.php");
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_after.php");

if (Tools::isDev()) {
    $sphinxPatch = '/home/dev/paoloconte/projects/paoloconte/paoloconte.sphinx/data/userdefined/';
} else {
    $sphinxPatch = '/home/bitrix/sphinx/data/userdefined/';
}

if (isset($_REQUEST['restart']) && $_REQUEST['restart'] == 'y') {
    if (
    !is_file($sphinxPatch . "restart")
    ) {
        $fp = fopen($sphinxPatch . "restart", "w");
        fwrite($fp, 1);
        fclose($fp);
    }
    sleep(60);
    $fp = fopen($sphinxPatch . "restarted", "w");
    fwrite($fp, 1);
    fclose($fp);
    header("Location: /bitrix/admin/citfact_sphinx_dictionary.php");
}

class SphinxHelper
{
    public $index_name;
    public $need_rotate;
    public $config = array(
        'exceptions' => array(
            'delemiter' => '=>',
            'num_unit' => 2,
            'rules' => '
                Формат записи: <br/>
                <ul>
                    <li>банка > powerbank
                    <li>повер банк > powerbank
                    <li>зарядник > powerbank
                    <li>резинка стирательная > ластик
                    <li>резинка для стирания > ластик
                </ul>    
                Важно! <br/> 
                если синоним также присутствует в словаре словоформ, как синоним он будет проигнорирован.
              
                Все токены здесь чувствительны к регистру: они не будут обрабатываться правилами.<br/> 
                Таким образом, в приведенном выше примере файла исключений текст «банка» будет маркироваться как «powerbank». <br/>
                <ul>
                    <li>всегда интерпретируется как одно слово</li>
                    <li>учитывает регистр и пробел!</li>
                </ul>
                В примере запрос «Резинка стирательная» не будет сопоставлять документ с текстом «резинка стирательная».<br/>
                <br/>
                Пробелы в списке токенов в левой части имеют значение, но их количество не имеет значения. <br/>
                Например, маркер «резинка      стирательная» в левой части будет соответствовать тексту «резника стирательная» независимо от количества пробелов как в левой части, так и в индексированном тексте. <br/>
                Поэтому такой текст будет проиндексирован как специальное ключевое слово «резника стирательная».<br/>
                <br/>
                Исключения применяются к необработанным входящим документам и данным запросов во время индексации и поиска соответственно. <br/>
                Поэтому, чтобы применить изменения в файле, необходимо переиндексировать поиск<br/>
                Важная особенность работы: в правой части должно быть одно слово в нижнем регистре, если необходимо приводить синонимы к произвольной фразе - необходимо использовать словарь словоформ
                ',
        ),
        'stopwords' => array(
            'delemiter' => '',
            'num_unit' => 1,
            'rules' => '
                Формат записи: <br/>
                слово <br/> 
                второеслово<br/>
                Важно! <br/> 
                указанные слова будут полностью проигнорированы при поиске',
        ),
        'wordforms' => array(
            'delemiter' => '>',
            'num_unit' => 2,
            'rules' => '
                Формат записи: <br/>
                <ul>
                    <li>фортепианинка > фортеп </li> 
                    <li>конфетище > конфет </li>
                </ul>
                Данный словарь используется для словообразования, если по каким-либо причинам определенная форма слова
                не попадает в поисковый индекс и для добавления фраз с синонимичными значениями<br/>
                Важно! <br/> 
                если словоформа также присутствует в словаре синонимов, как синоним она будет проигнорирована.<br/>
                <ul>
                    <li>девушек > девушк</li>
                    <li>девушку > девушк</li>
                    <li>девчушечка > девушк # комментарий, будет проигнорирован</li>
                    <li>~дева > девушк</li>
                </ul>
                Вы можете преобразовать несколько исходных слов в одно целевое слово. <br/>
                Поскольку работа происходит с нормальными формами слов, а не с исходным текстом, различия в пробелах и разметке игнорируются.<br/>
                <br/>
                Допускаются комментарии (начинающиеся с «#»<br/>
                Наконец, если строка начинается с тильды («~»), словоформа будет применяться после морфологии, а не до).<br/>
                <ul>
                    <li>core 2 duo > c2d</ul>
                    <li>e6600 > c2d</ul>
                    <li>core 2duo => c2d</ul>
                </ul>
                Вы можете указать несколько токенов назначения:<br/>
                <ul>
                    <li>s02e02 > сезон 2 серия 2</li>
                    <li>s3 e3 > сезон 3 серия 3</li>
                    <li>соня > ручка роллер parker sonnet</li>
                    <li>Нло > Папка-вкладыш</li>
                </ul>
                В случае поиска "соня" будут найдены все товары, в которых встречаются все перечисленные справа слова<br/>
                Данный словать регистронезависим, все папки-вкладыши будут найдены как при запросе "Нло", так и "нло" и "НЛО"<br/>',
        )
    );

    public $errors = array();

    public $sphinxPatch;
    public $dirPath;

    public function __construct()
    {
        $this->index_name = \COption::GetOptionString('search', 'sphinx_index_name');

        if (Tools::isDev()) {
            $this->sphinxPatch = '/home/dev/paoloconte/projects/paoloconte/paoloconte.sphinx/data/userdefined/';
        } else {
            $this->sphinxPatch = '/home/bitrix/sphinx/data/userdefined/';
        }
        $this->dirPath = $this->sphinxPatch;

        $this->need_rotate = is_file($this->sphinxPatch . "sphinx_need_rotate");
    }

    public function getConfigPath($file)
    {
        if (!$file) {
            return '';
        }
        return $this->dirPath . $this->index_name . '.' . $file . '.txt';
    }

    public function getConfigData($file)
    {
        if (!$file || array_key_exists($file, $this->config) === false) {
            return '';
        }

        $result = [];
        $configPath = $this->getConfigPath($file);
        if (is_file($configPath) && is_readable($configPath)) {
            $result = file_get_contents($configPath);

            if (empty($result) || $result === false) {
                return '';
            }

            if ($this->config[$file]['num_unit'] == 2) {
                $tmp_res = explode(PHP_EOL, $result);
            } else {
                $tmp_res = explode(' ', $result);
            }
            $result = [];
            foreach ($tmp_res as $str) {
                if ($this->config[$file]['num_unit'] == 2) {
                    $tmp_str = explode($this->config[$file]["delemiter"], $str);
                    $result[] = $tmp_str[0] . " > " . $tmp_str[1];
                } else {
                    $result[] = trim($str);
                }
            }
        }

        return implode(PHP_EOL, $result);
    }

    public function saveConfig($file)
    {
        $request = '';

        if (isset($_REQUEST['sphinx_' . $file]) && !empty($_REQUEST['sphinx_' . $file])) {
            $request = $_REQUEST['sphinx_' . $file];
        }

        $data = $this->getConfigEncodeData($file, $request);

        if ($data) {
            file_put_contents($this->getConfigPath($file), $data);
        }
    }

    public function saveAllConfig()
    {
        foreach ($this->config as $key => $file) {
            $this->saveConfig($key);
        }
    }

    public function getConfigDecodeData($file)
    {
        return $this->getConfigData($file);
    }

    public function getConfigEncodeData($file, $data)
    {
        $n = 1;

        foreach (explode(PHP_EOL, $data) as $str) {
            if (!trim($str)) {
                continue;
            }
            if ($this->config[$file]['num_unit'] == 2) {
                if (strpos($str, '>') === false) {
                    $this->errors[$file][] = 'В строке ' . $n . ' не найден разделитель " > "';
                    continue;
                }

                $tmp_str = explode('>', $str);
                if (count($tmp_str) != 2) {
                    $this->errors[$file][] = 'Ошибка в строке ' . $n . ' "' . $str . '"';
                    continue;
                } else {
                    $data_tmp[] = trim($tmp_str[0]) . " " . $this->config[$file]['delemiter'] . " " . trim($tmp_str[1]);
                }
            } else {
                $data_tmp[] = trim($str);
            }
            $n++;
        }
        if ($this->config[$file]['num_unit'] == 2) {
            $data = implode(PHP_EOL, $data_tmp);
        } else {
            $data = implode(" ", $data_tmp);
        }

        return $data;
    }

    public function showErrors($file)
    {
        $result = '';
        if (!empty($this->errors[$file])) {
            $result = '<div class="adm-info-message-wrap adm-info-message-red">
            <div class="adm-info-message">
                <div class="adm-info-message-title">Ошибка</div>
                    ' . implode('<br/>', $this->errors[$file]) . '
                <div class="adm-info-message-icon"></div>
            </div>
        </div>';
        }

        return $result;
    }

    public function showRules($file)
    {
        if ($this->config[$file]['rules']) {
            return '<div class="adm-info-message-wrap"><div class="adm-info-message">' . $this->config[$file]['rules'] . '</div></div>';
        }

        return '';
    }

    public function showTab($file)
    {
        return $this->showErrors($file) . $this->showTextarea($file) . $this->showRules($file);
    }

    public function showTextarea($file)
    {
        return '<textarea style="width: 100%; min-height: 400px" name="sphinx_' . $file . '">' . $this->getConfigDecodeData($file) . '</textarea>';
    }

}

$sphinxHelper = new SphinxHelper();

$sphinxHelper->saveAllConfig();

$default_message = '
<div class="adm-info-message-wrap adm-info-message-red">
    <div class="adm-info-message">
    <div class="adm-info-message-title">Внимание!</div>
    Поcле изменения словарей необходимо перезагрузить sphinx и выполнить переиндексацию
    <br/>
    <br/>
    <div class="adm-info-message-icon"></div>
    <div class="adm-info-message-buttons">
        <a href="?restart=y" class="adm-btn" title="Переиндексация сайта">Перезагрузить sphinx</a>
        </div>
    </div>
</div>';
$start_reindex_message = '<div class="adm-info-message-wrap adm-info-message-gray"><div class="adm-info-message"><div class="adm-info-message-title">Запуск переиндексации</div>пожалуйста, подождите</div></div>';
if (is_file($sphinxPatch . "restarted")) {
    $default_message .= '<div class="adm-info-message-wrap adm-info-message-gray"><div class="adm-info-message"><div class="adm-info-message-title">Sphinx перезапущен</div>
 <a href="/bitrix/admin/search_reindex.php?lang=ru" class="adm-btn" title="Переиндексация сайта">Переиндексация сайта</a>
 </div></div>';
}
?>

<div id="reindex_result_div" style="margin:0px">
    <? if (isset($proccess_message) && !empty($proccess_message)) {
        echo $proccess_message;
    } else {
        echo $default_message;
    } ?>
</div>
<form method="post" action="<?= $APPLICATION->GetCurPage() ?>" name="post_form">
    <?= bitrix_sessid_post(); ?>
    <?
    $aTabs = array(
        array(
            "DIV" => "edit1",
            "TAB" => 'Словарь синонимов',
            "TITLE" => "Словарь синонимов"
        ),
        array(
            "DIV" => "edit2",
            "TAB" => 'Словарь стоп слов',
            "TITLE" => "Словарь стоп слов"
        ),
        array(
            "DIV" => "edit3",
            "TAB" => 'Словарь словоформ',
            "TITLE" => "Словарь словоформ"
        ),
    );
    $tabControl = new CAdminTabControl("tabControl", $aTabs);
    ?>

    <? $tabControl->Begin(); ?>

    <? $tabControl->BeginNextTab(); ?>
    <?= $sphinxHelper->showTab('exceptions'); ?>

    <? $tabControl->BeginNextTab(); ?>
    <?= $sphinxHelper->showTab('stopwords'); ?>

    <? $tabControl->BeginNextTab(); ?>
    <?= $sphinxHelper->showTab('wordforms'); ?>

    <? $tabControl->Buttons(
        array(
            "disabled" => false,
            "back_url" => "/bitrix/admin/?lang=" . LANGUAGE_ID . "&" . bitrix_sessid_get()
        )
    ); ?>
    <? $tabControl->End(); ?>
</form>
<script>
    timerId = 0;

    function check_sphinx_status() {
        BX.ajax({
            url: location.href.replace("reindex=y", ""),
            data: {GET_STATUS: '1'},
            method: 'POST',
            dataType: 'html',
            timeout: 30,
            async: true,
            processData: true,
            scriptsRunFirst: true,
            emulateOnload: true,
            start: true,
            cache: false,
            onsuccess: function (data) {
                if (data) {
                    BX.adjust(BX('reindex_result_div'), {html: data});
                }
            },
            onfailure: function () {
                console.log('fail');
            }
        });
    }

    function start_reindex(timerId) {
        BX.adjust(BX('reindex_result_div'), {html: '<?=$start_reindex_message?>'});
        clearInterval(timerId);
        BX.ajax({
            url: location.href + "?reindex=y",
            method: 'GET',
            dataType: 'html',
            timeout: 30,
            async: true,
            processData: true,
            scriptsRunFirst: true,
            emulateOnload: true,
            start: true,
            cache: false,
            onsuccess: function (data) {
                timerId = setInterval(function () {
                    check_sphinx_status();
                }, 4000);
            },
            onfailure: function () {
                console.log('fail');
            }
        });
        return false;
    }
</script>
<?
require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/epilog_admin_before.php");
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/epilog_admin_after.php");