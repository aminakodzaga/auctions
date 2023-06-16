let userService = {
  init: function () {
    $('#registerForm').validate({
      rules: {
        username: {
          minlength: 4,
          required: true,
          maxlength: 32
          // uniqueUsername: true
        },
        fname: {
          minlength: 4,
          required: true,
          maxlength: 32
        },
        sname: {
          minlength: 4,
          required: true,
          maxlength: 32
        },
        email: {
          required: true
          // uniqueEmail: true
        },
        password: {
          minlength: 6,
          maxlength: 30,
          required: true
        }
      },
      errorElement: 'div',
      errorPlacement: function (error, element) {
        error.addClass('invalid-feedback')
        error.insertAfter(element)
      },
      highlight: function (element) {
        $(element).removeClass('is-valid').addClass('is-invalid')
      },
      unhighlight: function (element) {
        $(element).removeClass('is-invalid').addClass('is-valid')
      },
      submitHandler: function (form) {
        $('#submit').valid()
        const user = Object.fromEntries((new FormData(form)).entries())
        userService.add(user)
      }
    })
    $('#signinForm').validate({
      rules: {
        email: {
          email: true
        },
        password: {
          minlength: 2,
          maxlength: 30,
          required: true
        }
      },
      errorElement: 'div',
      errorPlacement: function (error, element) {
        error.addClass('invalid-feedback')
        error.insertAfter(element)
      },
      highlight: function (element) {
        $(element).removeClass('is-valid').addClass('is-invalid')
      },
      unhighlight: function (element) {
        $(element).removeClass('is-invalid').addClass('is-valid')
      },
      submitHandler: function (form) {
        const user = Object.fromEntries((new FormData(form)).entries())
        userService.auth(user)
      }
    })
  },
  auth: function (userData) {
    $.ajax({
      url: 'rest/login',
      type: 'POST',
      data: JSON.stringify(userData),
      contentType: 'application/json',
      dataType: 'json',
      success: function (result) {
        localStorage.setItem('token', result.token)
        window.location.replace('index.html')
      },
      error: function (XMLHttpRequest, textStatus, errorThrown) {
        toastr.error(XMLHttpRequest.responseJSON.message)
      }
    })
  },

  add: function (user) {
    $.ajax({
      url: 'rest/register',
      type: 'POST',
      data: JSON.stringify(user),
      contentType: 'application/json',
      dataType: 'json',
      success: function (result) {
        toastr.success('Registered!')
        $('#register').modal('hide')
      },
      error: function (XMLHttpRequest, textStatus, errorThrown) {
        toastr.error(XMLHttpRequest.responseJSON.message)
      }
    })
  },

  logout: function () {
    localStorage.clear()
    window.location.replace('login.html')
  }
}
