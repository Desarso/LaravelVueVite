<style>
  .grad  {
    background-image: linear-gradient(to top right,#146daf, #79bc79);
  }

  #androidlink {
    display: none;
  }
</style>
<html xmlns="http://www.w3.org/1999/xhtml">
  <head>
    <title>Whagons {{ env('ORGANIZATION_NAME', ' ') }} - IOS </title>
    <meta charset="utf-8">
  </head>
  <body class="grad">
    <img src="https://dingdonecdn.nyc3.digitaloceanspaces.com/general/whagons3/logo_white.png" alt="DingDone"/>
    <h1 style="color: white">Whagons {{ env('ORGANIZATION_NAME', ' ') }} - IOS  </h1>
    <a id="androidlink" href="itms-services://?action=download-manifest&url=https://dingdonecdn.nyc3.digitaloceanspaces.com/IOS/demov3/manifest.plist"></a>
  </body>
</html>
<script>
  document.getElementById('androidlink').click();
</script>

