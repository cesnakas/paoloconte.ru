<?
require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");

if (!CModule::IncludeModule('search') || !CModule::IncludeModule("iblock"))
    return;

/**
 * Генерация сайтмапа на основе стандартного Google Sitemap.
 * Кастомизирован для того, что бы добавить урлы отфильтрованных категорий в сайтмап
 */
class GGSiteMap extends CSiteMap
{
    function Create($site_id, $max_execution_time, $NS, $arOptions = array(), $customParams)
    {        
        @set_time_limit(0);
        if (!is_array($NS)) {
            $NS = Array(
                "ID" => 0,
                "CNT" => 0,
                "FILE_SIZE" => 0,
                "FILE_ID" => 1,
                "FILE_URL_CNT" => 0,
                "ERROR_CNT" => 0,
                "PARAM2" => 0,
            );
        } else {
            $NS = Array(
                "ID" => intval($NS["ID"]),
                "CNT" => intval($NS["CNT"]),
                "FILE_SIZE" => intval($NS["FILE_SIZE"]),
                "FILE_ID" => intval($NS["FILE_ID"]),
                "FILE_URL_CNT" => intval($NS["FILE_URL_CNT"]),
                "ERROR_CNT" => intval($NS["ERROR_CNT"]),
                "PARAM2" => intval($NS["ID"]),
                "SECTIONS_END" => intval($NS["SECTIONS_END"]),
            );
        }

        if (is_array($max_execution_time)) {
            $record_limit = $max_execution_time[1];
            $max_execution_time = $max_execution_time[0];
        } else {
            $record_limit = 5000;
        }

        if ($max_execution_time > 0) {
            $end_of_execution = time() + $max_execution_time;
        } else {
            $end_of_execution = 0;
        }

        if (is_array($arOptions) && ($arOptions["FORUM_TOPICS_ONLY"] == "Y"))
            $bForumTopicsOnly = CModule::IncludeModule("forum");
        else
            $bForumTopicsOnly = false;

        if (is_array($arOptions) && ($arOptions["BLOG_NO_COMMENTS"] == "Y"))
            $bBlogNoComments = CModule::IncludeModule("blog");
        else
            $bBlogNoComments = false;

        if (is_array($arOptions) && ($arOptions["USE_HTTPS"] == "Y"))
            $strProto = "https://";
        else
            $strProto = "http://";

        $seoSitemapUrl = $_SERVER["DOCUMENT_ROOT"]."/sitemap_seometa_1.xml";
        $seoSitemapNode = "";
        if (file_exists($seoSitemapUrl)){
            $changeTime = filemtime($seoSitemapUrl);
            $changeTime = date("c", $changeTime);
            $serverName = $_SERVER["HTTP_HOST"];
            if (!strlen($serverName)) $serverName = "paoloconte.ru";
            $seoSitemapNode = "
            <sitemap>
                <loc>".$strProto.$serverName."/sitemap_seometa_1.xml"."</loc>
                <lastmod>".$changeTime."</lastmod>
            </sitemap>            
            ";
        }        
        
        $rsSite = CSite::GetByID($site_id);
        if ($arSite = $rsSite->Fetch()) {
            $SERVER_NAME = trim($arSite["SERVER_NAME"]);
            if (strlen($SERVER_NAME) <= 0) {
                $this->m_error = GetMessage("SEARCH_ERROR_SERVER_NAME", array("#SITE_ID#" => '<a href="site_edit.php?LID=' . urlencode($site_id) . '&lang=' . urlencode(LANGUAGE_ID) . '">' . htmlspecialcharsbx($site_id) . '</a>')) . "<br>";
                return false;
            }
            //Cache events
            $this->m_events = GetModuleEvents("search", "OnSearchGetURL", true);

            //Clear error file
            if ($NS["ID"] == 0 && $NS["CNT"] == 0) {
                $e = fopen($arSite["ABS_DOC_ROOT"] . $arSite["DIR"] . "sitemap_errors.xml", "w");
                $strBegin = "<?xml version='1.0' encoding='UTF-8'?>\n<urlset xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\" xsi:schemaLocation=\"http://www.sitemaps.org/schemas/sitemap/0.9\" xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\">\n";
                fwrite($e, $strBegin);
            } //Or open it for append
            else {
                $e = fopen($arSite["ABS_DOC_ROOT"] . $arSite["DIR"] . "sitemap_errors.xml", "a");
            }
            if (!$e) {
                $this->m_error = GetMessage("SEARCH_ERROR_OPEN_FILE") . " " . $arSite["ABS_DOC_ROOT"] . $arSite["DIR"] . "sitemap_errors.xml" . "<br>";
                return false;
            }

            // Кастомизированный кусок кода
            // Тут переименовывем первый файл, что бы был без циферок
            if (sprintf("%03d", $NS["FILE_ID"]) == "000") {
                $file_name = $arSite["ABS_DOC_ROOT"] . $arSite["DIR"] . "sitemap.xml";
            } else {
                $file_name = $arSite["ABS_DOC_ROOT"] . $arSite["DIR"] . "sitemap_" . sprintf("%03d", $NS["FILE_ID"]) . ".xml";
            }

            //Open current sitemap file
            if ($NS["FILE_SIZE"] == 0) {
                $f = fopen($file_name, "w");
                $strBegin = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n<urlset xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\" xsi:schemaLocation=\"http://www.sitemaps.org/schemas/sitemap/0.9\" xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\">\n";
                $strBegin .= $seoSitemapNode;
                fwrite($f, $strBegin);
                $NS["FILE_SIZE"] += strlen($strBegin);

            } else {
                $f = fopen($file_name, "a");
            }
            if (!$f) {
                $this->m_error = GetMessage("SEARCH_ERROR_OPEN_FILE") . " " . $file_name . "<br>";
                return false;
            }

            // Конец кастомизированного куска кода

            CTimeZone::Disable();

            $this->GetURLs($site_id, $NS["ID"], $record_limit);
            $bFileIsFull = false;
            while (!$bFileIsFull && $ar = $this->Fetch()) {
                $record_limit--;

                // -- Кастомизированный кусок кода
                $curSection = explode('/', $ar['URL'])[1];
                if (!in_array($curSection, $customParams['PAGES'])) {
                    continue;
                }

                $curPageAddParams = $customParams['PAGE_PARAMS']['DEFAULT'];

                if ($curSection === 'index.php') {
                    $curPageAddParams = $customParams['PAGE_PARAMS']['MAIN'];
                }

                if ($curSection === 'catalog') {
                    $countUrlParts = count(explode('/', $ar['URL']));
                    $isSection = $ar['ITEM_ID']{0} === 'S';

                    if ($countUrlParts === 3 || ($isSection && $countUrlParts === 4)) {
                        $curPageAddParams = $customParams['PAGE_PARAMS']['CATALOG']['SECTION'];
                    } elseif ($isSection && $countUrlParts > 4) {
                        $curPageAddParams = $customParams['PAGE_PARAMS']['CATALOG']['SUBSECTION'];
                    } else {
                        $curPageAddParams = $customParams['PAGE_PARAMS']['CATALOG']['DETAIL'];
                    }
                }
                // Конец кастомизированного куска кода

                $NS["ID"] = $ar["ID"];
                if (strlen($ar["URL"]) < 1)
                    continue;

                if ($bForumTopicsOnly && ($ar["MODULE_ID"] == "forum")) {
                    //Forum topic ID
                    $PARAM2 = intval($ar["PARAM2"]);
                    if ($NS["PARAM2"] < $PARAM2) {
                        $NS["PARAM2"] = $PARAM2;
                        $arTopic = CForumTopic::GetByIDEx($PARAM2);
                        if ($arTopic)
                            $ar["FULL_DATE_CHANGE"] = $arTopic["LAST_POST_DATE"];
                    } else {
                        continue;
                    }
                }

                if ($bBlogNoComments && ($ar["MODULE_ID"] == "blog")) {
                    if (substr($ar["ITEM_ID"], 0, 1) === "C")
                        continue;
                }

                if (preg_match("/^[a-z]+:\\/\\//", $ar["URL"]))
                    $strURL = $ar["URL"];
                else
                    $strURL = $strProto . $ar["SERVER_NAME"] . $ar["URL"];
                $strURL = $this->LocationEncode($this->URLEncode($strURL, "UTF-8"));

                $strTime = $this->TimeEncode(MakeTimeStamp(ConvertDateTime($ar["FULL_DATE_CHANGE"], "DD.MM.YYYY HH:MI:SS"), "DD.MM.YYYY HH:MI:SS"));

                // -- Кастомизированный кусок кода
                $strToWrite = "\t<url>";
                $strToWrite .= "\n\t\t<loc>" . $strURL . "</loc>";
                $strToWrite .= "\n\t\t<lastmod>" . $strTime . "</lastmod>";
                if ($curPageAddParams['CHANGEFREQ']) {
                    $strToWrite .= "\n\t\t<changefreq>" . $curPageAddParams['CHANGEFREQ'] . "</changefreq>";
                }
                if ($curPageAddParams['PRIORITY']) {
                    $strToWrite .= "\n\t\t<priority>" . $curPageAddParams['PRIORITY'] . "</priority>";
                }
                $strToWrite .= "\n\t</url>\n";
                // Конец кастомизированного куска кода

                if (strlen($strURL) > 2048) {
                    fwrite($e, $strToWrite);
                    $NS["ERROR_CNT"]++;
                } else {
                    fwrite($f, $strToWrite);
                    $NS["CNT"]++;
                    $NS["FILE_SIZE"] += strlen($strToWrite);
                    $NS["FILE_URL_CNT"]++;
                }
                //Next File on file size or url count limit
                if ($NS["FILE_SIZE"] > 9000000 || $NS["FILE_URL_CNT"] >= 50000) {
                    $bFileIsFull = true;
                } elseif ($end_of_execution) {
                    if (time() > $end_of_execution) {
                        fclose($e);
                        fclose($f);
                        CTimeZone::Enable();
                        return $NS;
                    }
                }
            }

            CTimeZone::Enable();

            if ($bFileIsFull) {
                fwrite($e, "</urlset>\n");
                fclose($e);
                fwrite($f, "</urlset>\n");
                fclose($f);

                $NS["FILE_SIZE"] = 0;
                $NS["FILE_URL_CNT"] = 0;
                $NS["FILE_ID"]++;
                return $NS;
            } elseif ($record_limit <= 0) {
                return $NS;
            } else {
                fwrite($e, "</urlset>\n");
                fclose($e);
                fwrite($f, "</urlset>\n");
                fclose($f);
            }
            //WRITE INDEX FILE HERE
            $f = fopen($arSite["ABS_DOC_ROOT"] . $arSite["DIR"] . "sitemap_index.xml", "w");
            if (!$f) {
                $this->m_error = GetMessage("SEARCH_ERROR_OPEN_FILE") . " " . $arSite["ABS_DOC_ROOT"] . $arSite["DIR"] . "sitemap_index.xml" . "<br>";
                return false;
            }
            $strBegin = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n<sitemapindex xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\" xsi:schemaLocation=\"http://www.sitemaps.org/schemas/sitemap/0.9\" xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\">\n";
            
            fwrite($f, $strBegin);
            for ($i = 0; $i <= $NS["FILE_ID"]; $i++) {
                // Кастоизированный кусок кода
                if (sprintf("%03d", $i) == "000") {
                    $strFile = $arSite["DIR"] . "sitemap.xml";
                } else {
                    $strFile = $arSite["DIR"] . "sitemap_" . sprintf("%03d", $i) . ".xml";
                }
                // Конец кастоизированного куска кода
                $strTime = $this->TimeEncode(filemtime($arSite["ABS_DOC_ROOT"] . $strFile));
                fwrite($f, "\t<sitemap>\n\t\t<loc>" . $this->URLEncode($strProto . $arSite["SERVER_NAME"] . $strFile, "UTF-8") . "</loc>\n\t\t<lastmod>" . $strTime . "</lastmod>\n\t</sitemap>\n");
            }
            fwrite($f, "</sitemapindex>\n");
            fclose($f);
            $this->m_errors_count = $NS["ERROR_CNT"];
            $this->m_errors_href = $strProto . $arSite["SERVER_NAME"] . $arSite["DIR"] . "sitemap_errors.xml";
            $this->m_href = $strProto . $arSite["SERVER_NAME"] . $arSite["DIR"] . "sitemap_index.xml";
            return true;
        } else {
            $this->m_error = GetMessage("SEARCH_ERROR_SITE_ID") . "<br>";
            return false;
        }
    }
}