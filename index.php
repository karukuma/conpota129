<?php
require_once('./lib/unicom.php');
require_once('./lib/unicomdisp.php');
require_once('./lib/uniview.php');
ini_set('display_errors', DISP_ERR);

if ($_SERVER["REQUEST_METHOD"] === "GET") {
	if(!empty($_GET["op"])){
		$output_target_GET = $_GET["op"];
		switch ($output_target_GET){
			case "jsonp":
				$output_target = 1;
				header("Content-type: application/json; charset=utf-8");
				break;
			case "json":
				$output_target = 2;
				header("Content-type: application/json; charset=utf-8");
				break;
			case "html":
				header("Content-type: text/html; charset=utf-8");
				$output_target = 0;
				break;
			default:
				header("Content-type: text/html; charset=utf-8");
				$output_target = 0;
		}
	}else{
		$output_target = 0;
	}
}


$db_file = "./data/unicale.db";
$pathinfo = pathinfo(__FILE__);
$basedirTmp = realpath($pathinfo['dirname'])."/";
$root_dir = $basedirTmp;
$basedirTmp = $basedirTmp."data/";
$basedir = $basedirTmp;

$thisURL = (empty($_SERVER["HTTPS"]) ? "http://" : "https://") . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"];
$thisURLindex = (empty($_SERVER["HTTPS"]) ? "http://" : "https://") . $_SERVER["HTTP_HOST"] . $_SERVER["SCRIPT_NAME"];
$thisURLBase = str_replace("index.php","",(empty($_SERVER["HTTPS"]) ? "http://" : "https://") . $_SERVER["HTTP_HOST"] . $_SERVER["SCRIPT_NAME"]);

$Keijimsg = "";
if ($_SERVER["REQUEST_METHOD"] === "GET") {
	if(!empty($_GET["mode"])){
		if($_GET["mode"] == "installed"){
			$installer_rename_filename = date("Ymd").date("His")."_install.php";
			$old_installer_filename = $root_dir."data/install.php";
			$installer_rename_filename_path = $root_dir."data/". $installer_rename_filename;
			rename($old_installer_filename, $installer_rename_filename_path);
			
			$Keijimsg .= "インストールが終了しました。<br>install.phpを、".$installer_rename_filename."とリネームしました。<br>";
			$Keijimsg .= "data/".$installer_rename_filename."を削除するかWebから見えない位置に移動してください。<br>";
			$Keijimsg .= "誤ってすべてのカレンダーデータを消してしまう恐れがあります。";
		}
	}
}



$checkFileName = $root_dir."/data/install.php";
if(file_exists($checkFileName)){
	$title = "インストール";
	ob_start();
	?>
		<h1 class="headerh1">インストール</h1>
		<div style="margin: 0 auto;">
			<br> UNICALEのインストールを行いますか？
			<br>
			<br>
<?php 
		if(file_exists($db_file)){
?>
			既存のデータベースデータが見つかりました。<br>「アップグレードインストール」を行うと、既存のデータを残したままシステムのみのアップグレードができます。<br><br>
			<a href="./data/install.php?mode=upgrade" class="ui-button" style="margin: 0.5em; padding: 0.5em;">アップグレードインストール</a><br><br>
<?php
		}
?>
			<br> 新規インストールを行うと、すでにカレンダーデータが存在していた場合、データは削除され、新規にインストールされます。<br><br>
			<a href="./data/install.php?mode=new" class="ui-button" style="margin: 0.5em; padding: 0.5em;">新規インストール</a>
			<br>
			<br>
		</div>
	<?php
	$content = ob_get_clean();
	require_once('template.php');
	exit;
}


$pdo_connection = "sqlite:".$db_file;
if(! $db = new PDO($pdo_connection)){
  die("DB Connection Failed.");
}
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$unicom = new unicom();
$unicomdisp = new unicomdisp();
$uniview = new uniview();
$makeNavi = new makeNavi();
$confData = new confData();
$confData->getConfData($db);

if(isset($_SERVER['QUERY_STRING'])){
	if(trim($_SERVER['QUERY_STRING']) == ""){
		$query = "";
	}else{
		$query = "?".$_SERVER['QUERY_STRING'];
	}
}else{
	$query = "";
}

if($confData->conf["event_calendar_mode"] == true){
		header('location: ./event/calendar.php'.$query);
	exit();
}else{
	header('location: ./calendar/calendar.php'.$query);
	exit();
}

$db = null;

?>
