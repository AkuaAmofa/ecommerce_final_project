$(document).ready(function () {

  let uploadedImagePath = ""; // stores uploaded image path for DB
  let isEditMode = false;     // tracks if we're editing or adding

  // ------------------------------
  // IMAGE UPLOAD HANDLER
  // ------------------------------
  $("#product_image").on("change", function () {
    const fileData = this.files[0];
    if (!fileData) return;

    const formData = new FormData();
    formData.append("product_image", fileData);

    $.ajax({
      url: "../actions/upload_product_image_action.php",
      type: "POST",
      data: formData,
      contentType: false,
      processData: false,
      dataType: "json",
      beforeSend: function () {
        Swal.fire({
          title: "Uploading image...",
          text: "Please wait",
          allowOutsideClick: false,
          didOpen: () => Swal.showLoading()
        });
      },
      success: function (res) {
        Swal.close();
        if (res.status === "success") {
          uploadedImagePath = res.file_path;
          Swal.fire("Success", res.message, "success");
        } else {
          uploadedImagePath = "";
          Swal.fire("Error", res.message, "error");
        }
      },
      error: function (xhr) {
        Swal.close();
        Swal.fire("Error", "Image upload failed: " + xhr.responseText, "error");
      }
    });
  });

  // ------------------------------
  // FORM SUBMISSION (ADD / UPDATE)
  // ------------------------------
  $("#productForm").submit(function (e) {
    e.preventDefault();

    const product_id = $("#product_id").val();
    const cat_id = $("#product_cat").val();
    const brand_id = $("#product_brand").val();
    const title = $("#product_title").val().trim();
    const price = $("#product_price").val().trim();
    const desc = $("#product_desc").val().trim();
    const keywords = $("#product_keywords").val().trim();
    const location = $("#product_location").val().trim();
    const event_date = $("#event_date").val();
    const event_time = $("#event_time").val();

    // --- Basic Validation ---
    if (!title || !price || !cat_id || !brand_id) {
      Swal.fire("Error", "Please fill all required fields!", "error");
      return;
    }
    if (isNaN(price) || parseFloat(price) <= 0) {
      Swal.fire("Error", "Please enter a valid numeric price.", "error");
      return;
    }

    const formData = new FormData();
    formData.append("product_id", product_id);
    formData.append("product_cat", cat_id);
    formData.append("product_brand", brand_id);
    formData.append("product_title", title);
    formData.append("product_price", price);
    formData.append("product_desc", desc);
    formData.append("product_keywords", keywords);
    formData.append("product_image", uploadedImagePath);
    formData.append("product_location", location);
    formData.append("event_date", event_date);
    formData.append("event_time", event_time);

    const actionUrl = isEditMode
      ? "../actions/update_product_action.php"
      : "../actions/add_product_action.php";

    $.ajax({
      url: actionUrl,
      type: "POST",
      data: formData,
      contentType: false,
      processData: false,
      dataType: "json",
      beforeSend: function () {
        Swal.fire({
          title: isEditMode ? "Updating product..." : "Adding product...",
          text: "Please wait",
          allowOutsideClick: false,
          didOpen: () => Swal.showLoading()
        });
      },
      success: function (res) {
        Swal.close();
        if (res.status === "success") {
          Swal.fire("Success", res.message, "success").then(() => {
            window.location.reload();
          });
        } else {
          Swal.fire("Error", res.message, "error");
        }
      },
      error: function (xhr) {
        Swal.close();
        Swal.fire("Error", "Server error: " + xhr.responseText, "error");
      }
    });
  });

  // ------------------------------
  // EDIT BUTTON HANDLER
  // ------------------------------
  $(document).on("click", ".edit-btn", function () {
    const btn = $(this);
    $("#product_id").val(btn.data("id"));
    $("#product_title").val(btn.data("title"));
    $("#product_price").val(btn.data("price"));
    $("#product_desc").val(btn.data("desc"));
    $("#product_keywords").val(btn.data("keywords"));
    $("#product_cat").val(btn.data("cat"));
    $("#product_brand").val(btn.data("brand"));
    $("#product_location").val(btn.data("location"));
    $("#event_date").val(btn.data("date"));
    $("#event_time").val(btn.data("time"));

    $("#submitBtn").text("Update Product")
                   .removeClass("btn-primary")
                   .addClass("btn-warning");

    isEditMode = true;

    Swal.fire("Edit Mode", "You can now edit the product and click Update.", "info");
  });

  // ------------------------------
  // DELETE BUTTON HANDLER
  // ------------------------------
  $(document).on("click", ".delete-btn", function () {
    const productId = $(this).data("id");

    Swal.fire({
      title: "Are you sure?",
      text: "This product will be permanently deleted.",
      icon: "warning",
      showCancelButton: true,
      confirmButtonText: "Yes, delete it!",
      cancelButtonText: "Cancel"
    }).then((result) => {
      if (result.isConfirmed) {
        $.ajax({
          url: "../actions/delete_product_action.php",
          type: "POST",
          dataType: "json",
          data: { product_id: productId },
          success: function (res) {
            if (res.status === "success") {
              Swal.fire("Deleted!", res.message, "success").then(() => {
                window.location.reload();
              });
            } else {
              Swal.fire("Error", res.message, "error");
            }
          },
          error: function (xhr) {
            Swal.fire("Error", "Server error: " + xhr.responseText, "error");
          }
        });
      }
    });
  });
});
