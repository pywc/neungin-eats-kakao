<?php
function getmeal2($days)
{
  $date = date("Y.m.d", strtotime("+$days days"));
  if ($date == "2018.10.28"){
    $final2 = "짜장밥, ";
    $final2 = $final2 . "수제불고기퀘사디아, ";
    $final2 = $final2 . "진미야채무침, ";
    $final2 = $final2 . "김치, ";
    $final2 = $final2 . "레몬에이드";
    $return = array($date, $final2); // 해당날짜, 급식메뉴
    return $return;
  }
  // 음수를 인자로 주는 것은 코딩이 귀찮아져 생략.
  // 그에 따라 <어제 급식> 메뉴는 삭제해버림
  header("Content-type: application/json; charset=UTF-8");        // json type and UTF-8 encoding
  require("Snoopy.class.php");
  $URL = "https://stu.dge.go.kr/sts_sci_md01_001.do?schulCode=D100000277&&schulCrseScCode=3&schMmealScCode=3&schYmd=" . $date; // DOMDocument
  // url 생성
  $snoopy = new Snoopy; // snoopy 생성
  $snoopy->fetch($URL);

  preg_match('/<tbody>(.*?)<\/tbody>/is', $snoopy->results, $tbody); // tbody 추출
  $final2=$tbody[0];
  preg_match_all('/<tr>(.*?)<\/tr>/is', $final2, $final2); // tr 추출

  $final2=$final2[0][1]; // 첫 번째 항목(0)은 급식인원, 두 번째 항목은 식단표(1)이므로
  preg_match_all('/<td class="textC">(.*?)<\/td>/is', $final2, $final2); // td 추출
  $day=0; // weekday number를 가져옴
  if ( date('w')+$days > 6) {
    $day = (date('w')+$days)-7;
  } else {
    $day = date('w')+$days;
  }
  // 주말이면 인덱스가 넘어버리니까 수정(될지는 테스트 안해봄)
  $final2=$final2[0][$day]; // 해당 날의 급식을 가져옴
  $final2=preg_replace("/[0-9]/", "", $final2);
  // 숫자 제거(정규식이용)
  $array_filter = array('.', ' ', '<tdclass="textC">', '</td>');
  // 필터
  foreach ($array_filter as $filter) {
      $final2=str_replace($filter, '', $final2);
  } // 필터 내용 검색해 삭제
  $final2=str_replace('<br/>', ', ', $final2); // br => 개행
  $final2=substr($final2, 0, -2); // 마지막 줄 개행문자 없애기
  if ( strcmp($final2, '') == false ){
    $final2 = "급식이 없습니다."; // 급식이 없을 경우
  }
  $return = array($date, $final2); // 해당날짜, 급식메뉴
  return $return;
}
$final=getmeal2(0); echo $final[1];
?>
