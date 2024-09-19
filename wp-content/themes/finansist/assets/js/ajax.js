jQuery(document).ready(function($) {
  // Запуск, закрытие, возобновление проекта
  $(document).on('click', '#project_start, #project_stop, #project_restart', function(e) {
    e.preventDefault()
    const _this = $(e.target)
    Fancybox.show([{
      // defaultType: this.dataset.once
      src: finajax.url+'?action=' + _this.attr('id') + '&project_id=' + _this.data('project'),
      type: _this.data('once')
    }],
    {
      on: {
        'loaded': function(fancybox) {
          setTimeout(() => {
            const status = $(fancybox.container).find('input').val()
            $('#project_status').html(status)
          }, 0);
          _this.remove()
        }
      }
    })
  })

  $(document).on('click', '#user_edit_profile', function(e) {
    e.preventDefault()
    const _this = $(e.target)
    const userID = _this.data('user')
    Fancybox.show([{
      // defaultType: this.dataset.once
      src: finajax.url+'?action=' + _this.attr('id') + '&user_id=' + userID,
      type: _this.data('once')
    }],
    {
      on: {
        'loaded': function(fancybox) {
          setTimeout(() => {
            // const status = $(fancybox.container).find('input').val()
            // $('#project_status').html(status)
          }, 0);
        }
      }
    })
  })
  
  // Доход проекта
  $(document).on('click', '#project_profit', function(e) {
    e.preventDefault()
    const _this = $(e.target)
    Fancybox.show([{
      // defaultType: this.dataset.once
      src: finajax.url+'?action=' + _this.attr('id') + '&project_id=' + _this.data('project'),
      type: _this.data('once')
    }],
    {
      on: {
        'loaded': function(fancybox) {
          setTimeout(() => {
            // const status = $(fancybox.container).find('input').val()
            // $('#project_status').html(status)
          }, 0);
        }
      }
    })
  })

  // Выплата участникам
  $(document).on('click', '#checkout-actions button', function(e) {
    e.preventDefault()
    const _this = $(e.target)
    Fancybox.show([{
      // defaultType: this.dataset.once
      src: finajax.url+'?action=' + _this.attr('id') + '&user_id=' + _this.data('user'),
      type: 'ajax'
    }],
    {
      on: {
        'loaded': function(fancybox) {
          setTimeout(() => {
            const str = $(fancybox.container).find('input').serialize()
            let keyValuePairs = str.split('&');

            let data = {};

            keyValuePairs.forEach(pair => {
                let [key, value] = pair.split('=');
                data[decodeURIComponent(key.replace('result%5B', '').replace('%5D', ''))] = decodeURIComponent(value);
            });
            console.log(data)

            populateUserFields(data)
          }, 0);
        }
      }
    })
  })

  $(document).on('submit reset', '.form_export_transactions', function(e) {
    e.preventDefault()
    const type = e.type
    const form = $(this)
    const btnFilter = form.find('button[type=submit]')
    const ajax_result = form.parent().find('.ajax-result')
    const btnExport = form.find('#export_transactions')
    let data = form.serialize()
    let url = new URLSearchParams(data)

    if (type == 'reset') {
      data = url = []
    }

    btnFilter.attr('disabled', 'true')
    $.ajax({
      url: finajax.url+'?action=transactions_ajax_filter',
      data,
      method: 'POST',
      dataType: 'html',
      beforeSend: function(xhr) {
        ajax_result.addClass('loading')

        history.pushState('', '', '?'+url.toString())
      },
      success: function(res) {
        if (res) {
          const result = $(res).find('.ajax-result')
          const newExpBtn = $(res).find('#export_transactions')
          
          ajax_result.html('')
          result.appendTo(ajax_result)

          console.log(newExpBtn)

          btnExport.data('args', newExpBtn.data('args'))
        }
      },
      error: function(err) {
        Fancybox.show(['<h4 class="p-5">Упс! Что-то пошло не так</h4>'])
        setTimeout(() => {
          Fancybox.close()
        }, 1500);

        throw new Error(err);
      },
      complete: function() {
        btnFilter.removeAttr('disabled')
        ajax_result.removeClass('loading')
      }
    })
  })

  $(document).on('click', '#export_transactions', function(e) {
    e.preventDefault()
    const _this = $(this)
    _this.attr('disabled', 'true')
    $.ajax({
      url: finajax.url+'?action=' + _this.attr('id') + '&' + _this.data('args'),
      method: 'POST',
      dataType: 'json',
      success: function(data) {
        if (data.success) {
          const link = document.createElement('a')
          link.href = data.msg
          link.innerHTML = 'Скачать'
          link.download = 'Экспорт транзакций'
          link.classList.add('hidden')

          _this.after(link)
          setTimeout(() => {
            _this.next().get(0).click()

            _this.removeAttr('disabled')
          }, 500);
        }
      },
      error: function(err) {
        Fancybox.show(['<h4 class="p-5">Упс! Что-то пошло не так</h4>'])
        setTimeout(() => {
          Fancybox.close()
        }, 1500);

        throw new Error(err);
      }
    })
  })

})

function projectProfitHandler(data) {
  Fancybox.show([{
    src: finajax.url+'?action=project_profit_final&' + data,
    type: 'ajax'
  }],
  {
    on: {
      'loaded': function(fancybox) {
        setTimeout(() => {
          // const status = $(fancybox.container).find('input').val()
          // $('#project_status').html(status)
        }, 0);
      }
    }
  })
}
function checkoutPartiallyHandler(data) {
  Fancybox.show([{
    src: finajax.url+'?action=checkout_partial_calc&' + data,
    type: 'ajax'
  }],
  {
    on: {
      'loaded': function(fancybox) {
        setTimeout(() => {
          const str = jQuery(fancybox.container).find('input').serialize()
          let keyValuePairs = str.split('&');

          let data = {};

          keyValuePairs.forEach(pair => {
              let [key, value] = pair.split('=');
              data[decodeURIComponent(key.replace('result%5B', '').replace('%5D', ''))] = decodeURIComponent(value);
          });

          populateUserFields(data)
        }, 0);
      }
    }
  })
}

function populateUserFields(data) {
  let changed = false
  for(let key in data) {
    const value = Number(data[key]) ? (Number(data[key]).toLocaleString('ru-RU', {style:'currency', currency: 'RUB'})).replace(',', '.'): '0.00'
    const el = jQuery('.item[data-field="'+key+'"] .field__item')

    if (el.length && el.html() != value) {
      jQuery('.item[data-field="'+key+'"] .field__item').html(value)
      changed = true
    }
  }

  setTimeout(() => {
    if (typeof Fancybox != 'undefined' && changed)
      Fancybox.close()
  }, 1000);
}