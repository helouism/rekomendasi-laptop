// Konfirmasi logout

function confirmLogout() {
  Swal.fire({
    title: "Yakin ingin keluar?",
    text: "Apakah kamu yakin ingin keluar?",
    icon: "warning",
    showCancelButton: true,
    confirmButtonColor: "#3085d6",
    cancelButtonColor: "#d33",
    confirmButtonText: "Ya",
    cancelButtonText: "Tidak",
  }).then((result) => {
    if (result.isConfirmed) {
      window.location.href = "logout.php";
    }
  });
}
