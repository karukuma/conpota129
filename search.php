<?php
require_once('./lib/unicom.php');
ini_set('display_errors', DISP_ERR);

$basedirTmp = __FILE__;
//$basedirTmp = str_replace("\\","/",$basedirTmp); //WIN32
$basedirTmp = str_replace("index.php","",$basedirTmp);
$root_dir = $basedirTmp;
$basedirTmp = $basedirTmp."data/";
$basedir = $basedirTmp;

if (! $db = new PDO("sqlite:./data/unicale.db")) {
  die("DB Connection Failed.");
}
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$unicom = new unicom();
$confData = new confData();
$confData->getConfData($db);
$checkSubmit = new checkSubmit();
$calname = $confData->conf["calname"];

$fullSearch = new fullSearch();

$realtime_list = $unicom->cvtTimename2RealtimeList($db);
$realtime_list_JP = $unicom->cvtTimename2RealtimeListJP($db);
$realtime_list_JPEN = $unicom->cvtTimenameJP2ENlist($db);
$realtime_list_ID = $unicom->cvtTimename2TimezoneIDList($db);


$disp_result = 0;
$editable = false;
if ($_SERVER["REQUEST_METHOD"] === "GET") {
	if(!empty($_GET["tag"])){
		$keyword_unique = array("#".$fullSearch->cleanWordText($_GET["tag"]));
		$flat_input_keyword = implode("",$keyword_unique);
		$disp_result = 1;
	}elseif(!empty($_GET["sk"])){
		$keyword = $fullSearch->cleanWordText($_GET["sk"]);
		$keyword_array = explode(" ",$keyword);
		$keyword_unique = array_unique($keyword_array);
		$keyword_unique = array_values($keyword_unique);	//飛び飛びのキーを振り直す
		$flat_input_keyword = implode( " ", $keyword_unique);
		$disp_result = 1;
		
		if(!empty($_GET["editable"])){
			if($_GET["editable"] == "true"){
				$editable = true;
			}
		}

	}
}

if($editable){
	$editable_str = "true";
}else{
	$editable_str = "false";
}

?>
<html>
	<head>
		<link rel="stylesheet" type="text/css" href="./css/ucal_common.css"  media="all">
		<link rel="stylesheet" type="text/css" href="./css/ucalp.css" media="print">
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<link type="text/css" href="./js/jquery-ui-1.12.1.custom/jquery-ui.theme.min.css" rel="Stylesheet" />
		<script type="text/javascript" src="./js/jquery-3.2.1.min.js"></script>
		<script type="text/javascript" src="./js/jquery-ui-1.12.1.custom/jquery-ui.js"></script>
		<title><?php echo $calname; ?></title>
	</head>
	<body onLoad="document.form1.sk.focus()">
	<div id="pageimage">
	<h1><a href="./index.php" class="headerh1"><?php echo($calname);?></a></h1>
	<div id="search">
		<form action="search.php" method="get" name="form1">
			<input type="text" name="sk" size="40" value="<?php echo($flat_input_keyword); ?>"><input type="submit" name="search" value="検索">
			<input type='hidden' name='editable' value='<?php echo($editable_str);?>'>
		</form>
	</div>
<?php

$currentlastDay = date('d', strtotime('last day of ' . $currentYear."-".$currentMonth));

if ($disp_result == 1){
	$result_str = $fullSearch->fullSearchText($db,$keyword_unique);
	$result_counter = count($result_str);
	echo(($result_counter)."件ヒット<br>");
	echo("<table class='search_table'>");
	echo("<thead>");
	echo("<tr>");
	echo("<th style='width: 100px;'>日付</th>");
	echo("<th style='width: 100px;'>開始時刻</th>");
	echo("<th style='width: 100px;'>カテゴリ</th>");
	echo("<th>タイトル</th>");
	echo("<th>詳細</th>");
	echo("</tr>");
	echo("</thead>");

	for($i=0; $i<$result_counter;$i++){

		$theYYYYMMDD = str_replace("-","",$result_str[$i]["startdate"]);
		echo("<tr>");
		echo("<td><a href='./index.php?d=".$theYYYYMMDD."&focus=".$theYYYYMMDD."#focus'>".$result_str[$i]["startdate"]."</a></td>");
		if($result_str[$i]["vaguedate_f"] == 1){
			if($result_str[$i]["starttime"] == "ALL1"){
				echo("<td>&nbsp;");
			}else{
				echo("<td>".$result_str[$i]["timename_jp"]);
			}
		}else{
			echo("<td>".insertHoursemicolon($result_str[$i]["starttime"]));
		}
		if(empty($result_str[$i]["endtime"])){
				echo("&nbsp;</td>");
		}else{
			echo("〜".insertHoursemicolon($result_str[$i]["endtime"])."</td>");
		}

		echo("<td>");
		echo("<span style='font-size: 80%; background-color:#".$result_str[$i]["categorycolor"].";border-radius:2px;	margin: 2px 2px 0.4em 2px; padding: 2px;'>".mb_substr($result_str[$i]["categoryname"],0, 10,"utf-8")."</span>");
//		echo("<a href='edit.php?id=".$data["id"]."'>".mb_substr($result_str[$i]["title"], 0, 20,"utf-8")."</a>");
		echo("</td>");
		echo("<td>");

		if($editable == true){
			$link_head = "<a href='./calendar/edit.php?id=".$result_str[$i]["id"]."'>";
			$link_foot = "</a>";
		}else{
			$link_head = "";
			$link_foot = "";
		}

		echo($link_head.mb_substr($result_str[$i]["title"],0,20,"utf-8").$link_foot);
		if(!empty($result_str[$i]["place"])){
			echo("(".$result_str[$i]["place"].")");
		}
		echo("</td>");

		echo("<td>");
//		echo("<a href='./edit.php?id=".$result_str[$i]["id"]."'>".mb_substr($result_str[$i]["detail"],0,20,"utf-8")."</a>");
		$tags_html = tagconvt($result_str[$i]["detail"]);
		if($tags_html == $result_str[$i]["detail"]){
			echo(mb_substr($result_str[$i]["detail"],0,40,"utf-8")."...");
		}else{
			print_r($tags_html);
		}
		echo("</td>");
		echo("</tr>");
	}
	echo("</table>");
}

echo("<div class='forprint'>");
echo($unicom->makeMemberCheckBoxes2($db,0,3)); 
echo("</div>");
echo("<div class='tags'>");
$tags = $unicom->getTagsArray($db);
foreach($tags as $tagOne){
	echo("<a onClick=\"addTag('".$tagOne."')\">");
	echo("#".$tagOne);
	echo("</a>&nbsp;&nbsp;");
}
echo("</div>");

$unicom->dispFooter($confData);
?>
	<script>
	function addTag(param){
		Detailtxt = document.form1.sk.value + " #" + param;
		document.form1.sk.value = Detailtxt;
	}
	</script>

<?php
echo("</div>");
echo("</body>");
echo("</html>");
$db = null;

function insertHoursemicolon($hourstring){
		$HH = (int) substr($hourstring,0,2);
		$MM = (int) substr($hourstring,2,2);
		$MMstr = sprintf("%02d",$MM);
	
	return ($HH.":".$MMstr);
}

function tagconvt($text){
	$tags_html = preg_replace_callback('/#([^#\s]*)/u', function ($m) {
		list (, $h) = $m;
		return sprintf("<a href='./search.php?tag=%s'>#%s</a>",
			htmlspecialchars($h),
			htmlspecialchars($h)
			);
		}, $text);

	return ($tags_html);
}
?>
