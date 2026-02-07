<?php
// Define the target directory and URL for redirection
$targetDir = '/var/www/dashboard';
$redirectUrl = 'index.php'; // Change to your main page URL
$success = false;
$output = "";

if (isset($_POST['update_dashboard'])) {
    // SECURITY WARNING: Ensure the www-data user has NOPASSWD sudo access for the specific commands in /etc/sudoers
    
    $commands = [
    "sudo su",
    "sudo chown -R www-data:www-data /var/www",
    "sudo chown -R www-data:www-data /usr/local",
    "sudo rm -rf /var/www/dashboard",
    "sudo rm -rf /usr/local/bin",
    "sudo rm -rf /usr/local/sbin",
    "cd /var/www && sudo git clone https://github.com/dmrcz/ODS.DMRCZ-DASH-2026.git /var/www/dashboard",
    "cd /usr/local && sudo git clone https://github.com/dmrcz/ODS.DMRCZ-BIN-2026.git /usr/local/bin",
    "cd /usr/local && sudo git clone https://github.com/dmrcz/ODS.DMRCZ-SBIN-2026.git /usr/local/sbin",
    "sudo chown -R www-data:www-data /var/www/dashboard /usr/local/bin /usr/local/sbin"

    ];

    foreach ($commands as $cmd) {
        // Use exec for better control over output and return status
        exec("$cmd 2>&1", $cmdOutput, $returnValue);
        $output .= "Command: $cmd\n";
        $output .= "Output: " . implode("\n", $cmdOutput) . "\n";
        $output .= "Return Value: $returnValue\n\n";

        if ($returnValue !== 0) {
            $output .= "ERROR: Command failed, stopping update process.\n";
            break;
        }
    }

    if ($returnValue === 0) {
        $success = true;
        $message = "DASHBOARD USPESNE AKTUALIZOVANY! PRESMEROVANI NA DASHBOARD ZA 10 SEKUND...";
        // HTML meta refresh for redirect after output
        echo "<meta http-equiv='refresh' content='10;url=$redirectUrl'>";
    } else {
        $message = "AKTUALIZACE DASHBOARDU SE NEZDARILA !!!.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Update Dashboard</title>
</head>
<body>
    <h1>Update Dashboard</h1>

    <?php if (isset($message)): ?>
        <p style="color: <?= $success ? 'green' : 'red'; ?>;">
            <?= $message; ?>
        </p>
        <?php if (!$success): ?>
            <pre><?= htmlspecialchars($output); ?></pre>
        <?php endif; ?>
    <?php endif; ?>

    <?php if (!$success): ?>
        <form method="post" action="">
            <input type="submit" name="update_dashboard" value="Aktualizovat">
        </form>
    <?php endif; ?>

</body>
</html>

