function getBoardData(data, source, onSuccess, onError) {
  if (typeof data === "string") {
    const formData = new FormData();
    formData.append("qr_code", data);
    data = formData;
  }

  $.ajax({
    url: source,
    type: "POST",
    data: data,
    processData: false,
    contentType: false,
    dataType: "json",
    success: function (response) {
      if (typeof onSuccess === "function") {
        onSuccess(response);
      }
    },
    error: function (xhr, status, error) {
      if (typeof onError === "function") {
        onError(xhr, status, error);
      } else {
        Swal.fire({
          icon: "error",
          title: "Server Error",
          text: "An error occurred. Please try again.",
          toast: true,
          position: "top-right",
          timer: 3000,
          showConfirmButton: false,
        });
      }
    },
  });
}

function submitScrap(data, onSuccess, onError) {
  $.ajax({
    url: "scrapSubmit.php",
    data: data,
    type: "POST",
    processData: false,
    contentType: false,
    dataType: "json",
    success: function (response) {
      if (typeof onSuccess === "function") {
        onSuccess(response);
      }
    },
    error: function (xhr, status, error) {
      if (typeof onError === "function") {
        onError(xhr, status, error);
      }
    },
  });
}

function updateScrap(data, onSuccess, onError) {
  $.ajax({
    url: "scrapUpdate.php",
    data: data,
    type: "POST",
    processData: false,
    contentType: false,
    dataType: "json",
    success: function (response) {
      if (typeof onSuccess === "function") {
        onSuccess(response);
      }
    },
    error: function (xhr, status, error) {
      if (typeof onError === "function") {
        onError(xhr, status, error);
      }
    },
  });
}

function buildScrapData(
  qr_code,
  serial_code,
  response,
  location,
  process_location,
) {
  const scrapData = new FormData();
  scrapData.append("qr_code", qr_code);
  scrapData.append("model_name", response.model_name);
  scrapData.append("assy_code", response.assy_code);
  scrapData.append("kepi_lot", response.kepi_lot);
  scrapData.append("shift", response.shift);
  scrapData.append("line", response.line);
  scrapData.append("board_number", response.board_number);
  scrapData.append("serial_code", serial_code);
  scrapData.append("defect", defect);
  scrapData.append("operator_name", UserName);
  scrapData.append("board_number", "SCRAP");
  scrapData.append("location", location);
  scrapData.append("process_location", process_location);
  scrapData.append("repaired_by", "N/A");
  scrapData.append("action_rp", "N/A");
  scrapData.append("lcr_reading", "N/A");
  scrapData.append("parts_code", "N/A");
  scrapData.append("parts_lot", "N/A");
  scrapData.append("unitmeasurement", "N/A");
  scrapData.append("batchlot", "N/A");
  scrapData.append("repairable", "N/A");
  return scrapData;
}

function showSuccessToast(message) {
  Swal.fire({
    icon: "success",
    title: "Success!",
    text: message,
    toast: true,
    position: "top-right",
    timer: 3000,
    showConfirmButton: false,
  });
}

function showErrorToast(message) {
  Swal.fire({
    icon: "error",
    title: "Error",
    text: message,
    toast: true,
    position: "top-right",
    timer: 3000,
    showConfirmButton: false,
    didOpen: () => {
      $("#modal_serial_code").focus().select();
    },
  });
}

// module.exports = { getBoardData, submitScrap };
