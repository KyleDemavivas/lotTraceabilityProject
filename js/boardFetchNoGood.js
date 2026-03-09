function getBoardData(data, source, onSuccess, onError) {
    $.ajax({
        url: source,
        type: 'POST',
        data: data,
        processData: false,
        contentType: false,
        dataType: 'json',
        success: function(response) {
            if (typeof onSuccess === 'function') {
                onSuccess(response);
            }
        },
        error: function(xhr, status, error) {
            if (typeof onError === 'function') {
                onError(xhr, status, error);
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Server Error',
                    text: 'An error occurred. Please try again.',
                    toast: true,
                    position: 'top-right',
                    timer: 3000,
                    showConfirmButton: false
                });
            }
        }
    });
};

function submitScrap(data, onSuccess, onError) {
    $.ajax({
        url: 'scrapSubmit.php',
        data: data,
        type: 'POST',
        processData: false,
        contentType: false,
        dataType: 'json',
        success: function(response) {
            if(typeof onSuccess === 'function') {
                onSuccess(response);
            }
        },
        error: function(xhr, status, error) {
            if(typeof onError === 'function') {
                onError(xhr, status, error);
            }
        }
    })
}