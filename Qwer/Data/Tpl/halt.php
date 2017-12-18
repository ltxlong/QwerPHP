<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>错误提示</title>
    <style type="text/css">
        div.main{
            font-family: "微软雅黑";
            padding: 10px;
            margin-left: 30px;
        }
        div.pic{
            font-size: 125px;
            padding-bottom: 10px;
        }
        div.msg{
            font-size: 28px;
            margin-bottom: 30px;
        }
        div.info{
            font-size: 30px;
            margin-bottom: 10px;
        }
        div.title{
            font-weight: bold;
        }
        div.copyright{
            padding: 10px 45px;
            color: #aaaaaa;
            text-align: left;
        }
    </style>
</head>
<body>
<div class="main">
    <div class="pic">
        :(
    </div>
    <div class="msg">
        <?php echo $e['message']?>
    </div>
    <?php if(DEBUG && isset($e['file'])):?>
        <div class="info">
            <div class="title">
                错误位置：
            </div>
            <div class="path">
                FILE: [<?php echo $e['file'] . ':' . $e['line']?>]
            </div>
        </div>
    <?php endif ?>
    <?php if(isset($e['trace'])): ?>
        <div class="info">
            <div class="title">
                Trace
            </div>
            <div class="path">
                <?php echo nl2br($e['trace'])?>
            </div>
        </div>
    <?php endif ?>
</div>
<div class="copyright">
    <?php if(DEBUG):?>
        <b>Qwer框架(<?php echo QWER_VERSION ?>)</b>
    <?php endif ?>
</div>

</body>
</html>