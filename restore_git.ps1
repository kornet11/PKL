<#
Jalankan dari PowerShell di folder C:\xampp\htdocs\PKL1
Jika Git for Windows terinstall, skrip akan mengembalikan semua file ke HEAD.
#>

if (Test-Path ".git") {
    Write-Output "Git repo ditemukan. Menjalankan restore ke HEAD..."
    git restore --source=HEAD --staged --worktree .
    git clean -fd
    Write-Output "Selesai. Semua perubahan kerja telah dikembalikan ke HEAD."
} else {
    Write-Output "Tidak menemukan folder .git. Jika tidak memakai git, pulihkan file dari cadangan manual."
    Write-Output "Contoh manual (PowerShell): Copy-Item -Path 'C:\backup\PKL1\*' -Destination 'C:\xampp\htdocs\PKL1' -Recurse -Force"
}