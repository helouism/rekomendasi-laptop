$(document).ready(function () {
  // Custom renderer for the role column
  function getRoleBadge(role) {
    return role === "admin"
      ? `<span class="badge badge-danger px-3 py-2">Admin</span>`
      : `<span class="badge badge-info px-3 py-2">User</span>`;
  }

  var usersTable = $("#usersTable").DataTable({
    ajax: {
      url: "get_users.php",
      dataSrc: "",
    },
    columns: [
      {
        data: "id_user",
        width: "8%",
        className: "text-center align-middle font-weight-bold",
      },
      {
        data: "username",
        width: "25%",
        className: "align-middle",
        render: function (data, type, row) {
          return `<div class="d-flex align-items-center">
                      <div class="icon-circle bg-primary text-white mr-3">
                        <i class="fas fa-user"></i>
                      </div>
                      <div>
                        <span class="font-weight-bold">${data}</span>
                      </div>
                    </div>`;
        },
      },
      {
        data: "role",
        width: "5%",
        className: "text-center align-middle",
        render: function (data, type, row) {
          return getRoleBadge(data);
        },
      },
      {
        data: null,
        width: "15%",
        className: "text-center align-middle",
        render: function (data, type, row) {
          return `<span class="text-muted"><i class="fas fa-lock mr-1"></i>Password ter-enkripsi</span>`;
        },
      },
      {
        data: null,
        width: "5%",
        className: "text-center align-middle",
        orderable: false,
        render: function (data, type, row) {
          if (type === "display") {
            return `
                <div class="d-flex justify-content-center">
                  <button class="btn btn-primary btn-circle btn-sm edit-user mx-1" 
                          data-id="${row.id_user}" 
                          data-username="${row.username}" 
                          data-role="${row.role}"
                          data-toggle="tooltip"
                          title="Edit User">
                    <i class="fas fa-edit"></i>
                  </button>
                  <button class="btn btn-danger btn-circle btn-sm delete-user mx-1" 
                          data-id="${row.id_user}"
                          data-toggle="tooltip"
                          title="Delete User">
                    <i class="fas fa-trash"></i>
                  </button>
                </div>`;
          }
          return null;
        },
      },
    ],
    responsive: true,
    autoWidth: false,
    dom:
      '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>' +
      '<"row"<"col-sm-12"tr>>' +
      '<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
    language: {
      emptyTable:
        '<div class="text-center my-5"><i class="fas fa-users fa-4x text-gray-400 mb-3"></i><br>Tidak ada data user</div>',
      info: "Showing _START_ to _END_ of _TOTAL_ users",
      lengthMenu: "Show _MENU_ users per page",
      search: "",
      searchPlaceholder: "Cari user...",
      paginate: {
        first: '<i class="fas fa-angle-double-left"></i>',
        last: '<i class="fas fa-angle-double-right"></i>',
        next: '<i class="fas fa-angle-right"></i>',
        previous: '<i class="fas fa-angle-left"></i>',
      },
    },
    drawCallback: function () {
      // Initialize tooltips
      $('[data-toggle="tooltip"]').tooltip();
    },
  });

  // STYLING UNTUK SEARCH INPUT
  $(".dataTables_filter input").addClass("form-control form-control-sm").css({
    width: "250px",
    display: "inline-block",
    "margin-left": "10px",
  });

  // Add custom length menu styling
  $(".dataTables_length select").addClass("form-control form-control-sm");

  // CUSTOM STYLE
  $("#usersTable tbody tr:odd").addClass("bg-light");

  // BOX SHADOW SAAT HOVER
  $("<style>")
    .text(
      `
        #usersTable tbody tr {
          transition: all 0.2s ease;
        }
        #usersTable tbody tr:hover {
          box-shadow: 0 3px 10px rgba(0,0,0,0.1);
          transform: translateY(-1px);
        }
        .icon-circle {
          height: 40px;
          width: 40px;
          border-radius: 100%;
          display: flex;
          align-items: center;
          justify-content: center;
        }
        .badge {
          font-size: 85%;
          font-weight: 500;
          transition: all 0.2s ease;
        }
        .badge:hover {
          transform: scale(1.05);
        }
        .btn-circle {
          width: 35px;
          height: 35px;
          padding: 6px 0px;
          border-radius: 50%;
          text-align: center;
          transition: all 0.2s ease;
        }
        .btn-circle:hover {
          transform: scale(1.1);
        }
      `
    )
    .appendTo("head");

  $("#addUserBtn").on("click", function () {
    Swal.fire({
      title: "Tambah User Baru",
      html: `
        <div class="form-group mb-3">
          <label for="add-username" class="form-label">Username</label>
          <input type="text" id="add-username" class="form-control" maxlength="50" placeholder="Enter username">
        </div>
        <div class="form-group mb-3">
          <label for="add-password" class="form-label">Password</label>
          <input type="password" id="add-password" class="form-control" placeholder="Enter password">
        </div>
        <div class="form-group mb-3">
          <label for="add-confirm-password" class="form-label">Confirm Password</label>
          <input type="password" id="add-confirm-password" class="form-control" placeholder="Confirm password">
        </div>
        <div class="form-group mb-3">
          <label for="add-role" class="form-label">Role</label>
          <select id="add-role" class="form-control">
            <option value="user">User</option>
            <option value="admin">Admin</option>
          </select>
        </div>
      `,
      showCancelButton: true,
      confirmButtonText: "Simpan",
      confirmButtonColor: "#4e73df",
      cancelButtonText: "Batal",
      cancelButtonColor: "#858796",
      focusConfirm: false,
      preConfirm: () => {
        const username = $("#add-username").val();
        const password = $("#add-password").val();
        const confirmPassword = $("#add-confirm-password").val();

        // Validation
        if (!username) {
          Swal.showValidationMessage("Username is required");
          return false;
        }
        if (!password) {
          Swal.showValidationMessage("Password is required");
          return false;
        }
        if (password !== confirmPassword) {
          Swal.showValidationMessage("Passwords do not match");
          return false;
        }
        if (password.length < 8) {
          Swal.showValidationMessage("Password harus 8 karakter atau lebih");
          return false;
        }

        return {
          username: username,
          password: password,
          role: $("#add-role").val(),
        };
      },
    }).then((result) => {
      if (result.isConfirmed) {
        $.ajax({
          url: "add_user.php",
          method: "POST",
          data: result.value,
          success: function (response) {
            try {
              const res = JSON.parse(response);
              if (res.success) {
                Swal.fire({
                  icon: "success",
                  title: "Success!",
                  text: "User berhasil ditambahkan.",
                  confirmButtonColor: "#4e73df",
                }).then(() => {
                  usersTable.ajax.reload();
                });
              } else {
                Swal.fire({
                  icon: "error",
                  title: "Error!",
                  text: res.message || "Gagal menambahkan user.",
                  confirmButtonColor: "#4e73df",
                });
              }
            } catch (e) {
              Swal.fire({
                icon: "error",
                title: "Error!",
                text: "An unexpected error occurred.",
                confirmButtonColor: "#4e73df",
              });
            }
          },
          error: function () {
            Swal.fire({
              icon: "error",
              title: "Error!",
              text: "Failed to connect to the server.",
              confirmButtonColor: "#4e73df",
            });
          },
        });
      }
    });
  });

  // EDIT USER
  $("#usersTable tbody").on("click", ".edit-user", function () {
    const id = $(this).data("id");
    const username = $(this).data("username");

    Swal.fire({
      title: "Edit User",
      html: `
          <input type="hidden" id="edit-id" value="${id}">
          <div class="form-group mb-3">
            <label for="edit-username" class="form-label">Username</label>
            <input type="text" id="edit-username" class="form-control" value="${username}" maxlength="50">
          </div>
        
          <div class="form-group mb-3">
            <label for="edit-password" class="form-label">Password Baru</label>
            <input type="password" id="edit-password" class="form-control">
          </div>
        `,
      showCancelButton: true,
      confirmButtonText: "Simpan",
      confirmButtonColor: "#4e73df",
      cancelButtonText: "Batal",
      cancelButtonColor: "#858796",
      focusConfirm: false,
      preConfirm: () => {
        const username = $("#edit-username").val();
        if (!username) {
          Swal.showValidationMessage("Username cannot be empty");
          return false;
        }
        return {
          id_user: $("#edit-id").val(),
          username: username,

          password: $("#edit-password").val(),
        };
      },
    }).then((result) => {
      if (result.isConfirmed) {
        $.ajax({
          url: "update_user.php",
          method: "POST",
          data: result.value,
          success: function (response) {
            try {
              const res = JSON.parse(response);
              if (res.success) {
                Swal.fire({
                  icon: "success",
                  title: "Success!",
                  text: "User berhasil diperbarui.",
                  confirmButtonColor: "#4e73df",
                }).then(() => {
                  usersTable.ajax.reload();
                });
              } else {
                Swal.fire({
                  icon: "error",
                  title: "Error!",
                  text: res.message || "Gagal memperbarui user.",
                  confirmButtonColor: "#4e73df",
                });
              }
            } catch (e) {
              Swal.fire({
                icon: "error",
                title: "Error!",
                text: "An unexpected error occurred.",
                confirmButtonColor: "#4e73df",
              });
            }
          },
          error: function () {
            Swal.fire({
              icon: "error",
              title: "Error!",
              text: "Failed to connect to the server.",
              confirmButtonColor: "#4e73df",
            });
          },
        });
      }
    });
  });

  // HAPUS USER
  $("#usersTable tbody").on("click", ".delete-user", function () {
    const id = $(this).data("id");

    Swal.fire({
      title: "Apakah kamu yakin?",
      text: "Data ini akan dihapus secara permanen!",
      icon: "warning",
      showCancelButton: true,
      confirmButtonColor: "#d33",
      cancelButtonColor: "#3085d6",
      confirmButtonText: "Ya hapus!",
      cancelButtonText: "Batal",
    }).then((result) => {
      if (result.isConfirmed) {
        $.ajax({
          url: "delete_user.php",
          method: "POST",
          data: { id_user: id },
          success: function (response) {
            try {
              const res = JSON.parse(response);
              if (res.success) {
                Swal.fire({
                  icon: "success",
                  title: "Berhasil!",
                  text: "User berhasil dihapus.",
                  confirmButtonColor: "#4e73df",
                }).then(() => {
                  usersTable.ajax.reload();
                });
              } else {
                Swal.fire({
                  icon: "error",
                  title: "Error!",
                  text: res.message || "Gagal Menghapus User.",
                  confirmButtonColor: "#4e73df",
                });
              }
            } catch (e) {
              Swal.fire({
                icon: "error",
                title: "Error!",
                text: "An unexpected error occurred.",
                confirmButtonColor: "#4e73df",
              });
            }
          },
          error: function () {
            Swal.fire({
              icon: "error",
              title: "Error!",
              text: "Failed to connect to the server.",
              confirmButtonColor: "#4e73df",
            });
          },
        });
      }
    });
  });
});
