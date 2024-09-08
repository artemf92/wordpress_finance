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