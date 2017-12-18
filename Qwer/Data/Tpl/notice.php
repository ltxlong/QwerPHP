<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>错误提示</title>
    <style type="text/css">
        div.notice{
            border: 1px solid #990000;
            padding-left: 20px;
            margin: 10px;
        }
        div.notice h3{
            font-size: 18px;
            margin: 20px 0;
        }
        div.notice p{
            font-size: 14px;
            margin: 15px 0;
        }
    </style>
</head>
<body>
<div class="notice">
    <h3 style="font-size: 18px;"><?php echo $error ?></h3>
    <p>
        Severity: <?php echo $errno ?>
    </p>
    <p>
        File: <?php echo $file ?>
    </p>
    <p>
        Line: <?php echo $line ?>
    </p>
    <p style="color: #099">
        Qwer框架（<?php echo QWER_VERSION ?>）
    </p>
</div>
</div>
</body>
</html>