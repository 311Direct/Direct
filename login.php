<?php require('do/dologin.php'); ?>
<html>
  <head>
    <title>Login</title>
  </head>
  <body>
    <h1>Login</h1>
    <form name="loginForm" id="loginForm" action="login.php" method="post">
      <input type="text" name="u" placeholder="username" width="20" />
      <input type="password" name="p" placeholder="password" width="30" />
      <button type="submit" name="btnSubmit">Login</button>
    </form>
  </body>
</html>
