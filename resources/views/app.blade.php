<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0" />
    <link rel="icon" type="image/png" href="{{asset('assets/images/logos/qr-logo.png')}}" />
    <style>
      body {
        padding: 0;
        margin: 0;
        box-sizing: border-box
        
      }
    </style>
    <title>QR CONNECT</title>
    @vite('resources/js/app.js')
    @inertiaHead
  </head>
  <body>
    @inertia
  </body>
</html