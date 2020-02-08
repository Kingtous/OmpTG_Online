<?php
include_once "../header.php";
include_once "function.php";
?>
<html>
<head>
    <title>OmpTG--EFG Generator</title>
    <link rel="stylesheet" href="../css/style.css">
    <link href='//fonts.googleapis.com/css?family=Open+Sans:400,300italic,300,400italic,600,600italic,700,700italic,800,800italic'
          rel='stylesheet' type='text/css'>
    <link href='//fonts.googleapis.com/css?family=Montserrat:400,700' rel='stylesheet' type='text/css'>

    <link rel="stylesheet" href="../css/normalize.min.css">
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <script type="application/x-javascript"> addEventListener("load", function () {
            setTimeout(hideURLbar, 0);
        }, false);

        function hideURLbar() {
            window.scrollTo(0, 1);
        } </script>

</head>
<body>

<h1>EFG Generator</h1>
<iframe width="100%" height="40px auto" align="center" style="margin-top: 15px" src="../tools/time.html"></iframe>

<p>
<form action='../index.php'>
    <input type='submit' value='Back to Menu'/>
</form>
<div class="agile-its">
    <h2>File Upload</h2>
    <div class="w3layouts">
        <p>Convert ALF Code into Execution Flow Graph</p>
        <div class="photos-upload-view">
            <div id="messages">
                <p id="progress">
                    <script> document.getElementById("progress").innerText = "Processing..." </script>
                    <!--                该用来查看选中的elements-->
                    <script type="text/javascript">
                        function getSelectedValue() {
                            var radios = document.getElementsByName("file_radio");
                            var value = 0;
                            for (var i = 0; i < radios.length; i++) {
                                if (radios[i].checked == true) {
                                    value = radios[i].value;
                                }
                            }
                            return value;
                        }
                    </script>
                    <?php
                    if (isset($_POST['start_process'])) {
                        try{
                            // 变量声明
                            $pPath = $_POST['work_path'];
                            $dotFile = $_POST['dot'];
                            $wctFile = $_POST['wct'];
                            $relFile = $_POST['relation'];
                            $parseFunction = $_POST['parse'];
                            // 检查变量合法性
                            if (file_exists($pPath.'/'.$dotFile) and file_exists($pPath.'/'.$wctFile) and file_exists($pPath.'/'.$relFile) ){
                                $command = command_gen_efg($pPath.'/'.$dotFile,$pPath.'/'.$wctFile,$pPath.'/'.$relFile,$parseFunction);
                                my_exec(session_id(),$command);
                                // 检查是否生成图
                                $efgFolder = $pPath.'/'.'EFG';
                                $filelist = getFileList($efgFolder);
                                if (count($filelist) > 0){
                                    showMessage("See EFG folder in files for more details.");
                                }
                                show_home_btn();
                            }
                            else{
                                throw new ErrorException(IOError);
                            }
                        }
                        catch (ErrorException $e){
                            showError($e->getMessage());
                        }
                    } else {
                        if (!isset($_FILES["file"])) {
                            showMessage("No Entry Selected.");
                            goto _exit;
                        }
                        echo "<br>";
                        try {
                            if ($_FILES["file"]["error"] > 0) {

                                switch ($_FILES["file"]["error"]) {
                                    case 2:
                                        showMessage("Size Limited!");
                                        break;
                                    default:
                                        showMessage("Error: " . $_FILES["file"]["error"]);
                                }

                            } else {
                                if (!check_suffix("zip", $_FILES["file"]["name"])) throw new ErrorException("Suffix mismatch.");
                                showMessage("Upload: " . $_FILES["file"]["name"]);
                                showMessage("Size: " . ($_FILES["file"]["size"] / 1024) . " KB");
//                            showMessage("Path: " . $_FILES["file"]["tmp_name"]);
                            }
                            /*
                             * fName -> file name
                             * ofPath -> original file path
                             * pPath -> process Path
                             */
//                            echo 'session_id=' . session_id();
                            $fName = $_FILES["file"]["name"];
                            $ofPath = $_FILES["file"]['tmp_name'];
                            $pPath = sys_get_temp_dir() . '/phptemp/' . session_id(); // 为当前session创建的处理文件夹，处理操作都在这个文件夹内完成
                            $folder = $pPath . '/' . basename($fName, '.zip'); // zip解压后的文件夹路径
                            $dest = $pPath . '/' . $fName; // 重命名并移动后，zip文件的路径
                            show_process_animation();
                            showMessage(LONG_RES);
		            create_temp_folder_for_current_id();
                            if (file_exists($pPath) || mkdir($pPath)) {
                                //成功创建临时文件夹或者文件夹已存在，tmp/session_id/
                                if (move_uploaded_file($ofPath, $dest)) {
//                                echo '<br>'.'移动成功'.'</br>';
                                    //移动成功

                                    if (file_exists($folder)) {
                                        delDir($folder);
                                    }

                                    if (file_exists($folder) || mkdir($folder)) {
                                        //解压
                                        echo '<br>unzipping...</br>';
                                        shell_exec('unzip -d ' . $folder . ' ' . $dest . '&2>1');
                                        //列出文件
                                        $files = getFileList($folder);
                                        //初始化要储存的变量
                                        $dot_file = null;
                                        $wct_file = null;
                                        $rel_file = null;
                                        //显示dot文件
                                        foreach ($files as $file) {
                                            $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
                                            switch ($ext) {
                                                case ("dot"):
                                                    $dot_file = $file;
                                                    break;
                                                case ("wct"):
                                                    $wct_file = $file;
                                                    break;
                                                case ("txt");
                                                    $rel_file = $file;
                                                    break;
                                            }
                                        }
                                        if ($dot_file == null or $wct_file == null or $rel_file == null) {
                                            // 三个文件有一个没有都不行
                                            var_dump($dot_file);
                                            var_dump($wct_file);
                                            var_dump($rel_file);
                                            throw new ErrorException(NOT_ENOUGH_FILE_FOUND);
                                        } else {
                                            // 需要用到分析入口，通过dot文件中是否有_thrFuncXXX_来判断，并列出一个列表
                                            $result=[];
                                            $dotContent=fread(fopen($folder.'/'.$dot_file,"r"),filesize($folder.'/'.$dot_file));
                                            preg_match_all(REG_THRFUNC,$dotContent,$result);
                                           // var_dump($result);
                                            if (!is_null($result[0])){
                                                $result = $result[0];
                                                $result = array_flip($result);
                                                $result = array_flip($result);
                                                $result = array_values($result);
                                                //var_dump($result);
                                            }
                                            else{
                                                throw new ErrorException(FileError."(No Entry Found in Dot File)");
                                            }
                                            showMessage("We have found :");
                                            //显示结果
                                            echo '<form id="file_select_form" method="post" action="efg_show.php">';
                                            //显示
                                            echo "<input type='hidden' name='start_process' value='true'/>>";
                                            echo "<input type='hidden' name='work_path' value='$folder'/>>";
                                            echo "<p>DOT FILE: <input type='text' name='dot' value='$dot_file' readonly/></p>";
                                            echo "<p>WCT FILE: <input type='text' name='wct' value='$wct_file' readonly/></p>";
                                            echo "<p>RELATION FILE: <input type='text' name='relation' value='$rel_file' readonly/></p>";
                                            echo "<p>SELECT ENTRY:";
                                            set_options_in_form("parse","parse",$result,$result);
                                            echo "</p>";
                                            echo '<input type="submit" id="select" value="confirm" onclick="
                                           this.disabled=true;
                                           this.value=\'processing...\';
                                           document.getElementById(\'file_select_form\').submit();
                                           return true;
                                      "/>';
                                            echo '</form>';
                                        }
                                    } else throw new ErrorException("cannot create folder" . $folder);
                                } else throw new ErrorException("cannot move uploaded file in" . $pPath);
                            } else throw new ErrorException("cannot create folder in " . $pPath);
                        } catch (ErrorException $e) {
                            echo '<br>' . 'Error:' . $e->getMessage() . '</br>';
                        }
                        _exit:
                    }
                    ?>
                </p>
            </div>
        </div>
        <div class="clearfix"></div>
        <!--测试进度用的js-->
        <!--<script src="../js/process_show.js"></script>-->
    </div>
</div>


</body>
</html>


