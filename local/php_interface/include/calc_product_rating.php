<?
AddEventHandler("iblock", "OnAfterIBlockElementUpdate", "OnAfterIBlockElementUpdateHandlerUpdateRating");
function OnAfterIBlockElementUpdateHandlerUpdateRating(&$arFields){
    //если это инфоблок с отзывами
    if($arFields['IBLOCK_ID'] == 34 && $arFields['RESULT'] == 1 && !empty($arFields['ID'])){
        //берем редактируемы элемент со значением свойства привязки к товару
        $arFilter = array("IBLOCK_ID" => 34, "ID" => $arFields['ID']);
        $res = CIBlockElement::GetList(array('ID' => 'ASC'), $arFilter, false, array('nTopCount'=>1), array("ID", "IBLOCK_ID", "NAME", "PROPERTY_PRODUCT_ID"));
        if($ob = $res->GetNext(false, false)) {
            $element = $ob;
        }
        if(!empty($element['PROPERTY_PRODUCT_ID_VALUE'])){
            //собираем все отзывы к данному товару
            $arFilter = array("IBLOCK_ID" => 34, "ACTIVE"=>"Y", "PROPERTY_PRODUCT_ID_VALUE" => $element['PROPERTY_PRODUCT_ID_VALUE']);
            $res = CIBlockElement::GetList(array('ID' => 'ASC'), $arFilter, false, false, array("ID", "IBLOCK_ID", "NAME", "PROPERTY_STARS"));
            while($ob = $res->GetNext(false, false)) {
                $reviews[] = $ob;
            }

            //расчет рейтинга
            $sumStars = 0;//сумма голосов
            $countStars = 0;//кол-во голосов
            $rating = 0;//рейтинг
            $average = 0;//средний балл
            $minVotes = 1;//минимальное кол-во проголосовавших для imdb
            if(!empty($reviews)){
                foreach ($reviews as $reviewItem) {
                    if(!empty($reviewItem['PROPERTY_STARS_VALUE'])){
                        $countStars++;
                        $sumStars = $sumStars+intval($reviewItem['PROPERTY_STARS_VALUE']);
                    }
                }
            }

            $average = round($sumStars/$countStars,2);
            //$rating = round(($sumStars + 31.25) / ($countStars + 10),2); //формула екслера http://www.exler.ru/blog/item/1990/
            $rating = round(($countStars/($countStars+$minVotes))*$average+($minVotes/($countStars+$minVotes)),2); // формула imdb http://www.imdb.com/chart/top (внизу)

            //свежий рейтинг записываем в товар (обновляем рейтинг)
            $arRating = array(
                'vote_count' => $countStars,
                'vote_sum' => $sumStars,
                'rating' => $rating,
            );
            CIBlockElement::SetPropertyValuesEx($element['PROPERTY_PRODUCT_ID_VALUE'], 10, $arRating);
        }
    }
}