let userBookmarks = [];

function fetchUserBookmarks() {
  $.ajax({
    url: "get-user-bookmarks.php",
    method: "GET",
    dataType: "json",
    success: function (response) {
      userBookmarks = response;
      // console.log('Fetched bookmarks:', userBookmarks);
    },
    error: function (xhr, status, error) {
      // console.error('Error fetching bookmarks:', error);
    },
  });
}

function getRecommendations() {
  const formData = new FormData();

  const selectedBrand = document.getElementById("brandSelect").value;
  if (selectedBrand) {
    formData.append("brand", selectedBrand);
  }

  const selectedCategory = document.getElementById("categorySelect").value;
  if (selectedCategory) {
    formData.append("category", selectedCategory);
  }

  formData.append("max_price", document.getElementById("priceRange").value);

  fetch("rekomendasi.php", {
    method: "POST",
    body: formData,
  })
    .then((response) => response.json())
    .then((data) => {
      const table = $("#recommendationsTable").DataTable();
      table.clear();

      if (data.length === 0) {
        table.row
          .add({
            brand_name: "Tidak ada laptop yang sesuai kriteria",
            model_name: "",
            price: "",
            image_url: "",
            id_laptop: null,
          })
          .draw();
      } else {
        table.rows.add(data).draw();
      }
    });
  // .catch(error => console.error('Error:', error));
}

$(document).ready(function () {
  fetchUserBookmarks();

  $.fn.dataTable.ext.type.order["price-pre"] = function (data) {
    return parseFloat(data.replace(/[^\d.-]/g, ""));
  };

  $("#recommendationsTable").DataTable({
    pageLength: 10,
    lengthMenu: [
      [5, 10, 25, 50, -1],
      [5, 10, 25, 50, "All"],
    ],
    columns: [
      {
        data: null,
        orderable: false,
        render: function (data, type, row) {
          return `<img src="${row.image_url}" alt="${row.brand_name} ${row.model_name}" style="max-width: 100px; height: auto;">`;
        },
      },
      { data: "brand_name" },
      { data: "model_name" },
      {
        data: "price",
        render: function (data, type, row) {
          return `IDR ${new Intl.NumberFormat("id-ID").format(data)}`;
        },
        type: "price",
      },
      {
        data: null,
        orderable: false,
        render: function (data, type, row) {
          const isBookmarked = userBookmarks.includes(row.id_laptop);
          const bookmarkButtonHtml = isBookmarked
            ? `<button class="btn btn-secondary btn-sm" disabled>Sudah dibookmark</button>`
            : `<button class="btn btn-secondary btn-sm bookmark-laptop" data-laptop-id="${row.id_laptop}">Bookmark</button>`;

          return `
            <button class="btn btn-primary btn-sm view-details mb-1" data-laptop='${JSON.stringify(
              row
            )}'>Lihat spesifikasi</button>
            <br>
            ${bookmarkButtonHtml}
          `;
        },
      },
    ],
  });

  // MENGAMBIL PILIHAN KATEGORI DAN BRAND
  fetch("get-filters.php")
    .then((response) => response.json())
    .then((data) => {
      // console.log(data);
      const brandSelect = document.getElementById("brandSelect");
      data.brands.forEach((brand) => {
        const option = document.createElement("option");
        option.value = brand;
        option.textContent = brand;
        brandSelect.appendChild(option);
      });

      const categorySelect = document.getElementById("categorySelect");
      data.categories.forEach((category) => {
        const option = document.createElement("option");
        option.value = category;
        option.textContent = category;
        categorySelect.appendChild(option);
      });
    });
  // .catch(error => console.error('Error:', error));

  // DISPLAY UPDATE HARGA
  function updatePriceDisplay() {
    const price = document.getElementById("priceRange").value;
    const formattedPrice = new Intl.NumberFormat("id-ID", {
      style: "currency",
      currency: "IDR",
    }).format(price);
    document.getElementById(
      "priceDisplay"
    ).textContent = `0 - ${formattedPrice}`;
  }

  document
    .getElementById("priceRange")
    .addEventListener("input", updatePriceDisplay);
  updatePriceDisplay(); // Initial call to set the default value

  // handler untuk button bookmark
  $(document).on("click", ".bookmark-laptop", function () {
    const laptopId = $(this).data("laptop-id");
    const button = $(this);
    $.ajax({
      url: "bookmarks.php",
      method: "POST",
      data: {
        id_laptop: laptopId,
        action: "add",
      },
      dataType: "json",
      success: function (response) {
        if (response.success) {
          Swal.fire({
            icon: "success",
            title: "Berhasil",
            text: "Laptop berhasil dibookmark",
          });
          button.text("Sudah dibookmark").prop("disabled", true);
          userBookmarks.push(laptopId);
        } else {
          Swal.fire({
            icon: "error",
            title: "Error",
            text: response.message,
          });
        }
      },
      error: function (xhr, status, error) {
        // console.error(xhr.responseText);
        Swal.fire({
          icon: "error",
          title: "Error",
          text: "Terjadi kesalahan",
        });
      },
    });
  });

  // handler untuk button view handler
  $("#recommendationsTable").on("click", ".view-details", function () {
    const laptopData = JSON.parse($(this).attr("data-laptop"));
    if (laptopData.id_laptop !== null) {
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
    } else {
      // Menampilkan pesan tidak ada laptop yang sesuai kriteria
      $("#laptopDetailsContent").html(
        "<p>Tidak ada laptop yang sesuai kriteria untuk menampilkan spesifikasi.</p>"
      );
      $("#laptopDetailsModal").modal("show");
    }
  });
});
