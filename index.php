<?php
include_once "header.php";
include_once "process/function.php"?>
<!DOCTYPE html>
<html>
<head>
	<title>OmpTG Online</title>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1"/>
	<link rel="stylesheet" type="text/css" href="css/style.css">
</head>
<body>

	<div class="content">
		<h1>OmpTG Online</h1>
		<h3>Menu List</h3>
		<a href="c2ompic_index.php" class="btn">Step.1 Convert OpenMP C Code To OMPi Trimmed Code</a>
		<a href="c2alf_index.php" class="btn" >Step.2 Convert OMPi Trimmed Code into ALF Code</a>
		<a href="wcet_index.php" class="btn">Step.3 Generate WCET By ALF Code</a>
        <a href="alf2relation.php" class="btn">Step.4 Generate Relation File By ALF Code</a>
		<a href="alf2pcfg_index.php" class="btn" >Step.5 Generate PCFG By ALF Code</a>
		<a href="alf2efg_index.php" class="btn" >Step.6 Generate EFG By ALF Code</a>
        <a href="http://omptg.doc.kingtous.cn"  class="btn">Documention</a>
        <?php
        $p="files/".session_id();
        if (file_exists($p)){
            echo "<a href=\"$p\" class=\"btn\" > >>> Check My Files <<< </a>";
        }
        ?>
	</div>

    <iframe  width="100%" height="40px auto" align="center" style="margin-top: 15px" src="tools/time.html"></iframe>

	<div class="footer">
        <p></p>
		<p> © 2019 OmpTG Online, RTCO-LAB, Northeastern University All Rights Reserved</p>
		<br>
		<a href="http://www.beian.miit.gov.cn" style="color: black; align:center;" draggable="false">冀ICP备18017068号-1</a>
		<br>
	</div>


</body>
</html>