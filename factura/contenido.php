
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Demo Responsive Design 980px</title>
<style>
#page {
    width: 980px;
    margin: 0 auto;
}
#header {
    height: 150px;
    background:#CCCCCC;
}
#content {
    width: 50%;
    float: left;
    background: yellow;
    min-height:150px;
}
#sidebar {
    width: 50%;
    float: right;
    background: green;
    min-height:150px;
}
#footer {
    clear: both;
    height: 50px;
    background: #FF9900;
}
@media screen and (max-width: 980px) {

    #page { width: 100%; }
    #content { width: 70%; }
    #sidebar {width: 30%; }
}

@media screen and (max-width: 700px) {

    #content, #sidebar {
        width: auto;
        float: none;
    }
}
</style>
<!--[if lt IE 9]>
<script src="http://css3-mediaqueries-js.googlecode.com/svn/trunk/css3-mediaqueries.js"></script>
<![endif]-->
</head>

<body>
<div id="page">
    
    <div id="content">
        <p>25/10/21 - Credicoop - $32.510.920,00 - 10052345</p>	
		<p>25/10/21 - Credicoop - $32.510.920,00 - 10052345</p>
        

    </div>
    <div id="sidebar"></div>
     
</div>
</body>
</html>