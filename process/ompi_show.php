<?php
include_once("../header.php");
include_once("function.php");
?>
<html>
<head>
    <title>OMPi Convertor</title>
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

<h1>OMPi Convertor</h1>
<iframe  width="100%" height="40px auto" align="center" style="margin-top: 15px" src="../tools/time.html"></iframe>

<p>
<form action='../index.php'>
    <input type='submit' value='Back to Menu'/>
</form>
<div class="agile-its">
    <h2>OpenMP C File Upload</h2>
    <div class="w3layouts">
        <p>Convert OpenMP C Code into trimmed OMPi C Code using custom OMPi tools and Trimming Programde</p>
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
                    if (isset($_POST['file_entry'])) {
                        try {
                            // 用户已经选择了函数入口，开始调用
                            $entry = $_POST['file_entry'];
                            $folder = $_POST['folder_path'];
                            showMessage("Entry:" . $entry);
                            show_process_animation();
                            showMessage(LONG_RES);

                            $commmand_1 = '/usr/bin/gcc -E -U__GNUC__ -D_OPENMP=200805 -D_REENTRANT -D_REENTRANT -I /usr/local/include/ompi "' . $folder . '/' . $entry . '" > "' . $folder . '/' . basename($entry, '.c') . '.pc"';
//  调试使用             $commmand_1 = '/usr/bin/gcc -E -U__GNUC__ -D_OPENMP=200805 -D_REENTRANT -D_REENTRANT -I /usr/local/include/ompi "' . $folder.'/'.$entry.  '"';
                            showMessage("gcc preprocessing...");
//                            showMessage("Executing:$commmand_1");
                            if (my_exec(session_id(), $commmand_1)) throw new ErrorException("gcc preprocessing error. ");

                            showMessage("ompi processing...");
                            $ompi_output = $folder . '/' . basename($entry, '.c') . '_ompi.c';
                            $commmand_2 = "/usr/local/bin/_ompi \"" . $folder . '/' . basename($entry, '.c') . '.pc' . "\" __ompi__ > \"" . $ompi_output . "\" ";
//                        showMessage("Executing:$commmand_2");
                            if (my_exec(session_id(), $commmand_2)) throw new ErrorException("ompi preprocessing error. ");
                            // 修剪程序
                            showMessage("trimming program processing...");
                            $commmand_3 = TRIM_P . $ompi_output;
//                            showMessage($commmand_3);
                            if (my_exec(session_id(), $commmand_3)) throw new ErrorException("trimming error. ");
                            showMessage("check output file...");
                            $final_output_file_path = $folder . '/' . basename($entry, '.c') . '_ompi_trim.c';
                            if (file_exists($final_output_file_path)) {
                                showMessage("Done.");
                                rename_and_show_download_btn(null, $final_output_file_path);
                            } else throw new ErrorException(NO_FILE_FOUND);
                        } catch (ErrorException $e) {
                            showMessage($e->getMessage() . UNSPPORRT_CONTACT);
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
//                                    echo '<br>unzip -d ' . $folder . ' ' . $dest . '</br>';
                                        shell_exec('unzip -d ' . $folder . ' ' . $dest . '&2>1');


                                        //列出文件
                                        echo '<br>please select entry:</br>';

                                        $files = getFileList($folder);

                                        echo '<form id="file_select_form" method="post" action="">';

                                        foreach ($files as $file) {
                                            $ext = pathinfo($file, PATHINFO_EXTENSION);
                                            if ($ext == 'c' || $ext == 'C') {
                                                echo '<p><input type="radio" name="file_radio" onclick="document.getElementById(\'select\').removeAttribute(\'disabled\');"  value=' . "$file" . "> " . $file . " </p>";
                                            }
                                        }
                                        echo '<input type="hidden" name="folder_path" value="' . $folder . '"/>';
                                        echo '<input type="hidden" name="file_entry" id="file_entry" />';

                                        echo '<input type="submit" id="select" disabled="disabled" value="select" onclick="
                                           this.disabled=true;
                                           this.value=\'processing...\';
                                           $value = getSelectedValue();
                                           document.getElementById(\'file_entry\').value=$value;
                                           document.getElementById(\'file_select_form\').submit();
                                           return true;
                                      "/>';
                                        echo '</form>';

                                    } else throw new ErrorException("cannot create folder" . $folder);
                                } else throw new ErrorException("cannot move uploaded file in" . $pPath);
                            } else throw new ErrorException("cannot create folder in " . $pPath);
                        } catch (ErrorException $e) {
                            echo '<br>' . 'Error:' . $e->getMessage() . '</br>';
                        }
                    }

                    _exit:

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
