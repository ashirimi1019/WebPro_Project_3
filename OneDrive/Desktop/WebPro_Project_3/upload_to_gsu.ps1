# Upload Fixed Files to GSU Server
# Run this from your project directory

Write-Host "🚀 Uploading Fixed Files to GSU Server..." -ForegroundColor Green
Write-Host ""

# Server details
$server = "aimran6@codd.cs.gsu.edu"
$remotePath = "~/public_html/WebPro_Project_3"

# Files to upload
$files = @(
    @{local="js\puzzle.js"; remote="js/puzzle.js"; desc="Fixed tile rendering"},
    @{local="php\config.php"; remote="php/config.php"; desc="GSU database config"},
    @{local="test_db.php"; remote="test_db.php"; desc="Database test page"},
    @{local="GSU_DATABASE_SETUP.md"; remote="GSU_DATABASE_SETUP.md"; desc="Setup guide"},
    @{local="QUICK_FIX_GUIDE.md"; remote="QUICK_FIX_GUIDE.md"; desc="Quick fix guide"}
)

Write-Host "Files to upload:" -ForegroundColor Yellow
foreach ($file in $files) {
    Write-Host "  ✓ $($file.local) - $($file.desc)" -ForegroundColor Cyan
}
Write-Host ""

# Check if scp is available
try {
    $null = Get-Command scp -ErrorAction Stop
    Write-Host "✅ SCP command found" -ForegroundColor Green
} catch {
    Write-Host "❌ SCP not found. Please install OpenSSH or use WinSCP." -ForegroundColor Red
    Write-Host ""
    Write-Host "Alternative methods:" -ForegroundColor Yellow
    Write-Host "1. Use WinSCP (https://winscp.net/)"
    Write-Host "2. Use FileZilla (https://filezilla-project.org/)"
    Write-Host "3. Install OpenSSH: Settings → Apps → Optional Features → OpenSSH Client"
    Write-Host ""
    exit
}

# Confirm before uploading
Write-Host "Ready to upload to: $server" -ForegroundColor Yellow
$confirm = Read-Host "Continue? (y/n)"

if ($confirm -ne 'y') {
    Write-Host "Upload cancelled." -ForegroundColor Red
    exit
}

Write-Host ""
Write-Host "Uploading files..." -ForegroundColor Green
Write-Host "(You may be prompted for your GSU password)"
Write-Host ""

# Upload each file
$success = 0
$failed = 0

foreach ($file in $files) {
    Write-Host "Uploading $($file.local)..." -ForegroundColor Cyan
    
    $localFile = $file.local
    $remoteFile = "${remotePath}/$($file.remote)"
    
    try {
        # Use scp to upload
        scp $localFile "${server}:${remoteFile}" 2>&1 | Out-Null
        
        if ($LASTEXITCODE -eq 0) {
            Write-Host "  ✅ Success!" -ForegroundColor Green
            $success++
        } else {
            Write-Host "  ❌ Failed!" -ForegroundColor Red
            $failed++
        }
    } catch {
        Write-Host "  ❌ Error: $_" -ForegroundColor Red
        $failed++
    }
    
    Write-Host ""
}

# Summary
Write-Host "═══════════════════════════════════════" -ForegroundColor Yellow
Write-Host "Upload Summary:" -ForegroundColor Green
Write-Host "  ✅ Successful: $success" -ForegroundColor Green
Write-Host "  ❌ Failed: $failed" -ForegroundColor $(if($failed -gt 0){"Red"}else{"Green"})
Write-Host "═══════════════════════════════════════" -ForegroundColor Yellow
Write-Host ""

if ($success -eq $files.Count) {
    Write-Host "🎉 All files uploaded successfully!" -ForegroundColor Green
    Write-Host ""
    Write-Host "Next Steps:" -ForegroundColor Yellow
    Write-Host "1. SSH to server: ssh $server" -ForegroundColor Cyan
    Write-Host "2. Edit config: nano $remotePath/php/config.php" -ForegroundColor Cyan
    Write-Host "   (Add your database password on line 12)" -ForegroundColor Gray
    Write-Host "3. Setup database: mysql -u aimran6 -p" -ForegroundColor Cyan
    Write-Host "4. Test connection: https://codd.cs.gsu.edu/~aimran6/WebPro_Project_3/test_db.php" -ForegroundColor Cyan
    Write-Host "5. Play game: https://codd.cs.gsu.edu/~aimran6/WebPro_Project_3/index.html" -ForegroundColor Cyan
    Write-Host ""
} else {
    Write-Host "⚠️ Some files failed to upload." -ForegroundColor Red
    Write-Host "Please check your connection and try again." -ForegroundColor Yellow
    Write-Host ""
}

Write-Host "Press any key to exit..."
$null = $Host.UI.RawUI.ReadKey("NoEcho,IncludeKeyDown")
