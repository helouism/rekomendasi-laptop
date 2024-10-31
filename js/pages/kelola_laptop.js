$(document).ready(function () {
  // formatter untuk harga
  function formatPrice(price) {
    return new Intl.NumberFormat("id-ID", {
      style: "currency",
      currency: "IDR",
      minimumFractionDigits: 0,
      maximumFractionDigits: 0,
    })
      .format(price)
      .replace("IDR", "Rp")
      .replace(/\s+/g, " ");
  }

  // STYLING UNTUK KATEGORI
  function getCategoryBadge(category) {
    const badges = {
      Student: "badge-info",
      Designer: "badge-success",
      Gamer: "badge-danger",
    };
    const icons = {
      Student: "fa-graduation-cap",
      Designer: "fa-paint-brush",
      Gamer: "fa-gamepad",
    };
    return `<span class="badge ${badges[category]} px-3 py-2">
                    <i class="fas ${icons[category]} mr-1"></i> ${category}
                </span>`;
  }

  $("#laptopTable").DataTable({
    columnDefs: [
      {
        targets: 3, // Category column
        render: function (data, type, row) {
          return getCategoryBadge(data);
        },
      },
      {
        targets: 4, // Price column
        render: function (data, type, row) {
          if (type === "display") {
            return `<div class="price-tag">
                            <span class="amount">${formatPrice(data)}</span>
                       </div>`;
          }
          return data;
        },
      },
    ],
  });

  // Custom CSS
  $("<style>")
    .text(
      `
            /* Category badges */
            .badge {
                font-size: 0.85rem;
                font-weight: 500;
                letter-spacing: 0.3px;
                transition: all 0.2s ease;
            }
            .badge:hover {
                transform: translateY(-1px);
                box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            }
            .badge-info {
                background-color: #36b9cc;
                color: white;
            }
            .badge-success {
                background-color: #1cc88a;
                color: white;
            }
            .badge-danger {
                background-color: #e74a3b;
                color: white;
            }
            
            /* Price styling */
           
        .price-tag {
            display: inline-flex;
            align-items: baseline;
            font-family: 'Fira Sans', sans-serif;
        }
        .price-tag .amount {
            color: #5a5c69;
            font-size: 1rem;
            font-weight: 600;
        }
            
            /* Table cell alignment */
            #laptopTable td:nth-child(4) {  /* Category column */
                text-align: center;
                vertical-align: middle;
            }
            #laptopTable td:nth-child(5) {  /* Price column */
                text-align: right;
                vertical-align: middle;
            }
        `
    )
    .appendTo("head");

  $("#laptopTable").DataTable();

  // Submit Form Tambah Laptop
  $("#addLaptopForm").submit(function (e) {
    e.preventDefault();
    var formData = new FormData(this);

    // Log form data
    for (var pair of formData.entries()) {
      console.log(pair[0] + ": " + pair[1]);
    }

    $.ajax({
      url: "kelola_laptop.php",
      type: "POST",
      data: formData,
      contentType: false,
      processData: false,
      success: function (response) {
        console.log("Raw server response:", response);
        var result = response;

        if (result.status === "success") {
          Swal.fire("Berhasil", result.message, "success").then(() => {
            location.reload();
          });
        } else {
          Swal.fire(
            "Error",
            result.message || "An unknown error occurred",
            "error"
          );
        }
      },
      error: function (xhr, status, error) {
        console.error("AJAX error:", status, error);
        console.log("Response Text:", xhr.responseText);
        console.log("Status Code:", xhr.status);
        Swal.fire("Error", "Terjadi kesalahan", "error");
      },
    });
  });

  // Submit form edit laptop
  $("#editLaptopForm").submit(function (e) {
    e.preventDefault();
    var formData = new FormData(this);

    // Log form data for debugging
    for (var pair of formData.entries()) {
      console.log(pair[0] + ": " + pair[1]);
    }

    $.ajax({
      url: "kelola_laptop.php",
      type: "POST",
      data: formData,
      contentType: false,
      processData: false,
      success: function (response) {
        console.log("Raw server response:", response);
        try {
          var result =
            typeof response === "string" ? JSON.parse(response) : response;
          console.log("Parsed result:", result);
          if (result.status === "success") {
            Swal.fire("Berhasil", result.message, "success").then(() => {
              location.reload();
            });
          } else {
            console.error("Server returned error:", result.message);
            Swal.fire(
              "Error",
              result.message || "An unknown error occurred",
              "error"
            );
          }
        } catch (e) {
          console.error("Error parsing JSON:", e);
          console.log("Response that caused error:", response);
          if (
            typeof response === "string" &&
            response.toLowerCase().includes("success")
          ) {
            Swal.fire(
              "Berhasil",
              "Edit berhasil, namun terjadi kesalahan pada response.",
              "success"
            ).then(() => {
              location.reload();
            });
          } else {
            Swal.fire(
              "Error",
              "Invalid response from server. Check console for details.",
              "error"
            );
          }
        }
      },
      error: function (xhr, status, error) {
        console.error("AJAX error:", status, error);
        console.log("Response Text:", xhr.responseText);
        console.log("Status Code:", xhr.status);
        Swal.fire("Error", "Terjadi kesalahan", "error");
      },
    });
  });

  // ISI DATA EDIT LAPTOP MODAL
  $(document).on("click", ".edit-laptop", function () {
    var laptopData = $(this).data("laptop");
    console.log("Clicked laptop data:", laptopData);

    // Clear previous data
    $("#editLaptopForm")[0].reset();

    // Populate form fields
    $("#edit_id_laptop").val(laptopData.id_laptop);
    $("#edit_brand_name").val(laptopData.brand_name);
    $("#edit_model_name").val(laptopData.model_name);
    $("#edit_processor").val(laptopData.processor);
    $("#edit_operating_system").val(laptopData.operating_system);
    $("#edit_graphics").val(laptopData.graphics);
    $("#edit_ram").val(laptopData.ram);
    $("#edit_screen_size").val(laptopData.screen_size);
    $("#edit_internal_storage").val(laptopData.internal_storage);
    $("#edit_category").val(laptopData.category);
    $("#edit_price").val(laptopData.price);

    // Log populated values for debugging
    console.log("Populated values:", {
      id_laptop: $("#edit_id_laptop").val(),
      brand_name: $("#edit_brand_name").val(),
      model_name: $("#edit_model_name").val(),
      processor: $("#edit_processor").val(),
      operating_system: $("#edit_operating_system").val(),
      graphics: $("#edit_graphics").val(),
      ram: $("#edit_ram").val(),
      screen_size: $("#edit_screen_size").val(),
      internal_storage: $("#edit_internal_storage").val(),
      category: $("#edit_category").val(),
      price: $("#edit_price").val(),
    });
  });

  $("#addLaptopModal")
    .on("show.bs.modal", function () {
      $(this).removeAttr("inert");
    })
    .on("hide.bs.modal", function () {
      $(this).attr("inert", "");
    });

  // Handle delete button click
  $(document).on("click", ".delete-laptop", function () {
    var laptopId = $(this).data("id");
    Swal.fire({
      title: "Apakah kamu yakin?",
      text: "Yakin ingin menghapus laptop ini ?",
      icon: "warning",
      showCancelButton: true,
      confirmButtonColor: "#3085d6",
      cancelButtonColor: "#d33",
      cancelButtonText: "Tidak",
      confirmButtonText: "Ya!",
    }).then((result) => {
      if (result.isConfirmed) {
        $.ajax({
          url: "kelola_laptop.php",
          type: "POST",
          data: {
            action: "delete_laptop",
            id_laptop: laptopId,
          },
          success: function (response) {
            console.log("Raw server response:", response);
            try {
              var result =
                typeof response === "string" ? JSON.parse(response) : response;
              console.log("Parsed result:", result);
              if (result.status === "success") {
                Swal.fire("Berhasil", result.message, "success").then(() => {
                  location.reload();
                });
              } else {
                console.error("Server returned error:", result.message);
                Swal.fire(
                  "Error",
                  result.message || "An unknown error occurred",
                  "error"
                );
              }
            } catch (e) {
              console.error("Error parsing JSON:", e);
              console.log("Response that caused error:", response);
              if (
                typeof response === "string" &&
                response.toLowerCase().includes("success")
              ) {
                Swal.fire(
                  "Berhasil",
                  "Hapus berhasil, namun terjadi kesalahan pada response",
                  "success"
                ).then(() => {
                  location.reload();
                });
              } else {
                Swal.fire("Error", "Invalid response dari server.", "error");
              }
            }
          },
          error: function (xhr, status, error) {
            console.error("AJAX error:", status, error);
            console.log("Response Text:", xhr.responseText);
            console.log("Status Code:", xhr.status);
            Swal.fire("Error", "Terjadi kesalahan.", "error");
          },
        });
      }
    });
  });
});
