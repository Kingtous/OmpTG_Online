<?php include_once "process/function.php"?>
<!DOCTYPE html>
<html>
<head>
    <title>OMPi Generator</title>
    <link rel="stylesheet" href="css/style.css">
    <link href='//fonts.googleapis.com/css?family=Open+Sans:400,300italic,300,400italic,600,600italic,700,700italic,800,800italic'
          rel='stylesheet' type='text/css'>
    <link href='//fonts.googleapis.com/css?family=Montserrat:400,700' rel='stylesheet' type='text/css'>

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
<div class="agile-its">
    <h2>OpenMP C File Upload</h2>
    <div class="w3layouts">
        <strong>Convert OpenMP C Code into trimmed OMPi C Code using custom OMPi tools and Trimming Program</strong>
        <br></br>
        <p>Please upload the .zip file which contains dependencies for the code to be analyzed (≤1MB)</p>
        <div class="photos-upload-view">
            <form id="upload" name="form_upload" method="POST" enctype="multipart/form-data" action="process/ompi_show.php">
                <input type="hidden" id="MAX_FILE_SIZE" name="MAX_FILE_SIZE" value="1024000"/>
                <div class="agileinfo">
                    <input type="submit" class="choose" accept=".zip" value="Choose .zip File.." >
                    <h3>OR</h3>
                    <input type="file" id="fileselect" name="file" onchange="document.getElementsByName('file_submit')[0].removeAttribute('disabled');"/>
<!--                    <div id="filedrag">drag and drop file here</div>-->
                    <input type="submit" name="file_submit" value="start" disabled="disabled" onclick="
                          this.value='processing...';
                          this.disabled=true;
                          document.form_upload.submit();

                "/>
                </div>
            </form>
            <div id="messages">
                <p id="messages_tip">File Detail will be shown here</p>
            </div>

        </div>
        <div class="clearfix"></div>
<!--        未来添加拖拽功能-->
        <script src="js/filedrag.js"></script>
    </div>
</div>
<div class="footer">
    <?php  show_home_btn(); ?>
    <p> © 2019 OmpTG Online. All Rights Reserved</a></p>
</div>

<script type="text/javascript" src="js/jquery.min.js"></script>

</div>
</body>
</html>