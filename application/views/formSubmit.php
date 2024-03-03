<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <p><?php echo $redirectURI; ?></p>
    <script defer>window.location.replace("<?php $redirectURI; ?>");</script>
    <!-- <script defer>window.location.replace("http://stackoverflow.com");</script> -->
    <?php echo "Loaded"; ?>
</body>
</html>