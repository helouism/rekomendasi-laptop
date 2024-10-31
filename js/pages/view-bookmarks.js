var bookmarksTable;
$(document).ready(function () {
  $.fn.dataTable.ext.type.order["num-fmt-pre"] = function (data) {
    return parseFloat(data.replace(/[^\d.-]/g, ""));
  };

  $.ajax({
    url: "view-bookmarks-data.php",
    method: "GET",
    dataType: "json",
    success: function (response) {
      // console.log('Raw AJAX response:', response);
      if (response.error) {
        // console.error('Server error:', response.error);
        Swal.fire({
          icon: "error",
          title: "Error",
          text: response.error,
        });
      } else {
        initializeDataTable(response);
      }
    },
    error: function (xhr, status, error) {
      //console.error('AJAX error:', status, error);
      //console.log('Response text:', xhr.responseText);

      Swal.fire({
        icon: "error",
        title: "Error",
        text: "Terjadi kesalahan, cek konsol untuk melihat pesan error",
      });
    },
  });

  function initializeDataTable(data) {
    bookmarksTable = $("#bookmarksTable").DataTable({
      ajax: {
        url: "view-bookmarks-data.php",
        dataSrc: "",
      },
      columns: [
        {
          data: "image_url",
          render: function (data, type, row) {
            return `<img src="${data}" alt="${row.brand_name} ${row.model_name}" style="max-width: 100px; height: auto;">`;
          },
        },
        { data: "brand_name" },
        { data: "model_name" },
        {
          data: "price",
          type: "num-fmt",
          render: function (data, type, row) {
            return `IDR ${new Intl.NumberFormat("id-ID").format(data)}`;
          },
        },
        {
          data: null,
          render: function (data, type, row) {
            return `
                    <button class="btn btn-primary btn-sm view-details" data-laptop='${JSON.stringify(
                      row
                    )}'><i class="fas fa-eye"></i></button>
                    <button class="btn btn-danger btn-sm remove-bookmark" data-laptop-id="${
                      row.id_laptop
                    }"><i class="fas fa-trash"></i></button>
                `;
          },
        },
      ],
      order: [[3, "asc"]], // Sort by price column (index 3) in ascending order by default
    });
  }

  // Handler untuk button view details
  $("#bookmarksTable").on("click", ".view-details", function () {
    const laptopData = JSON.parse($(this).attr("data-laptop"));
    const detailsHtml = `
    <div class="row">
        <div class="col-md-4">
            <img src="${laptopData.image_url}" alt="${laptopData.brand_name} ${
      laptopData.model_name
    }" class="img-fluid">
        </div>
        <div class="col-md-8">
            <h4>${laptopData.brand_name} ${laptopData.model_name}</h4>
            <p><strong>Processor:</strong> ${laptopData.processor}</p>
            <p><strong>Operating System:</strong> ${
              laptopData.operating_system
            }</p>
            <p><strong>Graphics:</strong> ${laptopData.graphics}</p>
            <p><strong>RAM:</strong> ${laptopData.ram}</p>
            <p><strong>Screen Size:</strong> ${laptopData.screen_size}</p>
            <p><strong>Internal Storage:</strong> ${
              laptopData.internal_storage
            }</p>
            <p><strong>Category:</strong> ${laptopData.category}</p>
            <p><strong>Price:</strong> IDR ${new Intl.NumberFormat(
              "id-ID"
            ).format(laptopData.price)}</p>
        </div>
    </div>
`;
    $("#laptopDetailsContent").html(detailsHtml);
    $("#laptopDetailsModal").modal("show");
  });

  // Handler untuk button remove
  $("#bookmarksTable").on("click", ".remove-bookmark", function () {
    const laptopId = $(this).data("laptop-id");

    Swal.fire({
      title: "Apakah kamu yakin?",
      text: "Kamu yakin ingin menghapus laptop ini dari bookmark ?",
      icon: "warning",
      showDenyButton: true,
      confirmButtonColor: "#3085d6",
      denyButtonColor: "#d33",
      confirmButtonText: "Ya",
      denyButtonText: "Tidak",
    }).then((result) => {
      if (result.isConfirmed) {
        $.ajax({
          url: "remove-bookmarks.php",
          method: "POST",
          data: { id_laptop: laptopId },
          dataType: "json",
          success: function (response) {
            if (response.success) {
              bookmarksTable.ajax.reload();
              Swal.fire({
                icon: "success",
                title: "Berhasil",
                text: "Laptop dihapus dari bookmark",
              });
            } else {
              Swal.fire({
                icon: "error",
                title: "Error",
                text: response.message,
              });
            }
          },
          error: function (xhr, status, error) {
            //console.error(xhr.responseText);
            Swal.fire({
              icon: "error",
              title: "Error",
              text: "Terjadi kesalahan ketika menghapus bookmark",
            });
          },
        });
      }
    });
  });
});
