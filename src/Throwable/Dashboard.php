<!DOCTYPE html>
<html>
<head>
    <meta charset='utf-8'>
    <title><?php echo !empty($throwable['type']) ? $throwable['type'] : 'Badaboo...'; ?></title>
    <meta charset='utf-8'>
    <meta name='robots' content='no-index, no-follow' >
    <meta name='viewport' content='width=device-width, initial-scale=1'>
    <style><?php echo $style; ?></style>
</head>
<body>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Code</th>
                <th>Type</th>
                <th>Message</th>
                <th>File</th>
                <th>Line</th>
            </tr>
        </thead>
        <tbody>
        <?php if (!empty($throwable)) : ?>
            <tr>
                <td class=""><span class=""><?php echo $throwable['code']; ?></span></td>
                <td class=""><span class=""><?php echo $throwable['type']; ?></span></td>
                <td class=""><span class=""><?php echo $throwable['message']; ?></span></td>
                <td class=""><span class=""><?php echo $throwable['file']; ?></span></td>
                <td class=""><span class=""><?php echo $throwable['line']; ?></span></td>
            </tr>
        <?php endif; ?>
        </tbody>
    </table>
</body>
</html>
