<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    // SweetAlert untuk button logout
    // Ambil semua elemen dengan class btn-logout
    document.querySelectorAll('.btn-logout').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault(); // Mencegah tautan langsung
            const href = this.getAttribute('href'); // Ambil tautan href

            Swal.fire({
                title: 'Konfirmasi Logout',
                text: "Apakah Anda yakin ingin logout?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, Logout',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = href;
                }
            });
        });
    });
</script>