<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <form action="<?php echo site_url('persona/carga_masiva') ?>" method="post" enctype="multipart/form-data">
    <input type="file" name="mi_archivo">
    <button type="sumit">Subir</button>
    </form>
</body>
</html>