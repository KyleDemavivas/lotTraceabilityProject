function getBoardData(data, source, onSuccess, onError) {
  if (typeof data === "string") {
    const formData = new FormData();
    formData.append("serial_code", data);
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

module.exports = { getBoardData };

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

module.exports = { submitScrap };
