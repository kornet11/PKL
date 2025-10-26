#!/usr/bin/env bash
# Jalankan dari folder project: c:\xampp\htdocs\PKL1

# Jika repo Git tersedia, reset semua file ke HEAD (hard reset untuk working tree)
if [ -d ".git" ]; then
  echo "Git repo ditemukan. Melakukan 'git restore' ke HEAD untuk semua file..."
  git restore --source=HEAD --staged --worktree .
  git clean -fd
  echo "Selesai. Semua perubahan kerja telah dikembalikan ke HEAD."
else
  echo "Tidak menemukan .git. Skrip ini membutuhkan repo Git. Gunakan restore_manual.ps1 atau pulihkan dari backup."
fi