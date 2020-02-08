<?php
include_once "../header.php";
include_once "function.php";
?>
<!DOCTYPE html>
<html>
<head>
    <title>Relation Generator</title>
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


<h1>Relation Generator</h1>
<iframe  width="100%" height="40px auto" align="center" style="margin-top: 15px" src="../tools/time.html"></iframe>

<p>
<form action='../index.php'>
    <input type='submit' value='Back to Menu'/>
</form>
<div class="agile-its">
    <h2>ALF File Upload</h2>
    <div class="w3layouts">
        <p>Generate the DOT file based on ALF code.</p>
        <div class="photos-upload-view">
            <div id="messages">
                <p id="progress">
                    <script> document.getElementById("progress").innerText = "Processing..." </script>

                    <?php
                    try{
                        echo "<br>";
                        if (!isset($_FILES["file"])) {
                            showMessage("No File Selected.");
                            goto _exit;
                        }
                        if ($_FILES["file"]["error"] > 0) {

                            switch ($_FILES["file"]["error"]) {
                                case 2:
                                    showMessage("Size Limited!".CONTACT);
                                    break;
                                default:
                                    showMessage("Error: " . $_FILES["file"]["error"]);
                            }

                        } else {
                            if (! check_suffix("alf",$_FILES["file"]["name"])) throw new ErrorException("Suffix mismatch.");
                            showMessage("Upload: " . $_FILES["file"]["name"]);
                            showMessage("Size: " . ($_FILES["file"]["size"] / 1024) . " KB");
//                            showMessage("Path: " . $_FILES["file"]["tmp_name"]);
                            show_process_animation();
                            showMessage(LONG_RES);

                            $path = $_FILES["file"]["tmp_name"]; // 上传上来的为alf文件
                            $pPath = sys_get_temp_dir() . '/phptemp/' . session_id(); // 为当前session创建的处理文件夹，处理操作都在这个文件夹内完成
                            if (create_temp_folder_for_current_id()){
                                //$file = $pPath . '/' .$_FILES["file"]["name"];
                                $file = $pPath . '/' .$_FILES["file"]["name"];
                                // 将path 移动到$pPath中
                                if (move_uploaded_file($path,$file)){
                                    //$command = WCTG.' -i' . $file . " -t $pPath/dot &2>1";
                                    //$command = sweet."-i=$file –dot-print file=$path/dot g=cfg &2>1";
                                    $command = "sweet -i=$file func=_thrFunc0_ -c extref=off size=off -ae pu --dot-print file=$path g=cfg &2>1";
                                    my_exec(session_id(),$command);
                                    showMessage("See relat ion folder in files for more details.");
                                    show_home_btn();
                                }
                                else{
                                    showMessage(IOError);
                                }
                            }
                            else throw new ErrorException(IOError);
                        }
                    }catch (ErrorException $e){
                        showMessage($e->getMessage());
                    }
                    _exit:;
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

