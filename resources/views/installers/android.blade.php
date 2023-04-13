<style>
  #androidlink{
    display: none;
  }
  .grad  {
    background-image: linear-gradient(to top right,#146daf, #79bc79);
  }
</style>
<html xmlns="http://www.w3.org/1999/xhtml">
  <head>
    <title>Whagons {{ env('ORGANIZATION_NAME', ' ') }} - Android </title>
    <meta charset="utf-8">
  </head>
  <body class="grad">
    <img src="https://dingdonecdn.nyc3.digitaloceanspaces.com/general/whagons3/logo_white.png" alt="DingDone">
      <h1 style="color: white">Whagons {{ env('ORGANIZATION_NAME', ' ') }} - Android  </h1>
      <a id="androidlink" href="/install/android.apk"></a>
  </body>
</html>
<script>
  document.getElementById('androidlink').click();
</script>
