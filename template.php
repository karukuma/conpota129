<html>
<head>
	<link rel="stylesheet" type="text/css" href="<?php echo($relativepath); ?>css/ucal_common.css" media="all">
	<link rel="stylesheet" type="text/css" href="<?php echo($relativepath); ?>css/ucalp.css" media="print">
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <?php
    if($redirect_url != ""){
        ?>
        <meta http-equiv='refresh' content='0; URL="<?php echo($redirect_url); ?>"'>";
    <?php    
    }
    ?>
	<link type="text/css" href="<?php echo($relativepath); ?>js/jquery-ui-1.12.1.custom/jquery-ui.theme.min.css" rel="Stylesheet" />
	<script type="text/javascript" src="<?php echo($relativepath); ?>js/jquery-3.2.1.min.js"></script>
	<script type="text/javascript" src="<?php echo($relativepath); ?>js/jquery-ui-1.12.1.custom/jquery-ui.js"></script>
	<script type="text/javascript" src="<?php echo($relativepath); ?>js/extcal.js"></script>
	<title><?php echo ($title_insert); ?></title>
</head>
<body onLoad="document.form1['uni/title'].focus();">
    <div id="pageimage">
        <h1><a href='<?php echo($relativepath); ?>index.php' class='headerh1'><?php echo($content_header_title); ?></a></h1>
        <?php echo($content_header_inputmode); ?>
        <?php echo($content_header_keiji); ?>
        <?php echo($content_header_inputarea); ?>
        <?php echo($content_header_pickup); ?>
        <?php echo($content_header_memberCheckBoxes); ?>
        <?php echo($content_header_todaydate); ?>
        <?php echo($content_header_navi); ?>
        <br>
        <?php echo($content_header_membericons); ?>
        <?php echo($content); ?>
        <div class="footer">
        <?php echo($content_footer_searchBox); ?>
        <?php echo($content_footer_memberCheckBoxes); ?>
        <?php echo($content_footer_navi); ?>
        <?php echo($content_footer_menu); ?>
        </div>
        <div class='footer2'>
        <?php echo($content_footer_copyright); ?>
        <?php echo($content_footer_rss); ?>
        </div>
    </div>
</body>
</html>
