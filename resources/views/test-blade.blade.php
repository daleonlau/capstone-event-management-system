<!DOCTYPE html>
<html>
<head>
    <title>Test Blade</title>
</head>
<body>
    <h1>Test Blade View</h1>
    <p>If you can see this, Laravel is serving views correctly.</p>
    <p>Current time: {{ now() }}</p>
    <p>App URL: {{ config('app.url') }}</p>
</body>
</html>