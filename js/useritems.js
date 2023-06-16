let itemService = {
  init: function () {
    itemService.list()
    let minDate = new Date(new Date().getTime() + 3600000)
    const tzoffset = (minDate).getTimezoneOffset() * 60000
    minDate = new Date((new Date().getTime() + 1000000) - tzoffset)
    $('#start').attr('min', new Date(minDate).toISOString().slice(0, 16))
    $('#start').val((minDate.toISOString().slice(0, 16)))
    $('#itemadd').validate({
      rules: {
        title: {
          required: true
        },
        description: {
          minlength: 30,
          maxlength: 255,
          required: true
        },
        date: {
          required: true,
          dateISO: true
        },
        imageInput: {
          required: true,
          accept: 'image/*'
        }
      },
      messages:
        {
          imageInput: 'Only images are accepted'
        },
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
        const item = new FormData(form)
        itemService.addItem(item)
      }
    })
  },

  list: function () {
    $.ajax({
      url: 'rest/useritems',
      type: 'GET',
      beforeSend: function (xhr) {
        xhr.setRequestHeader('Authorization', localStorage.getItem('token'))
      },
      success: function (data) {
        $('#item-list').html('')
        let html = ''
        for (let i = 0; i < data.length; i++) {
          const currentDate = new Date().getTime()
          const endingDate = new Date(data[i].ending).getTime()
          const diff = Math.floor((endingDate - currentDate) / msDay)
          let toShow = 'Ended'
          let color = ''
          if (diff < 0) {
            color = 'rgb(0, 0, 0)'
          } else if (diff > 0) {
            toShow = diff + ' days left'
            color = 'rgb(15, 99, 30)'
          } else {
            const hours = Math.floor(((endingDate - currentDate) % msDay) / msHours)
            if (hours > 0) {
              toShow = hours + ' hours left'
              color = 'rgb(255,127,80)'
            } else {
              const mins = Math.floor(((endingDate - currentDate) % msDay) / msMinute)
              if (mins > 0) {
                toShow = mins + ' minutes left'
              } else {
                const seconds = Math.floor(((endingDate - currentDate) % 60000) / 1000)
                toShow = seconds + ' seconds left'
              }
            }
          }
          html += `
                <div class="col-md-6 col-lg-4 col-xl-3" data-toggle="modal" data-target="#exampleModalCenter" onclick="itemService.get(` + data[i].id + `)">
                <div id="product-2" class="single-product my-2">
                        <div class="part-1" id="itemImage` + itemNo + `">
                        <style>
                        #itemImage` + itemNo + `::before{
                            background: url("img/items/` + data[i].image + `") no-repeat center;
                            background-size: contain;
                            transition: all 0.3s;
                        }
                        </style>
                          <span class="discount" id="date` + itemNo + '" style="background-color: ' + color + '" datetime="' + data[i].ending + '">' + toShow + `</span>
                        </div>
                        <div class="part-2">
                                <h3 class="product-title">` + data[i].title + `</h3>
                                <h4 class="product-price" id="highestBid` + itemNo + '" value="' + data[i].id + `"><div class="spinner-border" role="status"></div></h4>
                        </div>
                    </div>
                </div>
                `
          itemNo++
        }
        $('#item-list').html(html)
      },
      error: function (XMLHttpRequest, textStatus, errorThrown) {
        toastr.error(XMLHttpRequest.responseJSON.message)
        userService.logout()
      }
    })
  },

  get: function (id) {
    $.ajax({
      url: 'rest/items/' + id,
      type: 'GET',
      beforeSend: function (xhr) {
        xhr.setRequestHeader('Authorization', localStorage.getItem('token'))
      },
      success: function (data) {
        $('#dataTitle').html(data.title)
        $('#modalDate').attr('dateTime', data.ending)
        $('#modalText').attr('value', data.id)
        modalEnding()
        setInterval(function () {
          modalEnding()
        }, 1000)
        $('#dataImage').css('background-image', "url('img/items/" + data.image + "')")
        $('#dataDescription').html(data.description)
        $('#exampleModalCenter').modal('show')
        $('#delete').attr('onClick', 'itemService.delete(' + id + ')')
      },
      error: function (XMLHttpRequest, textStatus, errorThrown) {
        toastr.error(XMLHttpRequest.responseJSON.message)
        userService.logout()
      }
    })
  },
  addItem: function (note) {
    $.ajax({
      url: 'rest/item',
      type: 'POST',
      beforeSend: function (xhr) {
        xhr.setRequestHeader('Authorization', localStorage.getItem('token'))
      },
      data: note,
      success: function (data) {
        itemService.list()
        $('#addItemModal').modal('hide')
        toastr.success('Item added!')
      },
      cache: false,
      processData: false,
      contentType: false,
      error: function (XMLHttpRequest, textStatus, errorThrown) {
        toastr.error(XMLHttpRequest.responseJSON.message)
      }
    })
  },
  add: function () {
    $('#addItemModal').modal('show')
  },

  delete: function (id) {
    $.ajax({
      url: 'rest/item/' + id,
      beforeSend: function (xhr) {
        xhr.setRequestHeader('Authorization', localStorage.getItem('token'))
      },
      type: 'DELETE',
      success: function (result) {
        $('#exampleModalCenter').modal('hide')
        itemService.list()
        toastr.success('Item deleted!')
      }
    })
  }

}
let itemNo = 0
const msDay = 60 * 60 * 24 * 1000
const msHours = 60 * 60 * 1000
const msMinute = 60 * 1000
function checkItems () {
  for (let i = 0; i < itemNo; i++) {
    $.ajax({
      url: 'rest/bids/' + $('#highestBid' + i).attr('value'),
      type: 'GET',
      beforeSend: function (xhr) {
        xhr.setRequestHeader('Authorization', localStorage.getItem('token'))
      },
      success: function (data) {
        if (!$.trim(data)) {
          $('#highestBid' + i).html('No bids!')
        } else {
          $('#highestBid' + i).html('Current highest bid ' + data[0].amount + 'BAM')
        }
      },
      error: function (XMLHttpRequest, textStatus, errorThrown) {
        toastr.error(XMLHttpRequest.responseJSON.message)
        userService.logout()
      }
    })
    const currentDate = new Date().getTime()
    const endingDate = new Date($('#date' + i).attr('datetime')).getTime()
    const diff = Math.floor((endingDate - currentDate) / msDay)
    if (diff < 0) {
      $('#date' + i).html('Ended')
      continue
    } else if (diff > 0) {
      $('#date' + i).html(diff + ' days left')
      $('#date' + i).css('background-color', 'rgb(15, 99, 30)')
      continue
    } else {
      const hours = Math.floor(((endingDate - currentDate) % msDay) / msHours)
      if (hours > 0) {
        $('#date' + i).html(hours + ' hours left')
        $('#date' + i).css('background-color', 'rgb(255,127,80)')
        continue
      } else {
        const mins = Math.floor(((endingDate - currentDate) % msDay) / msMinute)
        if (mins > 0) {
          $('#date' + i).html(mins + ' minutes left')
          continue
        } else {
          const seconds = Math.floor(((endingDate - currentDate) % 60000) / 1000)
          $('#date' + i).html(seconds + ' seconds left')
        }
      }
    }
  }
}
setInterval(function () {
  checkItems()
}, 1000)

function modalEnding () {
  $.ajax({
    url: 'rest/bids/' + $('#modalText').attr('value'),
    type: 'GET',
    beforeSend: function (xhr) {
      xhr.setRequestHeader('Authorization', localStorage.getItem('token'))
    },
    success: function (data) {
      $('#modalText').html('')
      if (!$.trim(data)) {
        $('#modalText').html('No bids!')
      } else {
        $('#modalText').html('Current highest bid ' + data[0].amount + 'BAM')
      }
    },
    error: function (XMLHttpRequest, textStatus, errorThrown) {
      toastr.error(XMLHttpRequest.responseJSON.message)
      userService.logout()
    }
  })
  const currentDate = new Date().getTime()
  const endingDate = new Date($('#modalDate').attr('datetime')).getTime()
  const diff = Math.floor((endingDate - currentDate) / msDay)
  if (diff < 0) {
    $('#modalDate').html('Ended')
    $('#modalDate').css('background-color', 'rgb(0, 0, 0)')
  } else if (diff > 0) {
    $('#modalDate').html(diff + ' days left')
    $('#modalDate').css('background-color', 'rgb(15, 99, 30)')
  } else {
    const hours = Math.floor(((endingDate - currentDate) % msDay) / msHours)
    if (hours > 0) {
      $('#modalDate').html(hours + ' hours left')
      $('#modalDate').css('background-color', 'rgb(255,127,80)')
    } else {
      const mins = Math.floor(((endingDate - currentDate) % msDay) / msMinute)
      if (mins > 0) {
        $('#modalDate').html(mins + ' minutes left')
      } else {
        const seconds = Math.floor(((endingDate - currentDate) % 60000) / 1000)
        $('#modalDate').html(seconds + ' seconds left')
      }
    }
  }
}
