<?php
include_once "../header.php";
include_once "function.php";
?>
<!DOCTYPE html>
<html>
<head>
    <title>WCET Generator</title>
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


<h1>WCET Generator</h1>

<iframe  width="100%" height="40px auto" align="center" style="margin-top: 15px" src="../tools/time.html"></iframe>
<p>
<form action='../index.php'>
    <input type='submit' value='Back to Menu'/>
</form>

<div class="agile-its">
    <h2>ALF File Upload</h2>
    <div class="w3layouts">
        <p>Generate WCET Based on ALF Code</p>
        <div class="photos-upload-view">
            <div id="messages">
                <p id="progress">
                    <script> document.getElementById("progress").innerText = "Processing..." </script>

                    <?php
                    echo "<br>";
                    // 检测是否有文件上传
                    if (!isset($_FILES["file"])) {
                        showMessage("No File Selected.");
                        goto _exit;
                    }
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
                            if (! check_suffix("c",$_FILES["file"]["name"])) throw new ErrorException("Suffix mismatch.");
                            showMessage("Upload: " . $_FILES["file"]["name"]);
                            showMessage("Size: " . ($_FILES["file"]["size"] / 1024) . " KB");
//                          showMessage("Path: " . $_FILES["file"]["tmp_name"]);
                            $path = $_FILES["file"]["tmp_name"];

//                            echo 'session_id=' . session_id();
                            $fName = $_FILES["file"]["name"];
                            $ofPath = $_FILES["file"]['tmp_name'];
                            $pPath = sys_get_temp_dir() . '/phptemp/' . session_id(); // 为当前session创建的处理文件夹，处理操作都在这个文件夹内完成
                            $folder = $pPath . '/' . basename($fName, '.c');
                            $dest = $pPath . '/' . $fName; // 重命名并移动后，c文件的路径
                            show_process_animation();
                            showMessage(LONG_RES);

                            if (move_uploaded_file($ofPath, $dest)) {
                                // 处理dest
                                showMessage("Converting C Code to LL Code...");
                                $command_1 = command_c_ll($dest);
//                                showMessage("Executing:" . $command_1);
                                if (my_exec(session_id(), $command_1)) throw new ErrorException("ALFBackend Error:" . UNSPPORRT_CONTACT);
                                $output_file_c1 = $pPath . '/' . basename($dest, '.c') . '.ll';


                                if (file_exists($output_file_c1)) {
                                    showMessage("Converting LL Code to ALF Code and Generating map file...");
                                    $command_2 = command_ll_alf_and_map($output_file_c1);
//                                    showMessage("Executiing:" . $command_2);
                                    if (my_exec(session_id(), $command_2)) throw new ErrorException("ALFBackend Error:" . UNSPPORRT_CONTACT);
                                    $final_output_file_alf = $pPath . '/' . basename($dest, '.c') . '.alf';
                                    $final_output_file_map = $pPath . '/' . basename($dest, '.c') . '.map';

                                    showMessage("Checking Output File...");

                                    if (file_exists($final_output_file_alf) && file_exists($final_output_file_map)) {
                                        showMessage("Done.");
                                        rename_and_show_download_btn(null, $final_output_file_alf, 'ALF FILE');
                                        rename_and_show_download_btn(null, $final_output_file_map, 'MAP FILE');
                                    } else throw new ErrorException(NO_FILE_FOUND);

                                } else throw new ErrorException(NO_FILE_FOUND);

                            } else {
                                throw new ErrorException("Server Error: cannot move files from $ofPath to $dest.");
                            }
                        }
                    } catch (ErrorException $e) {
                        showMessage($e->getMessage());
                    }
                    _exit:
                    show_home_btn();
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
