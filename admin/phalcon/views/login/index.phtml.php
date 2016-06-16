<!DOCTYPE html>
<html lang="zh-Hant-TW" class="no-js">
<head>
<title>Shoda</title>
<base href="/" />
<meta name="viewport" content="width=device-width; initial-scale=1.0; maximum-scale=1.0; user-scalable=0;">
<!-- Bootstrap  CSS -->
<link rel="stylesheet" href="http://cdn.bootcss.com/bootstrap/3.3.4/css/bootstrap.min.css">
<style>
BODY{
background: rgba(57, 97, 86, 1); /*#456A56*/
color: rgba(255,255,255,0.8);
}
#Login_form{
	width: 300px;
  right: 5%;
  position: absolute;
  bottom: 2%;
}

#Login_form LABEL{
  color:rgba(255,255,255,0.7);
  padding-right: 10px;
}
#Login_form INPUT{
  border: none;
  border-bottom: 1px solid rgba(0,0,0,0.175); 
  border-bottom:1px solid rgba(2552,255,255,0.175);
  background: none;
  color: rgba(255,255,255,0.8);
  text-align: right;
  box-shadow: none;
  border-radius:0;
}
#Login_form INPUT:HOVER{
  border-radius:4px;
  box-shadow: inset 0 1px 1px rgba(0,0,0,.075);
}
#Login_form BUTTON{
  border: none; 
  background: none;
  color: rgba(255,255,255,0.5);
}
#Login_form BUTTON:HOVER{
  color: rgba(255,255,255,0.8);
}
#Login_form BUTTON:ACTIVE{  
  color: rgba(255,255,255,0.175);
}
DISPLAY{
  color: rgba(0,0,0,0.175);	
}
</style>
</head>
<body>
<div id="Login_form" >

<form action="login/validate" id="recover" method="post">
        <div class="checkbox text-right">
          <label>
            <input type="checkbox" value="remember-me"> Remember me
          </label>
        </div>       
        <input type="text" name="username" class="form-control" placeholder="Your username" required="" autofocus="">       
        <input type="password" name="inputPassword" class="form-control" placeholder="Password" required="">        
        <button class="btn btn-lg btn-primary btn-block" type="submit">Sign in</button>
      </form>
<div></div>


</div>
</body>
</HTML>


