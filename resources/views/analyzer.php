<!doctype html>
<html>
<head>
    <title>Slide Analyzer</title>
    <style>
        body {
            background-color: black;
            color: white;
        }
    </style>
</head>
<body>
<h1>Analyze a Slide</h1>
<div>
Uploaded slide:<br/> <img src="<?php echo $image . "?h=400"; ?>" />
</div>
<div>
50% Brightness:<br/>  <img src="<?php echo $image . "?h=400&bri=-50"; ?>" />
</div>
<div>
50% Contrast:<br/>  <img src="<?php echo $image . "?h=400&con=-50"; ?>" />
</div>
<div>
50% Brightness / 50% Contrast:<br/>  <img src="<?php echo $image . "?h=400&bri=-50&con=-50"; ?>" />
</div>
<div>
    Gamma 5:<br/>  <img src="<?php echo $image . "?h=400&gam=5.0"; ?>" />
</div>
<div>
    Aspect ratio when displayed on 1280x800 projector (1.6):<br/> <img src="<?php echo $image . "?w=640&h=400&fit=stretch"; ?>" />
</div>
<div>
    Aspect ratio when displayed on 1920x1080 projector (1.777):<br/> <img src="<?php echo $image . "?w=640&h=360&fit=stretch"; ?>" />
</div>
<div>
    Aspect ratio when displayed on 1024x768 projector (1.333):<br/> <img src="<?php echo $image . "?w=512&h=384&fit=stretch"; ?>" />
</div>

</body>
</html>